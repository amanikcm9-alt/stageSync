@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Réinitialisation du mot de passe</h2>

    @if(session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-3">
            <label>Email :</label>
            <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
            @error('email')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Envoyer le lien de réinitialisation</button>
    </form>
</div>
@endsection