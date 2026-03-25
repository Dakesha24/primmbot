<?php

namespace Database\Seeders;

use App\Models\LessonMaterial;
use Illuminate\Database\Seeder;

class LessonMaterialSeeder extends Seeder
{
    public function run(): void
    {
        $chapterId = 1;

        LessonMaterial::create([
            'chapter_id' => $chapterId,
            'type' => 'pendahuluan',
            'content' => '<h3>Selamat Datang di Sesi Join Tabel!</h3>
<p>Halo! Pada sesi ini, kamu akan mempelajari konsep <strong>Equi Join</strong>, yaitu teknik dasar untuk menggabungkan data dari beberapa tabel di database agar menjadi informasi yang utuh dan bermakna.</p>
<p>Silakan perhatikan detail sesi di bawah ini:</p>
<ul>
    <li>Nama Sesi: Chapter 1 – Equi Join</li>
    <li>Alokasi Waktu: 2×45 menit</li>
</ul>
<p>Pastikan kamu membaca Petunjuk Belajar, Tujuan, Prasyarat, dan Ringkasan Materi pada tab tersedia sebelum melanjutkan ke tahap Predict. Selamat belajar!</p>',
            'order' => 1,
        ]);

        LessonMaterial::create([
            'chapter_id' => $chapterId,
            'type' => 'petunjuk_belajar',
            'content' => '<h3>Petunjuk Belajar</h3>
<p>Ikuti langkah-langkah berikut agar proses belajarmu berjalan efektif:</p>
<ol>
    <li><strong>Baca materi pendahuluan</strong> terlebih dahulu untuk memahami konteks sesi ini.</li>
    <li><strong>Pahami tujuan pembelajaran</strong> agar kamu tahu apa yang diharapkan setelah menyelesaikan sesi.</li>
    <li><strong>Pastikan prasyarat tools</strong> sudah terpenuhi (laptop, browser, koneksi internet).</li>
    <li><strong>Baca ringkasan materi</strong> untuk memahami konsep dasar Equi Join sebelum mulai mengerjakan.</li>
    <li><strong>Kerjakan tahap PRIMM secara berurutan</strong>: Predict → Run → Investigate → Modify → Make.</li>
    <li><strong>Gunakan Virtual Assistant</strong> jika kamu membutuhkan petunjuk atau penjelasan tambahan. Asisten tidak akan memberikan jawaban langsung, tapi akan membimbingmu berpikir.</li>
    <li><strong>Klik tombol "Cek"</strong> sebelum Submit untuk mendapatkan feedback dan memperbaiki jawabanmu.</li>
    <li><strong>Jangan terburu-buru.</strong> Pahami setiap tahap dengan baik sebelum lanjut ke tahap berikutnya.</li>
</ol>',
            'order' => 2,
        ]);

        LessonMaterial::create([
            'chapter_id' => $chapterId,
            'type' => 'tujuan',
            'content' => '<h3>Tujuan Pembelajaran</h3>
<p>Setelah menyelesaikan sesi Equi Join ini, kamu diharapkan mampu:</p>
<ol>
    <li>Menjelaskan konsep dasar dan fungsi Equi Join dalam penggabungan tabel database.</li>
    <li>Menganalisis hubungan antara Primary Key dan Foreign Key saat melakukan penggabungan data.</li>
    <li>Menulis sintaks SQL Equi Join untuk mengambil informasi dari dua tabel atau lebih dengan akurat.</li>
    <li>Memecahkan masalah pengelolaan data melalui pembuatan query mandiri pada tahap "Make".</li>
</ol>',
            'order' => 3,
        ]);

        LessonMaterial::create([
            'chapter_id' => $chapterId,
            'type' => 'prasyarat',
            'content' => '<h3>Prasyarat Tools</h3>
<p>Agar kamu bisa belajar dengan nyaman dan lancar, pastikan hal-hal berikut sudah siap:</p>
<ol>
    <li><strong>Komputer atau Laptop:</strong> Sangat disarankan menggunakan komputer/laptop agar kamu dapat mempraktikkan perintah SQL dengan mudah dan melihat struktur tabel dengan jelas di layar yang lebih luas.</li>
    <li><strong>Web Browser:</strong> Gunakan Google Chrome atau Mozilla Firefox versi terbaru agar fitur editor SQL interaktif dan feedback AI berjalan optimal.</li>
    <li><strong>Koneksi Internet:</strong> Dibutuhkan untuk menjalankan query secara real-time dan mendapatkan bantuan dari asisten AI melalui sistem RAG.</li>
</ol>',
            'order' => 4,
        ]);

        LessonMaterial::create([
            'chapter_id' => $chapterId,
            'type' => 'ringkasan_materi',
            'content' => '<h3>Dasar Equi Join</h3>
<p><strong>Equi Join</strong> adalah jenis JOIN yang menggabungkan dua tabel berdasarkan kesamaan nilai pada kolom tertentu menggunakan operator sama dengan (=). Biasanya kolom yang digunakan adalah Primary Key di satu tabel dan Foreign Key di tabel lainnya.</p>
<h4>Sintaks Dasar:</h4>
<pre><code>SELECT kolom1, kolom2
FROM tabel1
JOIN tabel2 ON tabel1.kolom_kunci = tabel2.kolom_kunci;</code></pre>
<h4>Contoh:</h4>
<p>Misalkan kita punya tabel <code>buku</code> dan tabel <code>penerbit</code>:</p>
<pre><code>SELECT buku.judul_buku, penerbit.nama_penerbit
FROM buku
JOIN penerbit ON buku.id_penerbit = penerbit.id_penerbit;</code></pre>
<p>Query di atas akan menampilkan judul buku beserta nama penerbitnya, dengan mencocokkan <code>id_penerbit</code> di kedua tabel.</p>',
            'order' => 5,
        ]);
    }
}