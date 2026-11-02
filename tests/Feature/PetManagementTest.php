<?php

namespace Tests\Feature;

use App\Models\PetOwner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PetManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_pet_pages_render(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get('/pets')
            ->assertInertia(fn (Assert $page) => $page
                ->component('pets/index')
                ->has('pets.data', 0));

        $this->get('/pets/create')
            ->assertInertia(fn (Assert $page) => $page
                ->component('pets/create'));
    }

    public function test_edit_pet_page_renders_with_pet_and_owner_options(): void
    {
        $this->actingAs(User::factory()->create());
        $owner = PetOwner::create(['name' => 'Jamie Rivera', 'phone' => '+639171234567']);
        $pet = $owner->pets()->create([
            'name' => 'Mochi',
            'species' => 'Dog',
            'breed' => 'Shih Tzu',
        ]);

        $this->get(route('pets.edit', $pet))
            ->assertInertia(fn (Assert $page) => $page
                ->component('pets/edit')
                ->where('pet.id', $pet->id)
                ->where('pet.owner_id', $owner->id)
                ->has('owners', 1)
                ->where('owners.0.name', $owner->name));
    }

    public function test_pet_form_creates_a_pet_and_redirects_to_the_directory(): void
    {
        $this->actingAs(User::factory()->create());
        $owner = PetOwner::create(['name' => 'Jamie Rivera', 'phone' => '+639171234567']);

        $this->post('/pets', [
            'owner_name' => $owner->name,
            'name' => 'Mochi',
            'species' => 'Dog',
            'breed' => 'Shih Tzu',
        ])->assertRedirect(route('pets.index'));

        $this->assertDatabaseHas('pets', [
            'owner_id' => $owner->id,
            'name' => 'Mochi',
            'species' => 'Dog',
            'breed' => 'Shih Tzu',
        ]);
    }

    public function test_pet_form_creates_a_new_owner_from_the_entered_name(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post('/pets', [
            'owner_name' => 'Taylor Reed',
            'name' => 'Poppy',
            'species' => 'Cat',
            'breed' => 'Tabby',
        ])->assertRedirect(route('pets.index'));

        $this->assertDatabaseHas('pet_owners', [
            'name' => 'Taylor Reed',
            'phone' => null,
        ]);
        $this->assertDatabaseHas('pets', [
            'name' => 'Poppy',
            'species' => 'Cat',
        ]);
        $this->assertDatabaseHas('pets', [
            'owner_id' => PetOwner::where('name', 'Taylor Reed')->value('id'),
            'name' => 'Poppy',
        ]);
    }

    public function test_pet_form_requires_an_owner_name(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post('/pets', [
            'name' => 'Poppy',
            'species' => 'Cat',
        ])->assertSessionHasErrors('owner_name');
    }

    public function test_pet_update_saves_changes_and_redirects_to_the_directory(): void
    {
        $this->actingAs(User::factory()->create());
        $owner = PetOwner::create(['name' => 'Jamie Rivera', 'phone' => '+639171234567']);
        $newOwner = PetOwner::create(['name' => 'Taylor Reed', 'phone' => null]);
        $pet = $owner->pets()->create([
            'name' => 'Mochi',
            'species' => 'Dog',
            'breed' => 'Shih Tzu',
        ]);

        $this->put(route('pets.update', $pet), [
            'owner_id' => $newOwner->id,
            'name' => 'Poppy',
            'species' => 'Cat',
            'breed' => 'Tabby',
        ])->assertRedirect(route('pets.index'));

        $this->assertDatabaseHas('pets', [
            'id' => $pet->id,
            'owner_id' => $newOwner->id,
            'name' => 'Poppy',
            'species' => 'Cat',
            'breed' => 'Tabby',
        ]);
    }
}
