<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
 
    use HasApiTokens, Notifiable,HasFactory;

    protected $fillable = [
        'first_name', 'last_name',
        'birth_date', 'password' ,'phone',
        'id_photo_path',
        'personal_photo_path'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
    public function getIdPhotoUrlAttribute()
    {
        return $this->id_photo_path ? asset('storage/' . $this->id_photo_path) : null;
    }
    public function getPersonalPhotoUrlAttribute()
    {
        return $this->personal_photo_path ? asset('storage/' . $this->personal_photo_path) : null;
    }
}
