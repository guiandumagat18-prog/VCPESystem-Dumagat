<?php

namespace App\Services;

use App\Models\MedicalVisit;
use App\Models\Pet;
use App\Models\PetOwner;
use Illuminate\Support\Facades\DB;

class DomainService
{
    /* ---------------- Sanitizers ---------------- */

    private function sanitizeText(?string $value): ?string
    {
        return $value === null ? null : trim(strip_tags($value));
    }

    private function sanitizePhone(?string $value): ?string
    {
        return $value === null ? null : preg_replace('/[^0-9+]/', '', trim($value));
    }

    private function formatMoney($value): float
    {
        return round((float) $value, 2);
    }

    /* ---------------- Pet Owner ---------------- */

    public function createOwner(array $data): PetOwner
    {
        $data['name']  = $this->sanitizeText($data['name']);
        $data['phone'] = $this->sanitizePhone($data['phone'] ?? null);

        return DB::transaction(fn () => PetOwner::create($data));
    }

    /* ---------------- Pet ---------------- */

    public function createPet(array $data): Pet
    {
        $data['name']    = $this->sanitizeText($data['name']);
        $data['species'] = $this->sanitizeText($data['species']);
        $data['breed']   = $this->sanitizeText($data['breed'] ?? null);

        return DB::transaction(fn () => Pet::create($data));
    }

    public function updatePet(Pet $pet, array $data): Pet
    {
        if (isset($data['name']))    $data['name']    = $this->sanitizeText($data['name']);
        if (isset($data['species'])) $data['species'] = $this->sanitizeText($data['species']);
        if (array_key_exists('breed', $data)) $data['breed'] = $this->sanitizeText($data['breed']);

        return DB::transaction(function () use ($pet, $data) {
            $pet->update($data);
            return $pet->fresh();
        });
    }

    /* ---------------- Medical Visit ---------------- */

    public function createVisit(array $data): MedicalVisit
    {
        return DB::transaction(function () use ($data) {
            // Business rule: confirm pet ownership
            $pet = Pet::with('owner')->findOrFail($data['pet_id']);
            if (! $pet->owner) {
                throw new \RuntimeException('Pet has no registered owner; cannot create visit.');
            }

            // Business rule: no visit without diagnosis
            $diagnosis = $this->sanitizeText($data['diagnosis'] ?? '');
            if ($diagnosis === '') {
                throw new \InvalidArgumentException('Diagnosis notes are required.');
            }

            $treatments = $data['treatments'] ?? [];
            unset($data['treatments']);

            $data['diagnosis']      = $diagnosis;
            $data['procedure_cost'] = $this->formatMoney($data['procedure_cost'] ?? 0);

            $visit = MedicalVisit::create($data);

            foreach ($treatments as $t) {
                $visit->treatments()->create([
                    'treatment_given' => $this->sanitizeText($t['treatment_given']),
                    'cost'            => $this->formatMoney($t['cost'] ?? 0),
                ]);
            }

            // Business rule: procedure cost = sum of treatment costs when treatments exist
            if (! empty($treatments)) {
                $visit->procedure_cost = $visit->treatments()->sum('cost');
                $visit->save();
            }

            return $visit->load('pet.owner', 'veterinarian', 'treatments');
        });
    }

    public function updateVisit(MedicalVisit $visit, array $data): MedicalVisit
    {
        return DB::transaction(function () use ($visit, $data) {
            if (isset($data['diagnosis'])) {
                $data['diagnosis'] = $this->sanitizeText($data['diagnosis']);
                if ($data['diagnosis'] === '') {
                    throw new \InvalidArgumentException('Diagnosis notes cannot be empty.');
                }
            }

            if (isset($data['procedure_cost'])) {
                $data['procedure_cost'] = $this->formatMoney($data['procedure_cost']);
            }

            $visit->update($data);
            return $visit->fresh()->load('pet.owner', 'veterinarian', 'treatments');
        });
    }
}