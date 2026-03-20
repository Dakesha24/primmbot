<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'sandbox';

    public function up(): void
    {
        // Tabel Penerbit
        Schema::connection('sandbox')->create('penerbit', function (Blueprint $table) {
            $table->string('id_penerbit')->primary();
            $table->string('nama_penerbit');
        });

        // Tabel Penulis
        Schema::connection('sandbox')->create('penulis', function (Blueprint $table) {
            $table->string('id_penulis')->primary();
            $table->string('nama_penulis');
        });

        // Tabel Buku
        Schema::connection('sandbox')->create('buku', function (Blueprint $table) {
            $table->string('id_buku')->primary();
            $table->string('judul_buku');
            $table->string('id_penerbit');
            $table->string('id_penulis');
        });

        // Tabel Pelanggan (untuk soal Make)
        Schema::connection('sandbox')->create('pelanggan', function (Blueprint $table) {
            $table->integer('id_pelanggan')->primary();
            $table->string('nama_pelanggan');
        });

        // Tabel Transaksi (untuk soal Make)
        Schema::connection('sandbox')->create('transaksi', function (Blueprint $table) {
            $table->integer('id_transaksi')->primary();
            $table->integer('id_pelanggan');
            $table->date('tanggal_transaksi');
        });
    }

    public function down(): void
    {
        Schema::connection('sandbox')->dropIfExists('transaksi');
        Schema::connection('sandbox')->dropIfExists('pelanggan');
        Schema::connection('sandbox')->dropIfExists('buku');
        Schema::connection('sandbox')->dropIfExists('penulis');
        Schema::connection('sandbox')->dropIfExists('penerbit');
    }
};