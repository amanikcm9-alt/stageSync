<?php

namespace App\Http\Controllers;

use App\Models\Discussion;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Envoyer un message de discussion
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'activity_id' => 'required|exists:activities,id',
            'message' => 'required|string|max:1000'
        ]);

        $user = Auth::user();
        $activity = Activity::findOrFail($request->activity_id);

        // Vérifier les permissions
        if ($user->role->name === 'stagiaire' && $activity->stagiaire_id !== $user->id) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        if ($user->role->name === 'encadrant' && $activity->encadrant_id !== $user->id) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        // Déterminer le destinataire
        if ($user->role->name === 'stagiaire') {
            $receiverId = $activity->encadrant_id;
        } else {
            $receiverId = $activity->stagiaire_id;
        }

        // Créer le message
        $discussion = Discussion::create([
            'activity_id' => $activity->id,
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'message' => $request->message,
            'type' => 'discussion',
            'read' => false
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Message envoyé avec succès',
            'discussion' => $discussion
        ]);
    }

    /**
     * Récupérer les messages d'une activité
     */
    public function getMessages(Request $request, Activity $activity)
    {
        $user = Auth::user();

        // Vérifier les permissions
        if ($user->role->name === 'stagiaire' && $activity->stagiaire_id !== $user->id) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        if ($user->role->name === 'encadrant' && $activity->encadrant_id !== $user->id) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        // Récupérer les messages
        $messages = Discussion::where('activity_id', $activity->id)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Marquer les messages comme lus
        Discussion::where('activity_id', $activity->id)
            ->where('receiver_id', $user->id)
            ->where('read', false)
            ->update(['read' => true]);

        return response()->json([
            'success' => true,
            'messages' => $messages
        ]);
    }

    /**
     * Marquer un message comme lu
     */
    public function markAsRead(Request $request, Discussion $discussion)
    {
        $user = Auth::user();

        // Vérifier que l'utilisateur est le destinataire
        if ($discussion->receiver_id !== $user->id) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $discussion->update(['read' => true]);

        return response()->json(['success' => true]);
    }
}
