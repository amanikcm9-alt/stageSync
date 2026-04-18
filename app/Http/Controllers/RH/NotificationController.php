<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\SmsService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Afficher la liste des notifications SMS
     */
    public function index(Request $request)
    {
        $query = Notification::sms()->with('notifiable');

        // Filtres
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('type')) {
            if ($request->type === 'acceptation') {
                $query->where('sujet', 'like', '%Acceptée%');
            } elseif ($request->type === 'refus') {
                $query->where('sujet', 'like', '%Refusée%');
            } elseif ($request->type === 'entretien') {
                $query->where('sujet', 'like', '%Entretien%');
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('destinataire', 'like', "%{$search}%")
                  ->orWhere('sujet', 'like', "%{$search}%")
                  ->orWhere('contenu', 'like', "%{$search}%");
            });
        }

        $notifications = $query->latest('date_envoi')->paginate(20);

        return view('rh.notifications.index', compact('notifications'));
    }

    /**
     * Renvoyer un SMS
     */
    public function renvoyer(Notification $notification)
    {
        if (!$notification->peutEtreEnvoye()) {
            return response()->json(['success' => false, 'message' => 'Cette notification ne peut pas être renvoyée']);
        }

        try {
            $resultat = $this->smsService->envoyer(
                $notification->destinataire,
                $notification->sujet,
                $notification->contenu
            );

            if ($resultat) {
                return response()->json(['success' => true, 'message' => 'SMS renvoyé avec succès']);
            } else {
                return response()->json(['success' => false, 'message' => 'Échec du renvoi du SMS']);
            }

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur lors du renvoi']);
        }
    }
}
