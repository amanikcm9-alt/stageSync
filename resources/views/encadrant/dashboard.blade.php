@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Dashboard Encadrant</h1>
    <p>Bienvenue, <strong>{{ Auth::user()->nom }} {{ Auth::user()->prenom }}</strong></p>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card h-100 border-info">
                <div class="card-body">
                    <h5 class="card-title">Mes Activités</h5>
                    <p class="card-text">Gérez les activités de vos stagiaires</p>
                    <a href="{{ route('encadrant.activities.index') }}" class="btn btn-info btn-sm">Voir mes activités</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card h-100 border-success">
                <div class="card-body">
                    <h5 class="card-title">Mes Évaluations</h5>
                    <p class="card-text">Consultez et créez des évaluations</p>
                    <a href="{{ route('encadrant.evaluations.index') }}" class="btn btn-success btn-sm">Voir mes évaluations</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection