<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Expand enum to include both values
        DB::statement("ALTER TABLE activities MODIFY COLUMN stage ENUM('predict','run','investigate','modified','modify','make') NOT NULL");

        // Step 2: Update existing data
        DB::table('activities')->where('stage', 'modified')->update(['stage' => 'modify']);

        // Step 3: Remove old value from enum
        DB::statement("ALTER TABLE activities MODIFY COLUMN stage ENUM('predict','run','investigate','modify','make') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE activities MODIFY COLUMN stage ENUM('predict','run','investigate','modified','modify','make') NOT NULL");

        DB::table('activities')->where('stage', 'modify')->update(['stage' => 'modified']);

        DB::statement("ALTER TABLE activities MODIFY COLUMN stage ENUM('predict','run','investigate','modified','make') NOT NULL");
    }
};
