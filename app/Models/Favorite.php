<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'apartment_id'
    ];

    /**
     * العلاقة مع المستخدم
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * العلاقة مع الشقة
     */
    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    /**
     * التحقق إذا كانت الشقة مفضلة للمستخدم
     */
    public static function isFavorite($userId, $apartmentId)
    {
        return self::where('user_id', $userId)
            ->where('apartment_id', $apartmentId)
            ->exists();
    }

    /**
     * جلب جميع الشقق المفضلة للمستخدم
     */
    public static function getUserFavorites($userId)
    {
        return self::with('apartment')
            ->where('user_id', $userId)
            ->latest()
            ->get();
    }

    /**
     * عدد الشقق المفضلة للمستخدم
     */
    public static function countUserFavorites($userId)
    {
        return self::where('user_id', $userId)->count();
    }
}