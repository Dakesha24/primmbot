<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL: change enum to string requires raw alter
        DB::statement('ALTER TABLE activities MODIFY level VARCHAR(20) NULL');

        // Renumber existing modified/make activities per chapter menjadi 1, 2, 3, dst
        foreach (['modified', 'make'] as $stage) {
            $chapters = DB::table('activities')
                ->where('stage', $stage)
                ->select('chapter_id')
                ->distinct()
                ->pluck('chapter_id');

            foreach ($chapters as $chapterId) {
                $acts = DB::table('activities')
                    ->where('stage', $stage)
                    ->where('chapter_id', $chapterId)
                    ->orderBy('order')
                    ->get();

                $counter = 1;
                foreach ($acts as $act) {
                    DB::table('activities')
                        ->where('id', $act->id)
                        ->update(['level' => (string) $counter]);
                    $counter++;
                }
            }
        }
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE activities MODIFY level ENUM('atoms','blocks','relations','macro','mudah','sedang','tantang') NULL");
    }
};
