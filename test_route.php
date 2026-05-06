<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Route;

// Test si l'EntretienController peut être résolu
try {
    $controller = $app->make('App\Http\Controllers\EntretienController');
    echo "EntretienController résolu avec succès !\n";
} catch (Exception $e) {
    echo "Erreur de résolution: " . $e->getMessage() . "\n";
}

// Test si le TypeStageController peut être résolu
try {
    $controller = $app->make('App\Http\Controllers\TypeStageController');
    echo "TypeStageController résolu avec succès !\n";
} catch (Exception $e) {
    echo "Erreur de résolution TypeStageController: " . $e->getMessage() . "\n";
}

// Test des autres contrôleurs
$controllers = [
    'App\Http\Controllers\CandidatureController',
    'App\Http\Controllers\OffreStageController',
    'App\Http\Controllers\RHUserController',
];

foreach ($controllers as $controllerClass) {
    try {
        $controller = $app->make($controllerClass);
        echo "$controllerClass résolu avec succès !\n";
    } catch (Exception $e) {
        echo "Erreur de résolution $controllerClass: " . $e->getMessage() . "\n";
    }
}
