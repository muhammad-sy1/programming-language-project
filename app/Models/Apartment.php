<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
    protected $table = 'Apartments';
    protected $fillable = ['governorate', 'city', 'description', 'price'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
