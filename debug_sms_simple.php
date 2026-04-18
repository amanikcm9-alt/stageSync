<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "=== Test simple du service SMS ===\n\n";

// Test du service SMS avec la méthode publique
$smsService = new App\Services\SmsService();

echo "Test d'envoi SMS simple:\n";
$result = $smsService->envoyer('123456789', 'Test sujet', 'Test contenu');
echo "Résultat : " . ($result ? 'SUCCÈS' : 'ÉCHEC') . "\n";

// Vérifier les notifications créées
echo "\n=== Notifications créées ===\n";
$notifications = App\Models\Notification::orderBy('created_at', 'desc')->take(5)->get();
foreach ($notifications as $notif) {
    echo "ID: {$notif->id}\n";
    echo "Type: {$notif->type}\n";
    echo "Destinataire: {$notif->destinataire}\n";
    echo "Statut: {$notif->statut}\n";
    echo "Erreur: " . ($notif->erreur_message ?? 'Aucune') . "\n";
    echo "Date envoi: " . ($notif->date_envoi ? $notif->date_envoi->format('Y-m-d H:i:s') : 'Non envoyé') . "\n";
    echo "------------------------\n";
}
