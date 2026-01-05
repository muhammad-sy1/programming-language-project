<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

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
        'is_available',
    ];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // أضف هذه العلاقات
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites')
            ->withTimestamps();
    }

    /**
     * التحقق إذا كانت الشقة مفضلة للمستخدم الحالي
     */
    public function isFavoritedBy($userId = null)
    {
        if (! $userId && Auth::check()) {
            $userId = Auth::id();
        }

        return $this->favorites()
            ->where('user_id', $userId)
            ->exists();
    }
}
