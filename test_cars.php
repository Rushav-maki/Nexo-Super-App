<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;

try {
    $controller = new App\Http\Controllers\TravelController();
    $response = $controller->cars(new Request());
    echo "STATUS: " . $response->getStatusCode() . "\n";
    echo "BODY:\n" . $response->getContent() . "\n";
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
