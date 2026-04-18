<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminActivityController extends Controller
{
    // Afficher les journaux d'activité
    public function index()
    {
        // Pour l'exemple, nous allons simuler des logs d'activité
        // En production, vous pourriez utiliser un système de logging réel
        
        $activities = $this->getSimulatedActivities();

        // Filtres
        if (request('user_id')) {
            $activities = $activities->where('user_id', request('user_id'));
        }

        if (request('action')) {
            $activities = $activities->where('action', request('action'));
        }

        if (request('date_from')) {
            $activities = $activities->where('created_at', '>=', request('date_from'));
        }

        if (request('date_to')) {
            $activities = $activities->where('created_at', '<=', request('date_to'));
        }

        // Pagination
        $perPage = request('per_page', 20);
        $currentPage = request('page', 1);
        $total = $activities->count();
        $activities = $activities->skip(($currentPage - 1) * $perPage)->take($perPage);

        $users = User::all();
        $actions = ['login', 'logout', 'create', 'update', 'delete', 'assign', 'view'];

        return view('admin.activity.index', compact('activities', 'users', 'actions', 'total', 'perPage'));
    }

    // Exporter les logs
    public function export(Request $request)
    {
        $activities = $this->getSimulatedActivities();

        // Appliquer les mêmes filtres
        if (request('user_id')) {
            $activities = $activities->where('user_id', request('user_id'));
        }

        if (request('action')) {
            $activities = $activities->where('action', request('action'));
        }

        $filename = "activity_logs_" . date('Y-m-d') . ".csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($activities) {
            $file = fopen('php://output', 'w');
            
            // En-têtes CSV
            fputcsv($file, ['Date', 'Utilisateur', 'Email', 'Action', 'Description', 'IP Address']);
            
            foreach ($activities as $activity) {
                fputcsv($file, [
                    $activity['created_at'],
                    $activity['user_name'],
                    $activity['user_email'],
                    $activity['action'],
                    $activity['description'],
                    $activity['ip_address']
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Vider les logs (avec confirmation)
    public function clear(Request $request)
    {
        // En production, cela viderait votre table de logs
        // Pour l'exemple, nous simulons l'action
        
        return redirect()->route('admin.activity.index')
            ->with('success', 'Journaux d\'activité vidés avec succès');
    }

    // Simuler des données d'activité pour la démo
    private function getSimulatedActivities()
    {
        $activities = collect([
            [
                'id' => 1,
                'user_id' => 1,
                'user_name' => 'admin admin',
                'user_email' => 'admin@gmail.com',
                'action' => 'login',
                'description' => 'Connexion réussie',
                'ip_address' => '127.0.0.1',
                'created_at' => now()->subMinutes(30)->format('Y-m-d H:i:s'),
            ],
            [
                'id' => 2,
                'user_id' => 2,
                'user_name' => 'Admin User',
                'user_email' => 'admin@example.com',
                'action' => 'create',
                'description' => 'Création d\'un nouveau stagiaire',
                'ip_address' => '127.0.0.1',
                'created_at' => now()->subHours(2)->format('Y-m-d H:i:s'),
            ],
            [
                'id' => 3,
                'user_id' => 3,
                'user_name' => 'Encadrant User',
                'user_email' => 'encadrant@example.com',
                'action' => 'view',
                'description' => 'Consultation de la liste des stagiaires',
                'ip_address' => '127.0.0.1',
                'created_at' => now()->subHours(4)->format('Y-m-d H:i:s'),
            ],
            [
                'id' => 4,
                'user_id' => 1,
                'user_name' => 'admin admin',
                'user_email' => 'admin@gmail.com',
                'action' => 'assign',
                'description' => 'Affectation d\'un encadrant à un stagiaire',
                'ip_address' => '127.0.0.1',
                'created_at' => now()->subDays(1)->format('Y-m-d H:i:s'),
            ],
            [
                'id' => 5,
                'user_id' => 4,
                'user_name' => 'stagiaire stagiaire',
                'user_email' => 'stagiaire@gmail.com',
                'action' => 'logout',
                'description' => 'Déconnexion',
                'ip_address' => '127.0.0.1',
                'created_at' => now()->subDays(2)->format('Y-m-d H:i:s'),
            ],
        ]);

        return $activities;
    }
}
