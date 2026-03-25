# Fitur Download Hasil Belajar (PDF & Excel)

## Gambaran Umum

Siswa dapat mengunduh laporan hasil belajar mereka dari halaman detail hasil belajar (`/hasil-belajar/{course}`) dalam dua format: **PDF** dan **Excel (.xlsx)**. Tombol download muncul di pojok kanan atas, sejajar dengan tombol "Kembali".

---

## Package yang Digunakan

| Package | Versi | Fungsi |
|---|---|---|
| `barryvdh/laravel-dompdf` | ^3.1 | Menghasilkan file PDF dari Blade view |
| `maatwebsite/excel` | ^3.1 | Menghasilkan file Excel (.xlsx) menggunakan PhpSpreadsheet |

Kedua package di-install dengan:
```
composer require barryvdh/laravel-dompdf "maatwebsite/excel:^3.1" --ignore-platform-req=ext-gd --ignore-platform-req=ext-zip
```

> Flag `--ignore-platform-req` diperlukan karena PHP CLI (XAMPP) tidak mengaktifkan ekstensi `ext-gd` dan `ext-zip`, meskipun keduanya sudah aktif di PHP yang dijalankan Laragon (web server). Saat diakses via browser, ekstensi tersebut tersedia dan fitur berfungsi normal.

---

## Alur Kerja

### 1. Siswa klik tombol di halaman show

```
/hasil-belajar/{course}/pdf    → HasilBelajarController::downloadPdf()
/hasil-belajar/{course}/excel  → HasilBelajarController::downloadExcel()
```

### 2. Controller mempersiapkan data (`prepareShowData`)

Kedua method download menggunakan method privat `prepareShowData(Course $course)` agar tidak mengulang logika yang sama. Method ini:

1. Memastikan siswa terdaftar di course tersebut (`abort_unless 403`)
2. Memuat relasi: `kelas`, `school`, `chapters`, `activities` (diurutkan)
3. Mengambil semua `submissions` milik siswa untuk activities di course ini
4. Melakukan `keyBy('activity_id')` agar mudah dicari per activity
5. Menyematkan `my_submission` ke tiap activity object
6. Menghitung statistik: total soal, selesai, persen, rata-rata skor

Data yang dikembalikan: `['course', 'stats', 'submissions']`

### 3a. Download PDF

```php
// HasilBelajarController::downloadPdf()
$data = $this->prepareShowData($course);
$pdf  = Pdf::loadView('pdf.hasil-belajar', $data)->setPaper('a4', 'portrait');
return $pdf->download('hasil-belajar-{slug}.pdf');
```

- Facade `Pdf` dari `barryvdh/laravel-dompdf` memuat Blade view `resources/views/pdf/hasil-belajar.blade.php`
- Template PDF menggunakan **light theme** (putih) karena dompdf tidak mendukung CSS kompleks dark mode
- Layout: header → statistik ringkasan → per bab → per tahap PRIMM → per soal (pertanyaan + jawaban + feedback AI)
- Font yang digunakan: `DejaVu Sans` (bawaan dompdf, mendukung karakter Indonesia)

### 3b. Download Excel

```php
// HasilBelajarController::downloadExcel()
$data = $this->prepareShowData($course);
return Excel::download(new HasilBelajarExport($course, $data['submissions']), 'hasil-belajar-{slug}.xlsx');
```

- Facade `Excel` dari `maatwebsite/excel` menerima sebuah **Export class**
- Export class: `app/Exports/HasilBelajarExport.php`

---

## Export Class: `HasilBelajarExport`

File: `app/Exports/HasilBelajarExport.php`

Mengimplementasikan beberapa interface dari maatwebsite/excel:

| Interface | Fungsi |
|---|---|
| `FromCollection` | Menyediakan data baris via method `collection()` |
| `WithHeadings` | Mendefinisikan baris header kolom |
| `WithTitle` | Menentukan nama sheet di Excel |
| `WithStyles` | Memberi styling pada baris header (warna biru tua, teks putih) |
| `ShouldAutoSize` | Otomatis menyesuaikan lebar kolom dengan isi |

### Kolom yang dihasilkan

| # | Kolom | Sumber Data |
|---|---|---|
| 1 | Bab | `chapter->title` |
| 2 | Tahap | `activity->stage` (ucfirst) |
| 3 | Level | `activity->level` atau `-` |
| 4 | Pertanyaan | `activity->question_text` (strip_tags) |
| 5 | Status | Benar / Belum Benar / Belum Dikerjakan |
| 6 | Skor | `submission->score` atau `-` |
| 7 | Jawaban | `submission->answer_text` atau `-` |
| 8 | Kode SQL | `submission->answer_code` atau `-` |
| 9 | Feedback AI | `submission->ai_feedback` atau `-` |
| 10 | Tanggal | `submission->updated_at` format `d/m/Y H:i` |

Urutan baris mengikuti urutan: chapter → stage (predict → run → investigate → modified → make) → activity.

---

## Template PDF: `resources/views/pdf/hasil-belajar.blade.php`

Template khusus untuk dompdf, **terpisah** dari Blade view halaman web. Alasannya:

- dompdf hanya mendukung subset CSS (tidak ada flexbox, grid terbatas, tidak ada CSS variable)
- Warna harus light theme agar mudah dibaca saat dicetak
- Layout disederhanakan: tidak ada sidebar, animasi, atau JavaScript

### Struktur template

```
Header (judul course, info kelas, tanggal cetak)
  ↓
Stats Row (progress %, selesai X/Y, rata-rata skor)
  ↓
Per Chapter:
  └── Judul Bab (background biru tua)
      └── Per Stage (background abu-abu muda)
          └── Per Activity:
              ├── Meta (level, status, skor)
              ├── Pertanyaan
              ├── Kotak jawaban (teks + kode SQL)
              └── Kotak feedback AI (border kiri biru)
Footer (nama aplikasi)
```

---

## Route

Kedua route didaftarkan di dalam middleware group `auth` + `student` yang sama dengan route hasil belajar lainnya:

```php
Route::get('/hasil-belajar/{course}/pdf',   [HasilBelajarController::class, 'downloadPdf'])->name('hasil-belajar.pdf');
Route::get('/hasil-belajar/{course}/excel', [HasilBelajarController::class, 'downloadExcel'])->name('hasil-belajar.excel');
```

Keduanya menggunakan Laravel Route Model Binding — `{course}` otomatis di-resolve menjadi instance `Course`.

---

## Keamanan

- Kedua endpoint berada di dalam middleware `auth` — hanya pengguna yang login yang bisa mengakses
- `prepareShowData()` memanggil `abort_unless(enrollments...)` — siswa hanya bisa download course yang **ia sendiri ikuti**, bukan course orang lain
- Nama file menggunakan `str()->slug()` untuk menghindari karakter berbahaya di nama file

---

## File yang Terlibat

```
app/
├── Exports/
│   └── HasilBelajarExport.php          ← Export class untuk Excel
├── Http/Controllers/
│   └── HasilBelajarController.php      ← Method downloadPdf, downloadExcel, prepareShowData

resources/views/
├── pdf/
│   └── hasil-belajar.blade.php         ← Template khusus PDF (light theme)
├── pages/hasil-belajar/
│   └── show.blade.php                  ← Tombol download ditambahkan di sini

routes/
└── web.php                             ← 2 route baru ditambahkan
```
