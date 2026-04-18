<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discussion extends Model
{
    protected $fillable = [
        'activity_id',
        'sender_id', 
        'receiver_id',
        'message',
        'type',
        'read'
    ];

    protected $casts = [
        'read' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function scopeUnread($query)
    {
        return $query->where('read', false);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('receiver_id', $userId)->orWhere('sender_id', $userId);
    }

    public function markAsRead()
    {
        $this->update(['read' => true]);
    }

    public function getTypeLabel()
    {
        return match($this->type) {
            'message' => 'Message',
            'refus' => 'Refus',
            'acceptation' => 'Acceptation',
            'demande_info' => 'Demande d\'information',
            'evaluation' => 'Évaluation',
            default => 'Message'
        };
    }

    public function getTypeColor()
    {
        return match($this->type) {
            'message' => 'info',
            'refus' => 'danger',
            'acceptation' => 'success',
            'demande_info' => 'warning',
            'evaluation' => 'primary',
            default => 'secondary'
        };
    }
}
