<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "=== Test d'envoi d'email de candidature ===\n\n";

// Récupérer une candidature existante
$candidature = App\Models\Candidature::with('offreStage')->first();

if (!$candidature) {
    echo "Aucune candidature trouvée pour le test\n";
    exit;
}

echo "Candidature trouvée :\n";
echo "ID : {$candidature->id}\n";
echo "Nom : {$candidature->nom}\n";
echo "Prénom : {$candidature->prenom}\n";
echo "Email : {$candidature->email}\n";
echo "Téléphone : {$candidature->telephone}\n";
echo "Offre : " . ($candidature->offreStage ? $candidature->offreStage->titre : 'N/A') . "\n\n";

// Simuler l'envoi d'email comme dans le contrôleur
$password = 'TestPassword123';

echo "Test d'envoi d'email d'acceptation :\n";

try {
    \Illuminate\Support\Facades\Mail::raw(
        "Félicitations ! Votre candidature a été acceptée.\n\n" .
        "Email : {$candidature->email}\n" .
        "Mot de passe : {$password}\n\n" .
        "URL de connexion : " . url('/login') . "\n\n" .
        "Nous vous conseillons de changer votre mot de passe lors de votre première connexion.",
        function ($message) use ($candidature) {
            $message->to($candidature->email)
                   ->subject('Votre compte stagiaire a été créé - Candidature Acceptée');
        }
    );
    echo "✅ Email d'acceptation envoyé avec succès à : {$candidature->email}\n";
    
} catch (\Exception $e) {
    echo "❌ ERREUR lors de l'envoi de l'email : " . $e->getMessage() . "\n";
    echo "Stack trace : " . $e->getTraceAsString() . "\n";
}

echo "\n=== Vérification des logs Laravel ===\n";
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $logs = file_get_contents($logFile);
    $recentLogs = substr($logs, -2000); // Derniers 2000 caractères
    echo "Logs récents :\n";
    echo $recentLogs;
} else {
    echo "Aucun fichier de logs trouvé\n";
}
