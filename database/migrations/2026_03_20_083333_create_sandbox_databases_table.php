<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sandbox_databases', function (Blueprint $table) {
            $table->id();
            $table->string('name');           // "Toko Buku"
            $table->string('prefix')->unique(); // "toko_buku" — prefix tabel di primmbot_sandbox
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sandbox_databases');
    }
};