<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\EducationController;

$controller = new EducationController();

$req = Request::create('/education/quiz', 'POST', ['grade' => 6, 'topic' => 'Photosynthesis']);
$res = $controller->quiz($req);

echo "Status: " . $res->getStatusCode() . PHP_EOL;
echo substr($res->getContent(), 0, 1000) . PHP_EOL;