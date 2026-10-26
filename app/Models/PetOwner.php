<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PetOwner extends Model
{
    protected $fillable = ['name', 'phone'];

    public function pets()
    {
        return $this->hasMany(Pet::class, 'owner_id');
    }
}