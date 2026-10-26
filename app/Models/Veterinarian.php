<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Veterinarian extends Model
{
    protected $fillable = ['name', 'specialization'];

    public function medicalVisits()
    {
        return $this->hasMany(MedicalVisit::class);
    }
}