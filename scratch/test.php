<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $controller = new \App\Http\Controllers\PublikController();
    $response = $controller->apiDetail(11); // ID that's probably valid or try 10
    echo "Success: " . $response->getContent();
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine();
}
