<?php

namespace Tests\Feature; // <-- MUST match the folder name

use App\Models\Pet;
use App\Models\PetOwner;
use App\Models\Veterinarian;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicalVisitBusinessRulesTest extends TestCase // <-- MUST match the file name
{
    use RefreshDatabase;

    private function setupPet(): Pet
    {
        $owner = PetOwner::create(['name' => 'Test', 'phone' => '+639170000000']);
        return Pet::create([
            'owner_id' => $owner->id, 'name' => 'Buddy',
            'species' => 'Dog', 'breed' => 'Aspin',
        ]);
    }

    public function test_visit_requires_diagnosis(): void
    {
        $pet = $this->setupPet();
        $vet = Veterinarian::create(['name' => 'Dr. A']);

        $response = $this->post('/visits', [
            'pet_id' => $pet->id,
            'veterinarian_id' => $vet->id,
            'examination_date' => '2026-01-01',
            'diagnosis' => '',
            'procedure_cost' => 100,
            'payment_status' => 'pending',
        ]);

        $response->assertSessionHasErrors('diagnosis');
    }

    public function test_procedure_cost_equals_sum_of_treatments(): void
    {
        $pet = $this->setupPet();
        $vet = Veterinarian::create(['name' => 'Dr. A']);

        $this->post('/visits', [
            'pet_id' => $pet->id,
            'veterinarian_id' => $vet->id,
            'examination_date' => '2026-01-01',
            'diagnosis' => 'Mild fever, needs rest.',
            'procedure_cost' => 999,
            'payment_status' => 'pending',
            'treatments' => [
                ['treatment_given' => 'Antibiotic', 'cost' => 150],
                ['treatment_given' => 'Vitamin',    'cost' => 50],
            ],
        ]);

        $this->assertDatabaseHas('medical_visits', ['procedure_cost' => 200]);
    }

    public function test_species_is_trimmed(): void
    {
        $pet = $this->setupPet();

        $this->put("/pets/{$pet->id}", [
            'species' => '  Cat  ',
        ]);

        $this->assertDatabaseHas('pets', ['species' => 'Cat']);
    }
}