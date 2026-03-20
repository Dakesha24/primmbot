<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('lesson_material_id')->constrained('lesson_materials')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['user_id', 'lesson_material_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_completions');
    }
};