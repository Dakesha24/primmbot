<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->integer('score_keruntutan')->nullable()->after('score');
            $table->integer('score_berargumen')->nullable()->after('score_keruntutan');
            $table->integer('score_kesimpulan')->nullable()->after('score_berargumen');
            $table->integer('attempt')->default(1)->after('score_kesimpulan');
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn(['score_keruntutan', 'score_berargumen', 'score_kesimpulan', 'attempt']);
        });
    }
};
