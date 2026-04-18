<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Gestion des Stages</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: url('https://images.unsplash.com/photo-1521737604893-d14cc237f11d') no-repeat center center/cover;
            height: 100vh;
        }

        .login-container {
            height: 100vh;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .title {
            font-weight: bold;
            color: #333;
        }
    </style>
</head>
<body>

<div class="container login-container d-flex justify-content-center align-items-center">
    <div class="col-md-4">
        <div class="login-card">

            <h3 class="text-center title mb-4">Gestion des Stages</h3>
            <p class="text-center text-muted mb-4">Connexion à votre espace</p>

            @if ($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="/login" autocomplete="off">
                @csrf

                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" placeholder="exemple@email.com" required  ; >
                </div>

                <div class="mb-3">
                    <label>Mot de passe</label>
                    <input type="password" name="password" class="form-control" placeholder="********" required  >
                </div>

                <button type="submit" class="btn btn-primary w-100">Se connecter</button>

        <a href="{{ route('password.request') }}" class="btn btn-link">Mot de passe oublié ?</a>
            </form>

        </div>
    </div>
</div>

</body>
</html>