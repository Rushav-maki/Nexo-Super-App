<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$envKey = getenv('OPENAI_API_KEY');
$configKey = config('openai.api_key');

function maskKey($k) {
    if (!$k) return '(empty)';
    $len = strlen($k);
    if ($len <= 8) return $k;
    return substr($k,0,6) . str_repeat('*', max(0, $len-10)) . substr($k,-4);
}

echo "getenv(OPENAI_API_KEY): " . maskKey($envKey) . PHP_EOL;
echo "config('openai.api_key'): " . maskKey($configKey) . PHP_EOL;

// Also show if Laravel's env() helper reads from .env
$envHelper = env('OPENAI_API_KEY');
echo "env('OPENAI_API_KEY'): " . maskKey($envHelper) . PHP_EOL;

// Show if config is cached
$cached = file_exists(base_path('bootstrap/cache/config.php')) ? 'yes' : 'no';
echo "config cached: " . $cached . PHP_EOL;