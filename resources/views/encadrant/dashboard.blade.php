@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Dashboard Encadrant</h1>
    <p>Bienvenue, <strong>{{ Auth::user()->nom }} {{ Auth::user()->prenom }}</strong></p>

    <div class="row g-4">
        <div class="col-md-12">
            <div class="card h-100 border-info">
                <div class="card-body">
                    <h5 class="card-title">Mes Stagiaires</h5>
                    <p class="card-text">{{ \App\Models\User::whereHas('role', function($query) { $query->where('name', 'stagiaire'); })->where('encadrant_id', Auth::id())->count() }} stagiaires assignés</p>
                    <a href="#" class="btn btn-info btn-sm disabled">Voir détails</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection