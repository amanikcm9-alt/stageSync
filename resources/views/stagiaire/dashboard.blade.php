@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Dashboard Stagiaire</h1>
    <p>Bienvenue, <strong>{{ Auth::user()->nom }} {{ Auth::user()->prenom }}</strong></p>

    <div class="row g-4">
        <div class="col-md-12">
            <div class="card h-100 border-secondary">
                <div class="card-body">
                    <h5 class="card-title">Mon Encadrant</h5>
                    <p class="card-text">
                        {{ Auth::user()->encadrant ? Auth::user()->encadrant->nom . ' ' . Auth::user()->encadrant->prenom : 'Aucun encadrant assigné' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection