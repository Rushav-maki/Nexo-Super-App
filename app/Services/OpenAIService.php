<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use OpenAI\Client;
use OpenAI\Factory;

class OpenAIService
{
    private static function getClient(): Client
    {
        $apiKey = config('openai.api_key');
        if (empty($apiKey)) {
            throw new \RuntimeException('OpenAI API key not configured');
        }

        return (new Factory())
            ->withApiKey($apiKey)
            ->withHttpClient(new \GuzzleHttp\Client([
                'verify' => config('openai.verify', false),
                'timeout' => config('openai.request_timeout', 30),
            ]))
            ->make();
    }

    /**
     * Wrapper for chat calls that handles errors, rate-limits and returns the content string or null
     */
    private static function callChat(array $messages, string $model = 'gpt-3.5-turbo', float $temperature = 0.7, int $max_tokens = 800): ?string
    {
        try {
            // Log masked api key used by this process to help identify mismatched env vars between CLI and webserver
            try {
                $apiKeyForLog = config('openai.api_key');
                if (!empty($apiKeyForLog)) {
                    $len = strlen($apiKeyForLog);
                    $masked = $len > 10 ? substr($apiKeyForLog, 0, 6) . str_repeat('*', max(0, $len - 10)) . substr($apiKeyForLog, -4) : $apiKeyForLog;
                    \Log::info('OpenAI SDK call using api key', ['key_masked' => $masked]);
                } else {
                    \Log::warning('OpenAI SDK call: no api key configured');
                }
            } catch (\Throwable $t) {
                // ignore logging failure
            }

            $client = self::getClient();

            // Temporarily convert PHP warnings/notices into ErrorException so they can be caught and handled.
            $prevHandler = set_error_handler(function ($severity, $message, $file, $line) {
                throw new \ErrorException($message, 0, $severity, $file, $line);
            });

            try {
                $response = $client->chat()->create([
                    'model' => $model,
                    'messages' => $messages,
                    'temperature' => $temperature,
                    'max_tokens' => $max_tokens,
                ]);
            } finally {
                // Restore previous error handler regardless of success/failure
                if ($prevHandler !== null) {
                    restore_error_handler();
                }
            }

            // Try to read content safely
            if (isset($response->choices) && is_array($response->choices) && isset($response->choices[0]->message->content)) {
                return $response->choices[0]->message->content;
            }

            // If SDK returned an unexpected structure, attempt to log the raw response for debugging
            try {
                if (is_callable([$response, 'getRawResponse'])) {
                    $raw = $response->getRawResponse();
                    \Log::warning('OpenAI response missing choices', ['raw' => $raw]);
                    return $raw['choices'][0]['message']['content'] ?? null;
                }
            } catch (\Throwable $t) {
                \Log::warning('Failed to get raw response from OpenAI SDK', ['error' => $t->getMessage()]);
            }

            return null;
        } catch (\OpenAI\Exceptions\RateLimitException $e) {
            \Log::error('OpenAI rate limit: ' . $e->getMessage(), ['exception' => $e]);
            // As a fallback, try the raw HTTP call to capture body/status
            return self::callChatRaw($messages, $model, $temperature, $max_tokens);
        } catch (\Throwable $e) {
            // Catch any ErrorException or other Throwable and attempt raw HTTP call for better diagnostics
            \Log::error('OpenAI chat error: ' . $e->getMessage(), ['exception' => $e]);
            return self::callChatRaw($messages, $model, $temperature, $max_tokens);
        }
    }

    /**
     * Public wrapper around callChat for other services to use safely.
     */
    public static function simpleChat(array $messages, string $model = 'gpt-3.5-turbo', float $temperature = 0.7, int $max_tokens = 800): ?string
    {
        return self::callChat($messages, $model, $temperature, $max_tokens);
    }

    /**
     * Raw HTTP fallback to capture OpenAI responses when SDK fails or parsing errors occur.
     */
    private static function callChatRaw(array $messages, string $model = 'gpt-3.5-turbo', float $temperature = 0.7, int $max_tokens = 800): ?string
    {
        try {
            $apiKey = config('openai.api_key');
            if (empty($apiKey)) {
                \Log::error('OpenAI raw call failed: API key not configured');
                return null;
            }

            // Masked key logging for debugging which process is using which key
            try {
                $len = strlen($apiKey);
                $masked = $len > 10 ? substr($apiKey, 0, 6) . str_repeat('*', max(0, $len - 10)) . substr($apiKey, -4) : $apiKey;
                \Log::info('OpenAI raw call using api key', ['key_masked' => $masked]);
            } catch (\Throwable $t) {
                // ignore logging failures
            }

            $client = new \GuzzleHttp\Client([
                'verify' => config('openai.verify', false),
                'timeout' => config('openai.request_timeout', 30),
            ]);

            $resp = $client->post('https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'model' => $model,
                    'messages' => $messages,
                    'temperature' => $temperature,
                    'max_tokens' => $max_tokens,
                ],
            ]);

            $status = $resp->getStatusCode();
            $body = (string) $resp->getBody();

            \Log::warning('OpenAI raw HTTP response', ['status' => $status, 'body' => substr($body, 0, 2000)]);

            $decoded = json_decode($body, true);
            if (is_array($decoded) && isset($decoded['choices'][0]['message']['content'])) {
                return $decoded['choices'][0]['message']['content'];
            }

            return null;
        } catch (\GuzzleHttp\Exception\RequestException $re) {
            $response = $re->getResponse();
            $body = $response ? (string) $response->getBody() : $re->getMessage();
            \Log::error('OpenAI raw HTTP error', ['message' => $re->getMessage(), 'body' => substr($body, 0, 2000)]);
            return null;
        } catch (\Exception $e) {
            \Log::error('OpenAI raw HTTP exception: ' . $e->getMessage(), ['exception' => $e]);
            return null;
        }
    }

    public static function generateItinerary(string $destination, string $budget, int $days)
    {
        // Professional prompt
        $prompt = "You are a professional travel planner. Create a detailed $days-day travel itinerary for $destination for a $budget budget.
    Each day should start with arrival or breakfast depending on the day, and divide the activities into: morning, afternoon, evening, and night.
    Each activity should be realistic and suitable for the destination and use longer descriptions not shorter ones(at least 15 words per activity).
    Include realistic daily budget in Nepali Rupees (Rs).
    Output the itinerary in the following strict JSON format:

    {
        \"plan\": [
            {
                \"day\": 1,
                \"desc\": [
                    \"Morning: ...\",
                    \"Afternoon: ...\",
                    \"Evening: ...\",
                    \"Night: ...\"
                ],
                \"budget\": \"Approx. XXXX Rs\"
            },
            ...
        ]
    }";

        $content = self::callChat([
            ['role' => 'user', 'content' => $prompt]
        ], 'gpt-3.5-turbo', 0.7, 1200);

        if (!$content) {
            return ['plan' => []];
        }

        $decoded = json_decode($content, true);
        if (is_array($decoded) && isset($decoded['plan'])) {
            return $decoded;
        }

        // If content is a JSON array of days, normalize
        if (is_array($decoded) && !isset($decoded['plan'])) {
            return ['plan' => $decoded];
        }

        // Final fallback: attempt to parse or return empty plan
        return ['plan' => []];
    }


    public static function searchHotels(string $destination)
    {
        $prompt = "List 5 luxury/mid-range hotels in $destination with realistic details. Return as JSON array with objects containing: name (hotel name), rating (4.0-5.0), location (area in city), amenities (array of 3-5 amenities), pricePerNight (realistic daily rate in Nepali Rupees, typically 2000-15000 range). Make prices realistic for Nepal tourism.";

        $content = self::callChat([
            ['role' => 'user', 'content' => $prompt]
        ], 'gpt-3.5-turbo', 0.7, 600);

        $result = $content ? json_decode($content, true) : null;

        if (!$result || !is_array($result)) {
            return [
                ['name' => 'Hotel Himalaya', 'rating' => 4.5, 'location' => 'Thamel', 'amenities' => ['WiFi', 'Restaurant', 'Spa'], 'pricePerNight' => 3500],
                ['name' => 'Kathmandu Guest House', 'rating' => 4.2, 'location' => 'Thamel', 'amenities' => ['WiFi', 'Breakfast', 'Gym'], 'pricePerNight' => 2800],
            ];
        }

        return $result;
    }

    public static function searchCars(string $destination)
    {
        $prompt = "List 5 cars available for rent in $destination with name, type, price per day, specs, and image URL in JSON format.";

        $content = self::callChat([
            ['role' => 'user', 'content' => $prompt]
        ], 'gpt-3.5-turbo', 0.7, 600);

        $decoded = $content ? json_decode($content, true) : null;
        return is_array($decoded) ? $decoded : [];
    }

    public static function analyzeSymptoms(string $symptoms): array
    {
        $prompt = "Based on these symptoms: '$symptoms', provide a JSON response with: diagnosis (brief professional diagnosis), specialist (recommended doctor type), hospitals (array of 3 hospitals in Nepal), urgency (Low/Medium/High). Include medical disclaimer.";

        $content = self::callChat([
            ['role' => 'user', 'content' => $prompt]
        ], 'gpt-3.5-turbo', 0.7, 600);

        $result = $content ? json_decode($content, true) : null;

        if (!$result || !is_array($result)) {
            return [
                'diagnosis' => 'Please consult a healthcare professional',
                'specialist' => 'General Practitioner',
                'hospitals' => ['TUTH Central', 'Medicity Hospital', 'Rescue Center'],
                'urgency' => 'Medium'
            ];
        }

        return $result;
    }

    public static function analyzeAgriLocation(string $location, string $crop): array
    {
        $prompt = "Analyze the location '$location' for growing '$crop' in Nepal. Provide JSON with: suitability (Excellent/Good/Fair/Poor), bestVariety (recommended crop variety), soilTips (soil preparation advice), climateRisk (climate-related risks and mitigation).";

        try {
            $client = self::getClient();
            $response = $client->chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            // Guard access to the expected structure
            $content = null;
            if (isset($response->choices) && is_array($response->choices) && isset($response->choices[0]->message->content)) {
                $content = $response->choices[0]->message->content;
            } elseif (isset($response->getRawResponse) && is_callable([$response, 'getRawResponse'])) {
                // Fallback for different client versions
                $raw = $response->getRawResponse();
                $content = $raw['choices'][0]['message']['content'] ?? null;
            }

            $result = $content ? json_decode($content, true) : null;

            if (!$result) {
                // If parsing failed, return a reasonable fallback
                return [
                    'suitability' => 'Moderate',
                    'bestVariety' => 'Local variety recommended',
                    'soilTips' => 'Use organic compost and ensure proper drainage',
                    'climateRisk' => 'Check local weather patterns'
                ];
            }

            return $result;
        } catch (\Exception $e) {
            \Log::error('OpenAI analyzeAgriLocation error', ['error' => $e->getMessage(), 'exception' => $e]);

            // Return a safe fallback so frontend can show results instead of 500
            return [
                'suitability' => 'Moderate',
                'bestVariety' => 'Local variety recommended',
                'soilTips' => 'Use organic compost and ensure proper drainage',
                'climateRisk' => 'Check local weather patterns'
            ];
        }
    }

    public static function translatePlantData(string $commonName): array
    {
        $prompt = "Provide Nepali translation and brief cultivation guide for the plant named: $commonName. Return a JSON object with keys: nepaliName (string), guide (short paragraph), altitude (suggested altitude as string).";

        $content = self::callChat([
            ['role' => 'user', 'content' => $prompt]
        ], 'gpt-3.5-turbo', 0.7, 400);

        $result = $content ? json_decode($content, true) : null;

        if (!$result || !is_array($result)) {
            return [
                'nepaliName' => '',
                'guide' => 'No localized guide available.',
                'altitude' => 'Varies'
            ];
        }

        return $result;
    }

    public static function generateTravelHealthTips(string $destination): array
    {
        $prompt = "Generate 3 specific, practical health and safety tips for traveling to $destination. Return as JSON array with objects containing: tip (the advice), icon (emoji), and category (Altitude/Water/Food/Disease/Weather/Safety). Make them relevant and actionable.";

        $content = self::callChat([
            ['role' => 'user', 'content' => $prompt]
        ], 'gpt-3.5-turbo', 0.7, 400);

        $result = $content ? json_decode($content, true) : null;

        if (!$result || !is_array($result)) {
            return [
                ['tip' => 'Stay hydrated throughout your journey', 'icon' => '💧', 'category' => 'Water'],
                ['tip' => 'Consult a doctor before traveling for vaccinations', 'icon' => '💉', 'category' => 'Disease'],
                ['tip' => 'Pack a basic first aid kit and travel insurance', 'icon' => '🏥', 'category' => 'Safety']
            ];
        }

        return $result;
    }

    public static function generateDoctorRecommendations(string $diagnosis, string $urgency): array
    {
        $prompt = "Based on the diagnosis '$diagnosis' with urgency level '$urgency', recommend 3 suitable doctors in Nepal. Return as JSON array with objects containing: name (full name), specialty (medical specialty), hospital (hospital name), experience (years), phone (contact), availability (availability status). Make recommendations appropriate for the urgency level.";

        $content = self::callChat([
            ['role' => 'user', 'content' => $prompt]
        ], 'gpt-3.5-turbo', 0.7, 600);

        $result = $content ? json_decode($content, true) : null;

        if (!$result || !is_array($result)) {
            return [
                ['name' => 'Dr. Binod Poudel', 'specialty' => 'General Medicine', 'hospital' => 'TUTH Central', 'experience' => '15 years', 'phone' => '+977-1-XXXXX', 'availability' => '24/7'],
                ['name' => 'Dr. Sita Gurung', 'specialty' => 'Emergency Medicine', 'hospital' => 'Rescue Center', 'experience' => '12 years', 'phone' => '+977-1-XXXXX', 'availability' => '24/7'],
                ['name' => 'Dr. Amit Shah', 'specialty' => 'Internal Medicine', 'hospital' => 'Medicity Hospital', 'experience' => '10 years', 'phone' => '+977-1-XXXXX', 'availability' => 'Mon-Sun 9AM-9PM']
            ];
        }

        return $result;
    }

    /**
     * Chat with context-aware responses about Nexa app only
     */
    public static function chat(array $messages): string
    {
        $systemPrompt = "You are Nexo.Chat, an AI assistant for the NEXO.GLOBAL platform. Your role is to help users understand and use the Nexo application features.

IMPORTANT RULES:
1. ONLY answer questions related to NEXO.GLOBAL platform and its features
2. The platform includes these modules:
   - Travel/Voyage: Plan trips, book hotels, rent cars
   - Health: Health checks, symptom analysis, doctor recommendations, travel health tips
   - Agro: Plant information, disease analysis, crop recommendations, location-based agricultural advice
   - Education: Generate lessons, answer academic questions for Nepal NEB curriculum
   - Calender, Notes, Reminders, and basic app navigation
   - Weather information and daily weather updates, including other climatic data
3. If asked about anything unrelated to Nexo app, politely redirect: 'I'm designed to help with Nexo platform features only. How can I assist you with Travel, Health, Education, or Agro modules?'
4. Be helpful, concise, and professional
5. Use the conversation history to provide contextual responses

Keep responses relevant to the Nexo platform and its features. However, if the user asks about anything outside nexo, to help with daily tasks, please do so.";

        // Prepare messages with system prompt
        $chatMessages = [
            ['role' => 'system', 'content' => $systemPrompt]
        ];

        // Filter and validate messages (last 15 messages to maintain context)
        $recentMessages = array_slice($messages, -15);
        foreach ($recentMessages as $msg) {
            if (
                isset($msg['role']) && isset($msg['content']) &&
                in_array($msg['role'], ['user', 'assistant', 'system'])
            ) {
                if ($msg['role'] !== 'system') {
                    $role = $msg['role'] === 'model' ? 'assistant' : $msg['role'];
                    $chatMessages[] = [
                        'role' => $role,
                        'content' => $msg['content']
                    ];
                }
            }
        }

        $content = self::callChat($chatMessages, 'gpt-3.5-turbo', 0.7, 800);
        if ($content) {
            return trim($content);
        }

        Log::error('OpenAI Chat Error: empty response', ['messages_count' => count($chatMessages)]);
        return 'I\'m having trouble connecting right now. Please try again later or contact support if the issue persists.';
    }
}
