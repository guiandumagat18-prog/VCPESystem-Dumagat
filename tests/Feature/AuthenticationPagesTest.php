<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AuthenticationPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_and_registration_pages_are_available_to_guests(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('auth/login'));

        $this->get('/register')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('auth/register'));
    }

    public function test_homepage_redirects_to_the_dashboard(): void
    {
        $this->get('/')->assertRedirect(route('dashboard'));
    }

    public function test_guests_are_redirected_to_login_from_the_pet_directory(): void
    {
        $this->get('/pets')->assertRedirect(route('login'));
        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    public function test_registration_signs_in_the_new_user_and_opens_the_dashboard(): void
    {
        $this->post('/register', [
            'name' => 'Clinic User',
            'email' => 'clinic@example.com',
            'password' => 'StrongPassword123!',
            'password_confirmation' => 'StrongPassword123!',
        ])->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'name' => 'Clinic User',
            'email' => 'clinic@example.com',
        ]);
        $this->assertAuthenticatedAs(User::where('email', 'clinic@example.com')->firstOrFail());
        $this->get('/dashboard')->assertOk();
    }

    public function test_login_redirects_to_the_dashboard(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }
}
