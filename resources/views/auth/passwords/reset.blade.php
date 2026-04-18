@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Choisir un nouveau mot de passe</h2>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <div class="mb-3">
            <label>Email :</label>
            <input type="email" name="email" value="{{ $email ?? old('email') }}" class="form-control" required>
            @error('email')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label>Nouveau mot de passe :</label>
            <input type="password" name="password" class="form-control" required>
            @error('password')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label>Confirmer le mot de passe :</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success">Réinitialiser le mot de passe</button>
    </form>
</div>
@endsection