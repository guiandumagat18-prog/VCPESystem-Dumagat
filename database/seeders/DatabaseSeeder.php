<?php

namespace Database\Seeders;

use App\Models\PetOwner;
use App\Models\Veterinarian;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        PetOwner::create(['name' => 'Juan Dela Cruz', 'phone' => '+639171234567']);
        PetOwner::create(['name' => 'Maria Santos',   'phone' => '+639187654321']);

        Veterinarian::create(['name' => 'Dr. Reyes',  'specialization' => 'General']);
        Veterinarian::create(['name' => 'Dr. Cruz',   'specialization' => 'Surgery']);
    }
}