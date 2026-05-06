# Configuration Gmail pour l'envoi d'emails

## Problème identifié
Le fichier `.env` utilise `MAIL_MAILER=log` qui écrit les emails dans les logs au lieu de les envoyer réellement.

## Solution : Configuration Gmail

### 1. Mettre à jour le fichier .env
Ajoutez ces lignes dans votre fichier `.env` :

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=votre-email@gmail.com
MAIL_PASSWORD=votre-mot-de-passe-app
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=votre-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 2. Activer l'authentification à deux facteurs sur Gmail
1. Allez dans les paramètres de votre compte Google
2. Activez l'authentification à deux facteurs
3. Générez un "mot de passe d'application"

### 3. Obtenir un mot de passe d'application Gmail
1. Allez sur : https://myaccount.google.com/apppasswords
2. Sélectionnez "Autre (nom personnalisé)"
3. Donnez un nom (ex: "Laravel Candidature")
4. Copiez le mot de passe généré (16 caractères)
5. Utilisez ce mot de passe dans `MAIL_PASSWORD`

### 4. Redémarrer Laravel
Après avoir modifié le .env, exécutez :
```bash
php artisan config:clear
php artisan cache:clear
php artisan config:cache
```

## Test d'envoi d'email
Pour tester l'envoi d'email, vous pouvez utiliser :
```bash
php artisan tinker
```
Puis :
```php
Mail::raw('Test email', function($message) {
    $message->to('votre-email@test.com')->subject('Test');
});
```

## Alternative : Utiliser Mailtrap (pour le développement)
Si vous préférez tester sans envoyer de vrais emails :
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=votre-username-mailtrap
MAIL_PASSWORD=votre-password-mailtrap
MAIL_ENCRYPTION=tls
```

## Notes importantes
- N'utilisez jamais votre mot de passe Gmail normal
- Utilisez toujours un mot de passe d'application
- Le port 587 avec TLS est recommandé pour Gmail
- Assurez-vous que votre adresse Gmail est vérifiée
