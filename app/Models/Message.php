<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'message',
        'attachment',
        'attachment_type',
        'is_read',
        'read_at'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime'
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function getAttachmentUrlAttribute()
    {
        if ($this->attachment) {
            return asset('storage/' . $this->attachment);
        }
        
        return null;
    }

    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        }
    }

    public function getTimeAttribute()
    {
        return $this->created_at->format('h:i A');
    }

    public function getDateAttribute()
    {
        return $this->created_at->format('Y-m-d');
    }

    public function isRecent()
    {
        return $this->created_at->gt(now()->subMinutes(5));
    }
}