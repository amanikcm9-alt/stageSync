<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "=== Test du service SMS ===\n\n";

// Test du service SMS directement
$smsService = new App\Services\SmsService();

// Créer une fausse candidature pour le test
$candidature = new stdClass();
$candidature->prenom = 'Test';
$candidature->telephone = '123456789';
$candidature->offreStage = new stdClass();
$candidature->offreStage->titre = 'Test Offre';
$candidature->offreStage->entreprise = new stdClass();
$candidature->offreStage->entreprise->nom = 'Test Entreprise';

echo "Test d'envoi SMS d'acceptation:\n";
$result = $smsService->envoyerAcceptationCandidature($candidature);

echo "Résultat : " . ($result ? 'SUCCÈS' : 'ÉCHEC') . "\n";

// Vérifier les logs
echo "\n=== Vérification des notifications en base ===\n";

$notifications = App\Models\Notification::where('type', 'sms')
    ->orderBy('created_at', 'desc')
    ->take(3)
    ->get();

foreach ($notifications as $notif) {
    echo "Notification ID : {$notif->id}\n";
    echo "Destinataire : {$notif->destinataire}\n";
    echo "Sujet : {$notif->sujet}\n";
    echo "Statut : {$notif->statut}\n";
    echo "Date envoi : " . ($notif->date_envoi ? $notif->date_envoi : 'Non envoyé') . "\n";
    echo "------------------------\n";
}
