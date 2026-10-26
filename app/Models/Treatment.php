<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Treatment extends Model
{
    protected $fillable = ['visit_id', 'treatment_given', 'cost'];

    protected $casts = ['cost' => 'decimal:2'];

    public function visit()
    {
        return $this->belongsTo(MedicalVisit::class, 'visit_id');
    }
}