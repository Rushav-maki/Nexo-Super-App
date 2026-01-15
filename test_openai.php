<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $response = \App\Services\OpenAIService::chat([['role' => 'user', 'content' => 'Hello from test script']]);
    echo "RESPONSE:\n" . $response . "\n";
} catch (\Throwable $e) {
    echo "ERROR:\n" . $e->getMessage() . "\n";
    echo "TRACE:\n" . $e->getTraceAsString() . "\n";
}
