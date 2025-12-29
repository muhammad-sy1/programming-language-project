<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'description',
        'governorate',
        'city',
        'price',
        'photo_path',
        'is_available'
    ];

    protected $casts = [
        'is_available' => 'boolean'
    ];

  
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}