<?php
require __DIR__ . '/../vendor/autoload.php';
echo 'Factory exists: ' . (class_exists('\\OpenAI\\Factory') ? 'yes' : 'no') . PHP_EOL;
echo 'Client exists: ' . (class_exists('\\OpenAI\\Client') ? 'yes' : 'no') . PHP_EOL;
