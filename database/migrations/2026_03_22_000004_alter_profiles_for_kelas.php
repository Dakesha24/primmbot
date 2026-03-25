<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn(['school_name', 'kelas', 'tahun_ajaran']);
        });

        Schema::table('profiles', function (Blueprint $table) {
            $table->foreignId('kelas_id')->nullable()->after('gender')->constrained('kelas')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropForeign(['kelas_id']);
            $table->dropColumn('kelas_id');
            $table->string('school_name')->nullable();
            $table->string('kelas')->nullable();
            $table->string('tahun_ajaran')->nullable();
        });
    }
};
