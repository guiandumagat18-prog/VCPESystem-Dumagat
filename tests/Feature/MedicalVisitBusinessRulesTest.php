<?php

namespace Tests\Feature; // <-- MUST match the folder name

use App\Models\Pet;
use App\Models\PetOwner;
use App\Models\MedicalVisit;
use App\Models\Treatment;
use App\Models\Veterinarian;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicalVisitBusinessRulesTest extends TestCase // <-- MUST match the file name
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

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

    public function test_visit_pages_render_as_inertia_pages(): void
    {
        $this->get(route('visits.create'))
            ->assertOk()
            ->assertInertia(fn (\Inertia\Testing\AssertableInertia $page) => $page
                ->component('visits/create')
                ->has('pets')
                ->has('veterinarians'));

        $this->get(route('visits.index'))
            ->assertInertia(fn (\Inertia\Testing\AssertableInertia $page) => $page
                ->component('visits/index')
                ->has('visits.data', 0));
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

    public function test_visit_edit_page_shows_the_visit_and_treatments(): void
    {
        $pet = $this->setupPet();
        $vet = Veterinarian::create(['name' => 'Dr. A']);
        $visit = MedicalVisit::create([
            'pet_id' => $pet->id,
            'veterinarian_id' => $vet->id,
            'examination_date' => '2026-01-01',
            'diagnosis' => 'Routine checkup',
            'procedure_cost' => 150,
            'payment_status' => 'pending',
        ]);
        Treatment::create([
            'visit_id' => $visit->id,
            'treatment_given' => 'Vaccine',
            'cost' => 150,
        ]);

        $this->get(route('visits.edit', $visit))
            ->assertOk()
            ->assertInertia(fn (\Inertia\Testing\AssertableInertia $page) => $page
                ->component('visits/edit')
                ->where('visit.id', $visit->id)
                ->has('visit.treatments', 1)
                ->has('pets', 1)
                ->has('veterinarians', 1));
    }

    public function test_visit_update_saves_fields_and_replaces_treatments(): void
    {
        $pet = $this->setupPet();
        $newPet = Pet::create([
            'owner_id' => $pet->owner_id,
            'name' => 'Poppy',
            'species' => 'Cat',
        ]);
        $vet = Veterinarian::create(['name' => 'Dr. A']);
        $newVet = Veterinarian::create(['name' => 'Dr. B']);
        $visit = MedicalVisit::create([
            'pet_id' => $pet->id,
            'veterinarian_id' => $vet->id,
            'examination_date' => '2026-01-01',
            'diagnosis' => 'Routine checkup',
            'procedure_cost' => 100,
            'payment_status' => 'pending',
        ]);
        $oldTreatment = Treatment::create([
            'visit_id' => $visit->id,
            'treatment_given' => 'Old treatment',
            'cost' => 100,
        ]);

        $this->put(route('visits.update', $visit), [
            'pet_id' => $newPet->id,
            'veterinarian_id' => $newVet->id,
            'examination_date' => '2026-02-02',
            'diagnosis' => 'Updated diagnosis',
            'procedure_cost' => 999,
            'payment_status' => 'paid',
            'treatments_submitted' => 1,
            'treatments' => [
                ['treatment_given' => 'New treatment', 'cost' => 75],
                ['treatment_given' => 'Follow-up', 'cost' => 25],
            ],
        ])->assertRedirect(route('visits.index'));

        $this->assertDatabaseHas('medical_visits', [
            'id' => $visit->id,
            'pet_id' => $newPet->id,
            'veterinarian_id' => $newVet->id,
            'diagnosis' => 'Updated diagnosis',
            'procedure_cost' => 100,
            'payment_status' => 'paid',
        ]);
        $this->assertSame(
            '2026-02-02',
            MedicalVisit::findOrFail($visit->id)->examination_date->format('Y-m-d'),
        );
        $this->assertDatabaseMissing('treatments', ['id' => $oldTreatment->id]);
        $this->assertDatabaseHas('treatments', [
            'visit_id' => $visit->id,
            'treatment_given' => 'New treatment',
            'cost' => 75,
        ]);
        $this->assertDatabaseHas('treatments', [
            'visit_id' => $visit->id,
            'treatment_given' => 'Follow-up',
            'cost' => 25,
        ]);
    }

    public function test_visit_update_can_remove_all_treatments(): void
    {
        $pet = $this->setupPet();
        $vet = Veterinarian::create(['name' => 'Dr. A']);
        $visit = MedicalVisit::create([
            'pet_id' => $pet->id,
            'veterinarian_id' => $vet->id,
            'examination_date' => '2026-01-01',
            'diagnosis' => 'Routine checkup',
            'procedure_cost' => 100,
            'payment_status' => 'pending',
        ]);
        Treatment::create([
            'visit_id' => $visit->id,
            'treatment_given' => 'Old treatment',
            'cost' => 100,
        ]);

        $this->put(route('visits.update', $visit), [
            'treatments_submitted' => 1,
            'procedure_cost' => 25,
        ])->assertRedirect(route('visits.index'));

        $this->assertDatabaseMissing('treatments', ['visit_id' => $visit->id]);
        $this->assertDatabaseHas('medical_visits', [
            'id' => $visit->id,
            'procedure_cost' => 25,
        ]);
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