<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $res = \App\Services\OpenAIService::generateItinerary('Pokhara','Standard',1);
    echo "Result:\n";
    var_export($res);
    echo "\n";
} catch (\Exception $e) {
    echo 'ERR: ' . $e->getMessage() . "\n";
}
