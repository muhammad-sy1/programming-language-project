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

    // العلاقة مع المالك
    public function owner()
    {
    //    return $this->belongsTo(User::class, 'owner_id');
    }

    // العلاقة مع الحجوزات
    public function bookings()
    {
 //       return $this->hasMany(Booking::class);
    }

    // العلاقة مع التقييمات
    public function reviews()
    {
//        return $this->hasMany(Review::class);
    }
}