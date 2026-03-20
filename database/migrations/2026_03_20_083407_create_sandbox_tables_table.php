<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sandbox_tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sandbox_database_id')->constrained()->cascadeOnDelete();
            $table->string('table_name');     // nama tabel di primmbot_sandbox (misal: toko_buku__penerbit)
            $table->string('display_name');   // nama tampilan (misal: penerbit)
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sandbox_tables');
    }
};