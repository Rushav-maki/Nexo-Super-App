<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo 'PERENUAL key: ' . (config('services.perenual.key') ?: 'empty') . PHP_EOL;
echo 'PERENUAL base: ' . (config('services.perenual.base') ?: 'empty') . PHP_EOL;
