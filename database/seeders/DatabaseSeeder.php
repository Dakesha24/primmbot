<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            CourseSeeder::class,
            ChapterSeeder::class,
            LessonMaterialSeeder::class,
            ActivitySeeder::class,
        ]);
    }
}