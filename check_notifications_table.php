<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "=== Structure de la table notifications ===\n";

// Vérifier les colonnes de la table notifications
$columns = \Illuminate\Support\Facades\Schema::getColumnListing('notifications');
echo "Colonnes trouvées:\n";
foreach ($columns as $column) {
    echo "- {$column}\n";
}

echo "\n=== Test d'insertion simple ===\n";

try {
    $notification = new App\Models\Notification();
    $notification->type = 'sms';
    $notification->destinataire = '123456789';
    $notification->sujet = 'Test sujet';
    $notification->contenu = 'Test contenu';
    $notification->statut = 'en_attente';
    $notification->date_envoi = null;
    
    $notification->save();
    echo "Notification créée avec succès, ID: {$notification->id}\n";
    
} catch (\Exception $e) {
    echo "ERREUR lors de la création: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
