<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $controller = new \App\Http\Controllers\EntretienController();
    echo "EntretienController créé avec succès !\n";
    echo "Méthodes disponibles:\n";
    $methods = get_class_methods($controller);
    foreach ($methods as $method) {
        if ($method !== '__construct' && !str_starts_with($method, '_')) {
            echo "- $method\n";
        }
    }
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
