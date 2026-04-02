<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'username' => 'admin',
            'email' => 'admin@primmbase.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        Profile::create([
            'user_id' => $admin->id,
            'full_name' => 'Administrator',
        ]);

        $student = User::create([
            'username' => 'siswa01',
            'email' => 'siswa01@primmbase.test',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);

        Profile::create([
            'user_id' => $student->id,
            'full_name' => 'Danis Keysara Saputra',
            'nim' => '2100001',
            'school_name' => 'Universitas Pendidikan Indonesia',
        ]);
    }
}