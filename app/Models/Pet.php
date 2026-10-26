<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    protected $fillable = ['owner_id', 'name', 'species', 'breed'];

    public function owner()
    {
        return $this->belongsTo(PetOwner::class, 'owner_id');
    }

    public function medicalVisits()
    {
        return $this->hasMany(MedicalVisit::class);
    }
}