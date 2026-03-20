<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SandboxSeeder extends Seeder
{
    public function run(): void
    {
        $db = DB::connection('sandbox');

        // === Penerbit ===
        $db->table('penerbit')->insert([
            ['id_penerbit' => 'P01', 'nama_penerbit' => 'Gramedia'],
            ['id_penerbit' => 'P02', 'nama_penerbit' => 'Bentang Pustaka'],
            ['id_penerbit' => 'P03', 'nama_penerbit' => 'Hasta Mitra'],
        ]);

        // === Penulis ===
        $db->table('penulis')->insert([
            ['id_penulis' => 'A01', 'nama_penulis' => 'J.K. Rowling'],
            ['id_penulis' => 'A02', 'nama_penulis' => 'Andrea Hirata'],
            ['id_penulis' => 'A03', 'nama_penulis' => 'Pramoedya Ananta Toer'],
        ]);

        // === Buku ===
        $db->table('buku')->insert([
            ['id_buku' => 'B001', 'judul_buku' => 'Harry Potter', 'id_penerbit' => 'P01', 'id_penulis' => 'A01'],
            ['id_buku' => 'B002', 'judul_buku' => 'Laskar Pelangi', 'id_penerbit' => 'P02', 'id_penulis' => 'A02'],
            ['id_buku' => 'B003', 'judul_buku' => 'Bumi Manusia', 'id_penerbit' => 'P03', 'id_penulis' => 'A03'],
            ['id_buku' => 'B004', 'judul_buku' => 'Si Putih', 'id_penerbit' => 'P01', 'id_penulis' => 'A03'],
        ]);

        // === Pelanggan ===
        $db->table('pelanggan')->insert([
            ['id_pelanggan' => 1, 'nama_pelanggan' => 'Andi'],
            ['id_pelanggan' => 2, 'nama_pelanggan' => 'Budi'],
            ['id_pelanggan' => 3, 'nama_pelanggan' => 'Citra'],
        ]);

        // === Transaksi ===
        $db->table('transaksi')->insert([
            ['id_transaksi' => 1, 'id_pelanggan' => 1, 'tanggal_transaksi' => '2024-01-15'],
            ['id_transaksi' => 2, 'id_pelanggan' => 2, 'tanggal_transaksi' => '2024-02-20'],
            ['id_transaksi' => 3, 'id_pelanggan' => 3, 'tanggal_transaksi' => '2024-03-10'],
        ]);
    }
}