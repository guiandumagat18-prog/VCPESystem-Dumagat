<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medical_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')
                  ->constrained('pets')
                  ->onDelete('cascade');
            $table->foreignId('veterinarian_id')
                  ->constrained('veterinarians')
                  ->onDelete('restrict');
            $table->date('examination_date')->index();
            $table->text('diagnosis');
            $table->decimal('procedure_cost', 10, 2)->default(0);
            $table->enum('payment_status', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_visits');
    }
};