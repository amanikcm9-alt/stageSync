<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "=== Test détaillé du service SMS ===\n\n";

// Test du service SMS directement
$smsService = new App\Services\SmsService();

echo "1. Test de la méthode simulerEnvoiSms directement:\n";
$simulation = $smsService->simulerEnvoiSms('123456789', 'Test message');
echo "Résultat simulation : " . json_encode($simulation) . "\n\n";

echo "2. Test de création de notification en base:\n";
try {
    $notification = App\Models\Notification::create([
        'type' => 'sms',
        'destinataire' => '123456789',
        'sujet' => 'Test sujet',
        'contenu' => 'Test contenu',
        'statut' => 'en_attente',
        'date_envoi' => null
    ]);
    echo "Notification créée avec succès, ID : {$notification->id}\n";
    
    // Test des méthodes de marquage
    echo "3. Test de marquerEnvoye():\n";
    $notification->marquerEnvoye();
    echo "Statut après marquerEnvoye : {$notification->statut}\n";
    echo "Date envoi : {$notification->date_envoi}\n";
    
} catch (\Exception $e) {
    echo "ERREUR lors de la création/mise à jour : " . $e->getMessage() . "\n";
}

echo "\n4. Test complet d'envoi SMS:\n";
$result = $smsService->envoyer('123456789', 'Test sujet', 'Test contenu');
echo "Résultat final : " . ($result ? 'SUCCÈS' : 'ÉCHEC') . "\n";

// Vérifier les dernières notifications
echo "\n=== Dernières notifications ===\n";
$notifications = App\Models\Notification::orderBy('created_at', 'desc')->take(3)->get();
foreach ($notifications as $notif) {
    echo "ID: {$notif->id}, Type: {$notif->type}, Statut: {$notif->statut}, Erreur: " . ($notif->erreur_message ?? 'Aucune') . "\n";
}
