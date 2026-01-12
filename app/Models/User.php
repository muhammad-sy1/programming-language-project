<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Apartment;

class User extends Authenticatable
{

    use HasApiTokens, Notifiable, HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'birth_date',
        'password',
        'phone',
        'id_photo_path',
        'personal_photo_path',
        'status_updated_at',

    ];
    protected $dates = [
        'created_at',
        'updated_at',
        'email_verified_at',
        'status_updated_at',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'status_updated_at' => 'datetime',
    ];
    protected $table = 'users';

    public function apartments()
    {
        return $this->hasMany(Apartment::class, 'owner_id');
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];
    public function getIdPhotoUrlAttribute()
    {
        return $this->id_photo_path ? asset('storage/' . $this->id_photo_path) : null;
    }
    public function getPersonalPhotoUrlAttribute()
    {
        return $this->personal_photo_path ? asset('storage/' . $this->personal_photo_path) : null;
    }
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoriteApartments()
    {
        return $this->belongsToMany(Apartment::class, 'favorites')
            ->withTimestamps();
    }
    ////////////////////messages
    public function conversationsAsSender()
{
    return $this->hasMany(Conversation::class, 'sender_id');
}

public function conversationsAsReceiver()
{
    return $this->hasMany(Conversation::class, 'receiver_id');
}

public function sentMessages()
{
    return $this->hasMany(Message::class, 'sender_id');
}

public function getConversationsAttribute()
{
    return Conversation::where('sender_id', $this->id)
        ->orWhere('receiver_id', $this->id)
        ->get();
}

public function unreadConversationsCount()
{
    return Conversation::where(function($q) {
        $q->where('sender_id', $this->id)->where('is_sender_read', false)
          ->orWhere('receiver_id', $this->id)->where('is_receiver_read', false);
    })->count();
}
}
