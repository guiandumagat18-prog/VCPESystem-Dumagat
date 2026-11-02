<?php

namespace Tests\Feature;

use App\Models\MedicalVisit;
use App\Models\PetOwner;
use App\Models\User;
use App\Models\Veterinarian;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_displays_clinic_counts_and_recent_visits(): void
    {
        $this->actingAs(User::factory()->create());

        $owner = PetOwner::create(['name' => 'Jamie Rivera', 'phone' => null]);
        $pet = $owner->pets()->create(['name' => 'Mochi', 'species' => 'Dog']);
        $veterinarian = Veterinarian::create(['name' => 'Dr. Reyes']);
        $visit = MedicalVisit::create([
            'pet_id' => $pet->id,
            'veterinarian_id' => $veterinarian->id,
            'examination_date' => '2026-11-02',
            'diagnosis' => 'Routine checkup',
            'procedure_cost' => 500,
            'payment_status' => 'paid',
        ]);

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('dashboard')
                ->where('stats.pets', 1)
                ->where('stats.owners', 1)
                ->where('stats.veterinarians', 1)
                ->where('stats.visits', 1)
                ->has('recentVisits', 1)
                ->where('recentVisits.0.id', $visit->id)
                ->where('recentVisits.0.pet.name', 'Mochi')
                ->where('recentVisits.0.veterinarian.name', 'Dr. Reyes'));
    }

    public function test_dashboard_shows_empty_data_when_no_clinic_records_exist(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get(route('dashboard'))
            ->assertInertia(fn (Assert $page) => $page
                ->component('dashboard')
                ->where('stats.pets', 0)
                ->where('stats.owners', 0)
                ->where('stats.veterinarians', 0)
                ->where('stats.visits', 0)
                ->has('recentVisits', 0));
    }
}
