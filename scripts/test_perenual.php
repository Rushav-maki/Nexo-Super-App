<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;

$key = config('services.perenual.key');
$base = config('services.perenual.base');

if (!$key) {
    echo "No PERENUAL_API_KEY configured\n";
    exit(1);
}

// 1) species list
echo "Fetching species-list...\n";
$res = Http::withoutVerifying()->timeout(30)->get($base . '/v2/species-list', ['key' => $key, 'page' => 1]);
echo "Status: " . $res->status() . "\n";
if ($res->ok()) {
    $json = $res->json();
    $first = $json['data'][0] ?? null;
    echo "First species id/name: " . ($first['id'] ?? 'n/a') . " / " . ($first['common_name'] ?? ($first['scientific_name'] ?? 'n/a')) . "\n";
} else {
    echo "Body: " . $res->body() . "\n";
}

// 2) species details (if id available)
if (!empty($first['id'])) {
    $id = $first['id'];
    echo "\nFetching species details for id: $id\n";
    $res2 = Http::withoutVerifying()->timeout(30)->get($base . '/v2/species/details/' . $id, ['key' => $key]);
    echo "Status: " . $res2->status() . "\n";
    if ($res2->ok()) {
        $details = $res2->json();
        echo "Name: " . ($details['data']['common_name'] ?? ($details['data']['scientific_name'] ?? 'n/a')) . "\n";
    } else {
        echo "Body: " . $res2->body() . "\n";
    }
}

// 3) diseases
echo "\nFetching pest-disease-list...\n";
$res3 = Http::withoutVerifying()->timeout(30)->get($base . '/pest-disease-list', ['key' => $key]);
echo "Status: " . $res3->status() . "\n";
if ($res3->ok()) {
    $djson = $res3->json();
    $firstD = $djson['data'][0] ?? null;
    echo "First disease id/name: " . ($firstD['id'] ?? 'n/a') . " / " . ($firstD['common_name'] ?? 'n/a') . "\n";
} else {
    echo "Body: " . $res3->body() . "\n";
}
