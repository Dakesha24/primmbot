<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE lesson_materials MODIFY COLUMN type ENUM('pendahuluan','petunjuk_belajar','tujuan','prasyarat','ringkasan_materi') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE lesson_materials MODIFY COLUMN type ENUM('pendahuluan','tujuan','prasyarat','ringkasan_materi') NOT NULL");
    }
};
