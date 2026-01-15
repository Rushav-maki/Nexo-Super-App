<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\EduService;

try {
    echo "Generating quiz via EduService...\n";
    $quiz = EduService::generateQuiz(6, 'Photosynthesis');
    echo "Type: " . gettype($quiz) . "\n";
    echo "Count: " . count($quiz) . "\n";
    echo json_encode(array_slice($quiz,0,3), JSON_PRETTY_PRINT) . "\n";
} catch (\Throwable $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
