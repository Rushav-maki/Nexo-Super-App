<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\AgroController;

$controller = new AgroController();

echo "Calling getPlants...\n";
$req = Request::create('/agro/plants', 'GET', ['page' => 1]);
$res = $controller->getPlants($req);
echo "Status: " . $res->getStatusCode() . "\n";
echo substr($res->getContent(), 0, 500) . "\n\n";

echo "Calling getPlantDetails(1)...\n";
$res2 = $controller->getPlantDetails(new Request(), 1);
echo "Status: " . $res2->getStatusCode() . "\n";
echo substr($res2->getContent(), 0, 500) . "\n\n";

echo "Calling getDiseases...\n";
$res3 = $controller->getDiseases();
echo "Status: " . $res3->getStatusCode() . "\n";
echo substr($res3->getContent(), 0, 500) . "\n";
