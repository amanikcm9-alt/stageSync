@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Journaux d'Activité</h1>

    <!-- Filtres -->
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-3">
            <select name="user_id" class="form-select">
                <option value="">Tous les utilisateurs</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->nom }} {{ $user->prenom }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="action" class="form-select">
                <option value="">Toutes les actions</option>
                @foreach($actions as $action)
                    <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                        {{ ucfirst($action) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
        </div>
        <div class="col-md-2">
            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-secondary">Filtrer</button>
            <a href="{{ route('admin.activity.index') }}" class="btn btn-outline-secondary">Réinitialiser</a>
            <a href="{{ route('admin.activity.export') }}" class="btn btn-success">Export CSV</a>
        </div>
    </form>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5>Total activités</h5>
                    <h3>{{ $total }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5>Aujourd'hui</h5>
                    <h3>{{ $activities->where('created_at', '>=', now()->format('Y-m-d'))->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5>Cette semaine</h5>
                    <h3>{{ $activities->where('created_at', '>=', now()->subDays(7)->format('Y-m-d'))->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5>Actions critiques</h5>
                    <h3>{{ $activities->whereIn('action', ['delete', 'deactivate'])->count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau -->
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Utilisateur</th>
                    <th>Action</th>
                    <th>Description</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                @foreach($activities as $activity)
                    <tr>
                        <td>{{ $activity['created_at'] }}</td>
                        <td>
                            <div>
                                <strong>{{ $activity['user_name'] }}</strong><br>
                                <small class="text-muted">{{ $activity['user_email'] }}</small>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-{{ 
                                $activity['action'] === 'login' ? 'success' : 
                                ($activity['action'] === 'logout' ? 'secondary' : 
                                ($activity['action'] === 'delete' ? 'danger' : 
                                ($activity['action'] === 'create' ? 'primary' : 'info')) 
                            }}">
                                {{ ucfirst($activity['action']) }}
                            </span>
                        </td>
                        <td>{{ $activity['description'] }}</td>
                        <td><code>{{ $activity['ip_address'] }}</code></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Actions -->
    <div class="mt-4">
        <form action="{{ route('admin.activity.clear') }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment vider tous les journaux d\'activité?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash"></i> Vider les journaux
            </button>
        </form>
    </div>
</div>
@endsection
