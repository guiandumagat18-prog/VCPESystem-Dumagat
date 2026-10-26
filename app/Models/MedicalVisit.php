<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalVisit extends Model
{
    protected $fillable = [
        'pet_id',
        'veterinarian_id',
        'examination_date',
        'diagnosis',
        'procedure_cost',
        'payment_status',
    ];

    protected $casts = [
        'examination_date' => 'date',
        'procedure_cost'   => 'decimal:2',
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function veterinarian()
    {
        return $this->belongsTo(Veterinarian::class);
    }

    public function treatments()
    {
        return $this->hasMany(Treatment::class, 'visit_id');
    }
}