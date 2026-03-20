<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        Course::create([
            'title' => 'DML - Join Tabel',
            'description' => 'Pada kelas ini, kita akan belajar menggabungkan baris dari dua atau lebih tabel yang berelasi berdasarkan kolom kunci (Primary/Foreign Key) guna memperoleh informasi.',
            'order' => 1,
        ]);

        Course::create([
            'title' => 'DML - SQL Bertingkat',
            'description' => 'Pada kelas ini, kita akan belajar menggunakan subquery untuk menyelesaikan permasalahan data yang kompleks.',
            'order' => 2,
        ]);

        Course::create([
            'title' => 'DML - Fungsi Agregasi',
            'description' => 'Pada kelas ini, kita akan belajar menggunakan fungsi agregasi seperti COUNT, SUM, AVG, MIN, dan MAX.',
            'order' => 3,
        ]);
    }
}