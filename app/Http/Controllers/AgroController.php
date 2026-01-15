<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OpenAIService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class AgroController extends Controller
{
    private $perenualKey;
    private $perenualBase;

    public function __construct()
    {
        // Prefer config('services.perenual') but fall back to env for compatibility
        $this->perenualKey = config('services.perenual.key') ?: env('PERENUAL_API_KEY');
        $this->perenualBase = config('services.perenual.base', 'https://perenual.com/api');
    }

    /**
     * Perform a GET request against the Perenual API with retries, backoff and optional caching.
     * Returns an array with keys: success (bool), status (int), data (array|null), body (string|null)
     */
    private function perenualRequest(string $path, array $query = [], ?string $cacheKey = null, ?int $cacheHours = null)
    {
        // If cache exists return it
        if ($cacheKey && Cache::has($cacheKey)) {
            return ['success' => true, 'status' => 200, 'data' => Cache::get($cacheKey)];
        }

        if (! $this->perenualKey) {
            \Log::error('Perenual API key not configured');
            return ['success' => false, 'status' => 500, 'body' => 'Perenual API key not configured'];
        }

        $maxRetries = 3;
        $lastResponse = null;

        for ($attempt = 0; $attempt < $maxRetries; $attempt++) {
            try {
                $response = Http::withoutVerifying()->timeout(15)
                    ->get($this->perenualBase . $path, array_merge($query, ['key' => $this->perenualKey]));

                $lastResponse = $response;

                // Success
                if ($response->successful()) {
                    $json = $response->json();
                    // Cache when requested
                    if ($cacheKey && $cacheHours) {
                        Cache::put($cacheKey, $json, now()->addHours($cacheHours));
                    }
                    return ['success' => true, 'status' => $response->status(), 'data' => $json];
                }

                $status = $response->status();

                // Handle rate limit: obey Retry-After header if present
                if ($status === 429) {
                    $retryAfter = $response->header('Retry-After');
                    $wait = is_numeric($retryAfter) ? (int) $retryAfter : (int) pow(2, $attempt);
                    \Log::warning('Perenual rate limited, retrying', ['attempt' => $attempt, 'retry_after' => $wait]);
                    sleep(max(1, $wait));
                    continue;
                }

                // For server errors try again with exponential backoff
                if ($status >= 500) {
                    $wait = (int) pow(2, $attempt);
                    \Log::warning('Perenual server error, retrying', ['status' => $status, 'attempt' => $attempt, 'wait' => $wait]);
                    sleep(max(1, $wait));
                    continue;
                }

                // Client errors (4xx other than 429) - return immediately
                return ['success' => false, 'status' => $status, 'body' => $response->body()];
            } catch (\Exception $e) {
                \Log::error('Perenual request exception', ['message' => $e->getMessage(), 'attempt' => $attempt]);
                // Wait a bit before retrying
                sleep((int) pow(2, $attempt));
                continue;
            }
        }

        // After retries
        if ($lastResponse) {
            return ['success' => false, 'status' => $lastResponse->status(), 'body' => $lastResponse->body()];
        }

        return ['success' => false, 'status' => 500, 'body' => 'Unknown error'];
    }

    public function index()
    {
        return view('user.agro');
    }

    public function getPlants(Request $request)
    {
        $page = $request->query('page', 1);
        $cacheKey = "perenual.plants.page.{$page}";

        try {
            $result = $this->perenualRequest('/v2/species-list', ['page' => $page], $cacheKey, 12);

            if (! $result['success']) {
                \Log::error('Perenual Plants fetch failed', ['status' => $result['status'], 'body' => $result['body'] ?? null]);
                $cached = Cache::get($cacheKey);
                if ($cached) {
                    return response()->json($cached);
                }

                if ($result['status'] === 429) {
                    return response()->json(['error' => 'Perenual API rate limit exceeded', 'status' => 429], 429);
                }

                return response()->json(['error' => 'Failed to fetch plants from API', 'status' => $result['status']], 500);
            }

            $data = $result['data'];
            if (!isset($data['data'])) {
                \Log::warning('Unexpected Perenual response structure', ['data' => $data]);
                $cached = Cache::get($cacheKey);
                if ($cached) {
                    return response()->json($cached);
                }
                return response()->json(['error' => 'Invalid API response structure'], 500);
            }

            $plants = collect($data['data'] ?? [])
                ->map(function($plant) {
                    $id = $plant['id'] ?? null;
                    $name = $plant['common_name'] ?? null;

                    // scientific_name can be array or string
                    $scientific_name = '';
                    if (isset($plant['scientific_name'])) {
                        if (is_array($plant['scientific_name'])) {
                            $scientific_name = $plant['scientific_name'][0] ?? '';
                        } else {
                            $scientific_name = $plant['scientific_name'];
                        }
                    }

                    $cycle = $plant['cycle'] ?? 'Unknown';
                    $watering = $plant['watering'] ?? 'Moderate';

                    // sunlight may be missing, string, or array
                    $sunlight = 'Partial Shade';
                    if (array_key_exists('sunlight', $plant)) {
                        if (is_array($plant['sunlight'])) {
                            $sunlight = $plant['sunlight'][0] ?? $sunlight;
                        } elseif (!empty($plant['sunlight'])) {
                            $sunlight = $plant['sunlight'];
                        }
                    }

                    $image = data_get($plant, 'default_image.original_url');

                    return [
                        'id' => $id,
                        'name' => $name ?? ($scientific_name ?: 'Unknown'),
                        'scientific_name' => $scientific_name,
                        'cycle' => $cycle,
                        'watering' => $watering,
                        'sunlight' => $sunlight,
                        'image' => $image,
                    ];
                })
                ->filter(fn($p) => !empty($p['name']))
                ->values();

            // Cache for 12 hours
            Cache::put($cacheKey, $plants, now()->addHours(12));

            return response()->json($plants);
        } catch (\Exception $e) {
            \Log::error('Get Plants Exception', ['error' => $e->getMessage()]);
            $cached = Cache::get($cacheKey);
            if ($cached) {
                return response()->json($cached);
            }
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getPlantDetails(Request $request, $id)
    {
        $cacheKey = "perenual.plant.{$id}";
        try {
            $result = $this->perenualRequest('/v2/species/details/' . $id, [], $cacheKey, 24);

            if (! $result['success']) {
                \Log::error('Perenual Plant details fetch failed', ['id' => $id, 'status' => $result['status'], 'body' => $result['body'] ?? null]);
                if ($result['status'] === 429) {
                    return response()->json(['error' => 'Perenual API rate limit exceeded', 'status' => 429], 429);
                }

                if ($result['status'] === 404) {
                    return response()->json(['error' => 'Plant not found'], 404);
                }

                $cached = Cache::get($cacheKey);
                if ($cached) {
                    return response()->json($cached);
                }

                return response()->json(['error' => 'Failed to fetch plant details', 'status' => $result['status']], 500);
            }

            $data = $result['data'];
            if (!isset($data['data'])) {
                \Log::warning('Unexpected Perenual plant details structure', ['id' => $id, 'data' => $data]);
                $cached = Cache::get($cacheKey);
                if ($cached) {
                    return response()->json($cached);
                }
                return response()->json(['error' => 'Invalid API response structure'], 500);
            }

            return response()->json($data['data']);
        } catch (\Exception $e) {
            \Log::error('Get Plant Details Exception', ['error' => $e->getMessage(), 'id' => $id]);
            $cached = Cache::get($cacheKey);
            if ($cached) {
                return response()->json($cached);
            }
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getDiseases()
    {
        $cacheKey = 'perenual.diseases';
        try {
            $result = $this->perenualRequest('/pest-disease-list', [], $cacheKey, 24);

            if (! $result['success']) {
                \Log::error('Perenual Diseases fetch failed', ['status' => $result['status'], 'body' => $result['body'] ?? null]);

                if ($result['status'] === 429) {
                    return response()->json(['error' => 'Perenual API rate limit exceeded', 'status' => 429], 429);
                }

                $cached = Cache::get($cacheKey);
                if ($cached) {
                    return response()->json($cached);
                }

                return response()->json(['error' => 'Failed to fetch diseases from API', 'status' => $result['status']], 500);
            }

            $data = $result['data'];
            if (!isset($data['data'])) {
                \Log::warning('Unexpected Perenual diseases response structure', ['data' => $data]);
                $cached = Cache::get($cacheKey);
                if ($cached) {
                    return response()->json($cached);
                }
                return response()->json(['error' => 'Invalid API response structure'], 500);
            }

            $diseases = collect($data['data'] ?? [])
                ->map(fn($disease) => [
                    'id' => $disease['id'],
                    'name' => $disease['common_name'] ?? 'Unknown',
                    'scientific_name' => $disease['scientific_name'] ?? '',
                    'family' => $disease['family'] ?? '',
                    'description' => $disease['description'] ?? [],
                    'images' => $disease['images'] ?? [],
                ])
                ->values();

            return response()->json($diseases);
        } catch (\Exception $e) {
            \Log::error('Get Diseases Exception', ['error' => $e->getMessage()]);
            $cached = Cache::get($cacheKey);
            if ($cached) {
                return response()->json($cached);
            }
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function analyze(Request $request)
    {
        $validated = $request->validate([
            'location' => 'required|string|min:2',
            'crop' => 'required|string|min:2',
        ]);

        try {
            $data = OpenAIService::analyzeAgriLocation(
                $validated['location'],
                $validated['crop']
            );
            return response()->json($data);
        } catch (\Exception $e) {
            \Log::error('Agro analyze exception', ['error' => $e->getMessage(), 'exception' => $e]);
            return response()->json(['error' => 'Failed to analyze location at this time'], 500);
        }
    }

    public function translatePlant(Request $request)
    {
        $name = $request->query('name');
        if (!$name) {
            return response()->json(['error' => 'Missing name parameter'], 400);
        }

        try {
            $data = OpenAIService::translatePlantData($name);
            return response()->json($data);
        } catch (\Exception $e) {
            \Log::error('Translate Plant Exception', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
