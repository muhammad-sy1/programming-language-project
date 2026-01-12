<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'apartment_id',
        'sender_id',
        'receiver_id',
        'subject',
        'is_sender_read',
        'is_receiver_read',
        'last_message_at'
    ];

    protected $casts = [
        'is_sender_read' => 'boolean',
        'is_receiver_read' => 'boolean',
        'last_message_at' => 'datetime'
    ];

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class)->latest();
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latest();
    }

    public function isParticipant($userId)
    {
        return $this->sender_id == $userId || $this->receiver_id == $userId;
    }

    public function getOtherParticipant($userId)
    {
        if ($this->sender_id == $userId) {
            return $this->receiver;
        } elseif ($this->receiver_id == $userId) {
            return $this->sender;
        }
        
        return null;
    }

    public function isSender($userId)
    {
        return $this->sender_id == $userId;
    }

    public function getUserRole($userId)
    {
        if ($this->sender_id == $userId) {
            return 'sender';
        } elseif ($this->receiver_id == $userId) {
            return 'receiver';
        }
        
        return null;
    }

    public function markAsRead($userId)
    {
        $role = $this->getUserRole($userId);
        
        if ($role === 'sender') {
            $this->update(['is_sender_read' => true]);
        } elseif ($role === 'receiver') {
            $this->update(['is_receiver_read' => true]);
        }
        
        $this->messages()
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
    }

    public function hasUnreadMessages($userId)
    {
        $role = $this->getUserRole($userId);
        
        if ($role === 'sender') {
            return !$this->is_sender_read;
        } elseif ($role === 'receiver') {
            return !$this->is_receiver_read;
        }
        
        return false;
    }

    public function unreadMessagesCount($userId)
    {
        return $this->messages()
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->count();
    }

    public static function findExisting($apartmentId, $senderId, $receiverId)
    {
        return self::where('apartment_id', $apartmentId)
            ->where('sender_id', $senderId)
            ->where('receiver_id', $receiverId)
            ->first();
    }
}