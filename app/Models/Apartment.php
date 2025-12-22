<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
    // table name should match the migration (lowercase 'apartments')
    protected $table = 'apartments';

    protected $fillable = [
        'governorate',
        'city',
        'price',
        'description',
        'user_id',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
