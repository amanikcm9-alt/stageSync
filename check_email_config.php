<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "=== Configuration Email Actuelle ===\n\n";

echo "Mail Driver : " . config('mail.default') . "\n";
echo "Mail Host : " . config('mail.mailers.smtp.host') . "\n";
echo "Mail Port : " . config('mail.mailers.smtp.port') . "\n";
echo "Mail Username : " . config('mail.mailers.smtp.username') . "\n";
echo "Mail Password : " . (config('mail.mailers.smtp.password') ? 'Configuré' : 'Non configuré') . "\n";
echo "Mail Encryption : " . config('mail.mailers.smtp.encryption') . "\n";
echo "Mail From Address : " . config('mail.from.address') . "\n";
echo "Mail From Name : " . config('mail.from.name') . "\n";

echo "\n=== Test d'envoi d'email ===\n";

try {
    \Illuminate\Support\Facades\Mail::raw(
        "Ceci est un email de test pour vérifier la configuration.\n\n" .
        "Heure d'envoi : " . now()->format('d/m/Y H:i:s') . "\n" .
        "URL de l'application : " . url('/'),
        function ($message) {
            $message->to('test@example.com')
                   ->subject('Test Email - Configuration');
        }
    );
    echo "Email de test envoyé avec succès\n";
} catch (\Exception $e) {
    echo "ERREUR lors de l'envoi de l'email : " . $e->getMessage() . "\n";
}

echo "\n=== Explication pour Gmail ===\n";
echo "Pour recevoir les notifications sur Gmail :\n";
echo "1. Le candidat doit fournir son adresse Gmail dans le formulaire\n";
echo "2. Le système doit être configuré pour envoyer des emails\n";
echo "3. L'email sera envoyé à l'adresse du candidat (pas à votre Gmail)\n";
echo "4. Les SMS sont des simulations - seul l'email est réel\n";
