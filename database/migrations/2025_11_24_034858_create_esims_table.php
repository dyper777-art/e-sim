<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('esims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('esim_plan_id')->constrained('esim_plans')->cascadeOnDelete();
            $table->string('phone_number', 20)->unique();
            $table->string('assigned_to')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('esims');
    }
};

