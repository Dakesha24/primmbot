<?php

namespace Database\Seeders;

use App\Models\Chapter;
use Illuminate\Database\Seeder;

class ChapterSeeder extends Seeder
{
    public function run(): void
    {
        // Course 1: DML - Join Tabel (3 sub materi)
        Chapter::create([
            'course_id' => 1,
            'title' => 'Equi Join',
            'description' => 'Mempelajari konsep Equi Join, yaitu teknik dasar menggabungkan data dari beberapa tabel menggunakan operator sama dengan (=).',
            'order' => 1,
        ]);

        Chapter::create([
            'course_id' => 1,
            'title' => 'Inner Join',
            'description' => 'Mempelajari Inner Join untuk menggabungkan baris dari dua tabel yang memiliki nilai cocok di kedua tabel.',
            'order' => 2,
        ]);

        Chapter::create([
            'course_id' => 1,
            'title' => 'Left & Right Join',
            'description' => 'Mempelajari Left Join dan Right Join yang menampilkan semua data dari salah satu tabel meskipun tidak ada pasangan di tabel lain.',
            'order' => 3,
        ]);

        // Course 2: DML - SQL Bertingkat (3 sub materi placeholder)
        Chapter::create([
            'course_id' => 2,
            'title' => 'Subquery pada WHERE',
            'description' => 'Mempelajari penggunaan subquery di dalam klausa WHERE.',
            'order' => 1,
        ]);

        Chapter::create([
            'course_id' => 2,
            'title' => 'Subquery pada FROM',
            'description' => 'Mempelajari penggunaan subquery sebagai tabel sementara di dalam klausa FROM.',
            'order' => 2,
        ]);

        Chapter::create([
            'course_id' => 2,
            'title' => 'Correlated Subquery',
            'description' => 'Mempelajari subquery yang bergantung pada query luar (correlated).',
            'order' => 3,
        ]);

        // Course 3: DML - Fungsi Agregasi (3 sub materi placeholder)
        Chapter::create([
            'course_id' => 3,
            'title' => 'COUNT dan SUM',
            'description' => 'Mempelajari fungsi COUNT untuk menghitung jumlah baris dan SUM untuk menjumlahkan nilai.',
            'order' => 1,
        ]);

        Chapter::create([
            'course_id' => 3,
            'title' => 'AVG, MIN, MAX',
            'description' => 'Mempelajari fungsi AVG untuk rata-rata, MIN untuk nilai terkecil, dan MAX untuk nilai terbesar.',
            'order' => 2,
        ]);

        Chapter::create([
            'course_id' => 3,
            'title' => 'GROUP BY dan HAVING',
            'description' => 'Mempelajari pengelompokan data dengan GROUP BY dan filter kelompok dengan HAVING.',
            'order' => 3,
        ]);
    }
}