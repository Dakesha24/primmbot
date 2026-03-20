<?php

namespace Database\Seeders;

use App\Models\Activity;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        $chapterId = 1;

        // ==================== PREDICT ====================
        // ==================== PREDICT ====================
        Activity::create([
            'chapter_id' => $chapterId,
            'stage' => 'predict',
            'level' => null,
            'description' => '<p>Di bawah ini terdapat dua tabel dari sistem <strong>"Perpustakaan Digital"</strong>. Tugasmu adalah memprediksi hasil akhir dari sebuah perintah SQL tanpa menjalankannya di komputer.</p>
<div class="db-table">
    <p class="db-table-title">Tabel Buku</p>
    <table>
        <thead><tr><th>id_buku</th><th>judul_buku</th><th>id_penerbit</th></tr></thead>
        <tbody>
            <tr><td>B001</td><td>Harry Potter</td><td>P01</td></tr>
            <tr><td>B002</td><td>Laskar Pelangi</td><td>P02</td></tr>
            <tr><td>B003</td><td>Bumi Manusia</td><td>P03</td></tr>
            <tr><td>B004</td><td>Si Putih</td><td>P01</td></tr>
        </tbody>
    </table>
</div>
<div class="db-table">
    <p class="db-table-title">Tabel Penerbit</p>
    <table>
        <thead><tr><th>id_penerbit</th><th>nama_penerbit</th></tr></thead>
        <tbody>
            <tr><td>P01</td><td>Gramedia</td></tr>
            <tr><td>P02</td><td>Bentang Pustaka</td></tr>
            <tr><td>P03</td><td>Hasta Mitra</td></tr>
        </tbody>
    </table>
</div>',
            'question_text' => 'Amati potongan kode di bawah ini dengan teliti. Apa yang dilakukan dalam kode SQL tersebut? Berikan jawaban berdasarkan hasil analisis, tanpa menjalankan kode terlebih dahulu.',
            'code_snippet' => 'SELECT buku.judul_buku, penerbit.nama_penerbit
FROM buku
JOIN penerbit ON buku.id_penerbit = penerbit.id_penerbit;',
            'editor_default_code' => null,
            'expected_output' => null,
            'order' => 1,
        ]);

        // ==================== RUN ====================
        Activity::create([
            'chapter_id' => $chapterId,
            'stage' => 'run',
            'level' => null,
            'question_text' => 'Sekarang, coba bandingkan hasil output dengan jawaban prediksimu tadi. Apakah hasilnya sesuai? Jelaskan mengapa hasil output bisa berbeda (atau mungkin sama) dengan prediksimu. Bagian mana dari logika atau baris kode yang menyebabkan prediksimu kurang tepat?',
            'code_snippet' => 'SELECT buku.judul_buku, penerbit.nama_penerbit
FROM buku
JOIN penerbit ON buku.id_penerbit = penerbit.id_penerbit;',
            'editor_default_code' => null,
            'expected_output' => [
                ['judul_buku' => 'Harry Potter', 'nama_penerbit' => 'Gramedia'],
                ['judul_buku' => 'Laskar Pelangi', 'nama_penerbit' => 'Bentang Pustaka'],
                ['judul_buku' => 'Bumi Manusia', 'nama_penerbit' => 'Hasta Mitra'],
            ],
            'order' => 2,
        ]);

        // ==================== INVESTIGATE ====================
        // Level: Atoms
        Activity::create([
            'chapter_id' => $chapterId,
            'stage' => 'investigate',
            'level' => 'atoms',
            'description' => null,
            'question_text' => 'Perhatikan bagian `buku.judul_buku`. Menurut analisismu, apa fungsi dari tanda titik (`.`) yang memisahkan kata `buku` dan `judul_buku` tersebut?',
            'code_snippet' => 'SELECT buku.judul_buku, penerbit.nama_penerbit
FROM buku
JOIN penerbit ON buku.id_penerbit = penerbit.id_penerbit;',
            'editor_default_code' => null,
            'expected_output' => null,
            'order' => 3,
        ]);

        // Level: Blocks
        Activity::create([
            'chapter_id' => $chapterId,
            'stage' => 'investigate',
            'level' => 'blocks',
            'description' => null,
            'question_text' => 'Amati blok kode mulai dari kata `JOIN` hingga `ON`. Secara teknis, apa tugas utama dari blok perintah tersebut terhadap tabel `buku` dan `penerbit`?',
            'code_snippet' => 'SELECT buku.judul_buku, penerbit.nama_penerbit
FROM buku
JOIN penerbit ON buku.id_penerbit = penerbit.id_penerbit;',
            'editor_default_code' => null,
            'expected_output' => null,
            'order' => 4,
        ]);

        // Level: Relations
        Activity::create([
            'chapter_id' => $chapterId,
            'stage' => 'investigate',
            'level' => 'relations',
            'description' => null,
            'question_text' => 'Bagaimana hubungan antara kolom `id_penerbit` di tabel `buku` dengan `id_penerbit` di tabel `penerbit` sehingga data bisa tampil berdampingan secara akurat?',
            'code_snippet' => 'SELECT buku.judul_buku, penerbit.nama_penerbit
FROM buku
JOIN penerbit ON buku.id_penerbit = penerbit.id_penerbit;',
            'editor_default_code' => null,
            'expected_output' => null,
            'order' => 5,
        ]);

        // Level: Macro Structure
        Activity::create([
            'chapter_id' => $chapterId,
            'stage' => 'investigate',
            'level' => 'macro',
            'description' => null,
            'question_text' => 'Dalam sistem Perpustakaan Digital, mengapa petugas perpustakaan lebih membutuhkan hasil dari perintah `JOIN` ini dibandingkan hanya melihat tabel `buku` saja?',
            'code_snippet' => 'SELECT buku.judul_buku, penerbit.nama_penerbit
FROM buku
JOIN penerbit ON buku.id_penerbit = penerbit.id_penerbit;',
            'editor_default_code' => null,
            'expected_output' => null,
            'order' => 6,
        ]);

        // ==================== MODIFIED ====================
        // Level: Mudah
        Activity::create([
            'chapter_id' => $chapterId,
            'stage' => 'modified',
            'level' => 'mudah',
            'description' => 'Jelaskan bagian kode mana yang kamu ubah untuk melakukan penyaringan tersebut, dan mengapa kamu menggunakan perintah itu?',
            'question_text' => 'Ubahlah kode SQL tersebut agar hanya menampilkan kolom judul_buku saja, tanpa kolom nama_penerbit.',
            'code_snippet' => 'SELECT buku.judul_buku, penerbit.nama_penerbit
FROM buku
JOIN penerbit ON buku.id_penerbit = penerbit.id_penerbit;',
            'editor_default_code' => 'SELECT buku.judul_buku, penerbit.nama_penerbit
FROM buku
JOIN penerbit ON buku.id_penerbit = penerbit.id_penerbit;',
            'expected_output' => [
                ['judul_buku' => 'Harry Potter'],
                ['judul_buku' => 'Laskar Pelangi'],
                ['judul_buku' => 'Bumi Manusia'],
                ['judul_buku' => 'Si Putih'],
            ],
            'order' => 7,
        ]);

        // Level: Sedang
        Activity::create([
            'chapter_id' => $chapterId,
            'stage' => 'modified',
            'level' => 'sedang',
            'description' => 'Jelaskan bagian kode mana yang kamu tambahkan atau ubah, dan mengapa kamu menggunakan perintah tersebut?',
            'question_text' => 'Modifikasi query agar menampilkan judul_buku dan nama_penerbit, tetapi hanya untuk buku yang diterbitkan oleh "Gramedia".',
            'code_snippet' => 'SELECT buku.judul_buku, penerbit.nama_penerbit
FROM buku
JOIN penerbit ON buku.id_penerbit = penerbit.id_penerbit;',
            'editor_default_code' => 'SELECT buku.judul_buku, penerbit.nama_penerbit
FROM buku
JOIN penerbit ON buku.id_penerbit = penerbit.id_penerbit;',
            'expected_output' => [
                ['judul_buku' => 'Harry Potter', 'nama_penerbit' => 'Gramedia'],
                ['judul_buku' => 'Si Putih', 'nama_penerbit' => 'Gramedia'],
            ],
            'order' => 8,
        ]);

        // Level: Tantang
        Activity::create([
            'chapter_id' => $chapterId,
            'stage' => 'modified',
            'level' => 'tantang',
            'description' => 'Bagaimana caramu menentukan kolom mana yang menjadi penghubung (key) antar kedua tabel tersebut?',
            'question_text' => 'Modifikasi query agar menampilkan judul_buku, nama_penerbit, dan juga nama_penulis dari tabel penulis. Tabel penulis memiliki kolom id_penulis dan nama_penulis.',
            'code_snippet' => 'SELECT buku.judul_buku, penerbit.nama_penerbit
FROM buku
JOIN penerbit ON buku.id_penerbit = penerbit.id_penerbit;',
            'editor_default_code' => 'SELECT buku.judul_buku, penerbit.nama_penerbit
FROM buku
JOIN penerbit ON buku.id_penerbit = penerbit.id_penerbit;',
            'expected_output' => [
                ['judul_buku' => 'Harry Potter', 'nama_penerbit' => 'Gramedia', 'nama_penulis' => 'J.K. Rowling'],
                ['judul_buku' => 'Laskar Pelangi', 'nama_penerbit' => 'Bentang Pustaka', 'nama_penulis' => 'Andrea Hirata'],
                ['judul_buku' => 'Bumi Manusia', 'nama_penerbit' => 'Hasta Mitra', 'nama_penulis' => 'Pramoedya Ananta Toer'],
                ['judul_buku' => 'Si Putih', 'nama_penerbit' => 'Gramedia', 'nama_penulis' => 'Pramoedya Ananta Toer'],
            ],
            'order' => 9,
        ]);

        // ==================== MAKE ====================
        // Level: Mudah
        Activity::create([
            'chapter_id' => $chapterId,
            'stage' => 'make',
            'level' => 'mudah',
            'description' => 'Bagaimana caramu menentukan kolom mana yang menjadi penghubung (key) antar kedua tabel tersebut?',
            'question_text' => 'Buatlah perintah SQL untuk menampilkan nama pelanggan beserta tanggal transaksi yang pernah mereka lakukan!',
            'code_snippet' => null,
            'editor_default_code' => null,
            'expected_output' => [
                ['nama_pelanggan' => 'Andi', 'tanggal_transaksi' => '2024-01-15'],
                ['nama_pelanggan' => 'Budi', 'tanggal_transaksi' => '2024-02-20'],
                ['nama_pelanggan' => 'Citra', 'tanggal_transaksi' => '2024-03-10'],
            ],
            'order' => 10,
        ]);

        // Level: Sedang
        Activity::create([
            'chapter_id' => $chapterId,
            'stage' => 'make',
            'level' => 'sedang',
            'description' => 'Jelaskan mengapa kamu memilih kolom tersebut sebagai kondisi JOIN dan apa yang terjadi jika kondisi JOIN-nya salah?',
            'question_text' => 'Buatlah perintah SQL untuk menampilkan judul buku beserta nama penulis dari setiap buku!',
            'code_snippet' => null,
            'editor_default_code' => null,
            'expected_output' => [
                ['judul_buku' => 'Harry Potter', 'nama_penulis' => 'J.K. Rowling'],
                ['judul_buku' => 'Laskar Pelangi', 'nama_penulis' => 'Andrea Hirata'],
                ['judul_buku' => 'Bumi Manusia', 'nama_penulis' => 'Pramoedya Ananta Toer'],
                ['judul_buku' => 'Si Putih', 'nama_penulis' => 'Pramoedya Ananta Toer'],
            ],
            'order' => 11,
        ]);

        // Level: Tantang
        Activity::create([
            'chapter_id' => $chapterId,
            'stage' => 'make',
            'level' => 'tantang',
            'description' => 'Jelaskan langkah-langkah logika yang kamu gunakan untuk menyusun query ini dari awal hingga akhir.',
            'question_text' => 'Buatlah perintah SQL untuk menampilkan judul buku, nama penerbit, dan nama penulis dari setiap buku dalam satu hasil query!',
            'code_snippet' => null,
            'editor_default_code' => null,
            'expected_output' => [
                ['judul_buku' => 'Harry Potter', 'nama_penerbit' => 'Gramedia', 'nama_penulis' => 'J.K. Rowling'],
                ['judul_buku' => 'Laskar Pelangi', 'nama_penerbit' => 'Bentang Pustaka', 'nama_penulis' => 'Andrea Hirata'],
                ['judul_buku' => 'Bumi Manusia', 'nama_penerbit' => 'Hasta Mitra', 'nama_penulis' => 'Pramoedya Ananta Toer'],
                ['judul_buku' => 'Si Putih', 'nama_penerbit' => 'Gramedia', 'nama_penulis' => 'Pramoedya Ananta Toer'],
            ],
            'order' => 12,
        ]);
    }
}
