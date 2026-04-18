<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "=== Test d'acceptation réelle de candidature ===\n\n";

// Récupérer une candidature avec statut 'recue'
$candidature = App\Models\Candidature::where('statut', 'recue')->with('offreStage')->first();

if (!$candidature) {
    echo "Aucune candidature avec statut 'recue' trouvée\n";
    exit;
}

echo "Candidature à accepter :\n";
echo "ID : {$candidature->id}\n";
echo "Nom : {$candidature->nom}\n";
echo "Prénom : {$candidature->prenom}\n";
echo "Email : {$candidature->email}\n";
echo "Statut actuel : {$candidature->statut}\n";
echo "Offre : " . ($candidature->offreStage ? $candidature->offreStage->titre : 'N/A') . "\n\n";

// Simuler le processus d'acceptation comme dans le contrôleur
echo "Simulation du processus d'acceptation...\n";

// Vérifier si l'utilisateur existe déjà
$existingUser = App\Models\User::where('email', $candidature->email)->first();

if (!$existingUser) {
    echo "Création d'un nouvel utilisateur...\n";
    
    $password = 'TestPassword123';
    $stagiaireRole = App\Models\Role::where('name', 'stagiaire')->first();
    
    // Créer l'utilisateur
    $user = App\Models\User::create([
        'nom' => $candidature->nom,
        'prenom' => $candidature->prenom,
        'email' => $candidature->email,
        'password' => bcrypt($password),
        'role_id' => $stagiaireRole->id,
        'email_verified_at' => now(),
    ]);
    
    echo "Utilisateur créé avec ID : {$user->id}\n";
    
    // Mettre à jour la candidature
    $candidature->update([
        'statut' => 'accepte',
        'date_decision' => now(),
        'commentaire' => 'Test d\'acceptation'
    ]);
    
    echo "Candidature mise à jour\n";
    
    // Envoyer l'email
    try {
        \Illuminate\Support\Facades\Log::info("Tentative d'envoi d'email à : {$candidature->email}");
        
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
        
        \Illuminate\Support\Facades\Log::info("Email envoyé avec succès à : {$candidature->email}");
        echo "✅ Email envoyé avec succès\n";
        
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error("ERREUR email : " . $e->getMessage());
        echo "❌ Erreur lors de l'envoi de l'email : " . $e->getMessage() . "\n";
    }
    
} else {
    echo "L'utilisateur existe déjà, pas d'email envoyé\n";
}

echo "\nTest terminé. Vérifiez votre boîte Gmail.\n";
