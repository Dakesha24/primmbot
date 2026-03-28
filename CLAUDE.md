# PRIMMBOT — Panduan Proyek untuk Claude

## 1. Identitas Proyek

- **Nama:** PRIMMBOT
- **Tujuan:** Platform e-LKPD (Lembar Kerja Peserta Didik digital) untuk meningkatkan *Logical Thinking* siswa SMK pada materi Basis Data (SQL Join & DCL)
- **Model Pembelajaran:** PRIMM — Predict, Run, Investigate, Modify, Make
- **Konsep AI:** Virtual Assistant berperan sebagai MKO (*More Knowledgeable Other*) menggunakan strategi **Scaffolding** (membimbing lewat pertanyaan pemantik, tidak pernah memberi jawaban langsung)
- **Catatan penting:** Scaffolding & Fading ditangani oleh **struktur tahapan PRIMM** itu sendiri (Predict → Run → Investigate → Modify → Make). AI assistant bukan mekanisme fading — tugasnya murni membimbing siswa dalam satu tahap yang sedang dikerjakan
- **Target Pengguna:** Siswa SMKN 4 Bandung, kelas XI PPLG 1/2/3
- **Indikator Logical Thinking yang diukur:** Keruntutan Berpikir, Kemampuan Berargumen, Penarikan Kesimpulan

---

## 2. Tech Stack

| Komponen | Detail |
|---|---|
| Framework | Laravel 12.x + Blade |
| PHP | 8.3 |
| Server Lokal | Laragon (Windows) |
| DB Utama | MySQL — `primmbot` |
| DB Sandbox | MySQL — `primmbot_sandbox` (koneksi `sandbox`, modifikasi siswa di-rollback) |
| DB Vektor | Supabase + pgvector **(BELUM)** |
| LLM | Gemini 1.5 Flash API **(BELUM)** |
| Auth | Laravel Breeze (custom) + Google OAuth (Socialite) |
| Frontend Siswa | Blade + Vanilla CSS dark theme, Plus Jakarta Sans. **TIDAK pakai Tailwind compile** |
| Frontend Admin | Blade + Vanilla CSS light theme (bg `#f0f2f7`, topbar biru tua `#0f1b3d`, sidebar putih), Plus Jakarta Sans |
| Rich Text Editor | Quill.js v2 (CDN) + quill-resize-image plugin |
| ERD | Mermaid.js v10 (client-side, dark theme, saat ini hard-coded) |
| Admin Panel | Custom (Filament di-uninstall) |

---

## 3. Database MySQL — `primmbot`

### `users`
`id` | `username` (unique) | `email` (unique) | `password` (nullable) | `role` enum(admin, student) | `is_active` boolean default true | `google_id` (nullable) | `email_verified_at` | `remember_token` | timestamps

- Accessor: `getNameAttribute()` → `profile->full_name ?? username`
- Methods: `isAdmin()`, `isStudent()`
- Relasi: `profile()` hasOne, `submissions()` hasMany

### `profiles`
`id` | `user_id` FK | `full_name` | `nim` (nullable) | `gender` enum(Laki-laki, Perempuan) nullable | `kelas` (nullable) | `school_name` (nullable, fixed SMKN 4 Bandung) | `tahun_ajaran` (nullable) | `avatar` (nullable) | timestamps

- Method: `isComplete()` → cek 5 field wajib

### `courses`
`id` | `title` | `description` (nullable) | `order` | timestamps

- Relasi: `chapters()` hasMany orderBy('order'), `activities()` hasManyThrough(Activity, Chapter)

### `chapters`
`id` | `course_id` FK nullable | `title` | `description` (nullable) | `order` | timestamps

- Relasi: `course()` belongsTo, `lessonMaterials()` hasMany, `activities()` hasMany

### `lesson_materials`
`id` | `chapter_id` FK | `type` enum(pendahuluan, petunjuk_belajar, tujuan, prasyarat, ringkasan_materi) | `content` longText HTML | `order` | timestamps

### `activities`
`id` | `chapter_id` FK | `sandbox_database_id` FK nullable | `description` longText nullable | `stage` enum(predict, run, investigate, modify, make) | `level` enum(atoms, blocks, relations, macro, mudah, sedang, tantang) nullable | `question_text` text | `code_snippet` text nullable | `editor_default_code` text nullable | `expected_output` JSON nullable | `kkm` integer default 70 | `order` | timestamps

- Cast: `expected_output` → array
- Relasi: `sandboxDatabase()` belongsTo
- `kkm` = nilai minimum untuk dinyatakan lulus per aktivitas (default 70, dapat diatur guru dari admin)

**Penggunaan field per stage:**

| Field | predict | run | investigate | modified | make |
|---|---|---|---|---|---|
| `description` | deskripsi soal + tabel HTML | — | — | pertanyaan penjelasan | pertanyaan penjelasan |
| `question_text` | pertanyaan prediksi | pertanyaan refleksi | pertanyaan analisis | perintah SQL | perintah SQL |
| `code_snippet` | kode SQL read-only | kode SQL editable | kode SQL editable | — | — |
| `editor_default_code` | — | — | — | kode terisi (modifikasi) | — (kosong) |
| `expected_output` | — | — | — | JSON output benar | JSON output benar |
| `sandbox_database_id` | opsional | ya | ya | ya | ya |
| `level` | — | — | atoms/blocks/relations/macro | mudah/sedang/tantang | mudah/sedang/tantang |

### `submissions`
`id` | `user_id` FK | `activity_id` FK | `answer_text` nullable | `answer_code` nullable | `is_correct` boolean | `score` integer nullable | `score_keruntutan` integer nullable | `score_berargumen` integer nullable | `score_kesimpulan` integer nullable | `attempt` integer default 1 | `ai_feedback` text nullable | timestamps

- **Tidak ada unique constraint** pada pasangan `user_id` + `activity_id` — siswa boleh submit berkali-kali hingga memenuhi KKM
- `attempt` = urutan percobaan ke-berapa (auto-increment per siswa per aktivitas)
- `score` = skor total (dihitung oleh PHP dari rata-rata tiga indikator, bukan dari AI)
- `score_keruntutan`, `score_berargumen`, `score_kesimpulan` = skor per indikator logical thinking (0–100), digunakan untuk analisis data penelitian
- `is_correct` = true jika `score >= activity.kkm`
- Query "submission terakhir": `->where(user_id, activity_id)->orderBy('attempt','desc')->first()`

### `material_completions`
`id` | `user_id` FK | `lesson_material_id` FK | timestamps | UNIQUE pair

### `ai_interaction_logs`
`id` | `user_id` FK | `activity_id` FK | `type` enum(chat, submit) default chat | `prompt_sent` | `response_received` | `tokens_used` nullable | `response_time` decimal nullable | timestamps

- `type = 'chat'` → interaksi percakapan bebas siswa dengan VA
- `type = 'submit'` → interaksi evaluasi saat siswa klik Submit
- Riwayat percakapan chat direkonstruksi dari tabel ini (filter `type = 'chat'`, urut `created_at`)
- Pemisahan type penting agar log submit tidak ikut masuk sebagai history percakapan

### `teacher_reviews`
`id` | `submission_id` FK | `teacher_id` FK | `score` nullable | `feedback` nullable | timestamps

### `sandbox_databases`
`id` | `name` | `prefix` (unique) | `description` (nullable) | timestamps

- Relasi: `sandboxTables()` hasMany orderBy('order')
- `prefix` digunakan sebagai awalan nama tabel di `primmbot_sandbox`
- Contoh: prefix `toko_buku` → tabel `toko_buku__penerbit`

### `sandbox_tables`
`id` | `sandbox_database_id` FK (cascadeOnDelete) | `table_name` | `display_name` | `order` default 0 | timestamps

- `table_name` = nama tabel sungguhan di `primmbot_sandbox` (format: `{prefix}__{display_slug}`)
- `display_name` = nama tampilan di UI (contoh: "penerbit")

---

## 4. Database Sandbox — `primmbot_sandbox`

Koneksi terpisah (`sandbox` di `config/database.php`). Hanya untuk latihan SQL siswa.

**Mekanisme:**
- Guru buat "Database" (label/grup) di admin → disimpan di `sandbox_databases`
- Guru buat tabel sungguhan (CREATE TABLE) dari admin → tabel dibuat di `primmbot_sandbox`, metadata di `sandbox_tables`
- Format nama tabel: `{prefix}__{nama_tabel}` (contoh: `toko_buku__penerbit`)
- Guru bisa isi data, edit data, edit struktur, dan definisikan Foreign Key antar tabel
- Saat guru buat aktivitas PRIMM → pilih database sandbox → siswa hanya akses tabel dari database itu
- Semua query modifikasi siswa (INSERT/UPDATE/DELETE) di-rollback via transaction

**Status saat ini:**
- Tabel lama hardcoded masih ada: `penerbit`(3), `penulis`(3), `buku`(4), `pelanggan`(3), `transaksi`(3) — ID buku/penerbit/penulis = string, pelanggan/transaksi = integer
- Guru sudah bisa buat tabel baru dari admin panel
- **BELUM terhubung ke sisi siswa** — `SqlRunnerController` masih query semua tabel tanpa filter per `sandbox_database_id`

---

## 5. Hierarki Konten

```
Course
  └── Chapter
        ├── LessonMaterials (5 tipe: pendahuluan, petunjuk_belajar, tujuan, prasyarat, ringkasan_materi)
        └── Activities (5 tahap PRIMM: predict, run, investigate, modified, make)
              └── sandbox_database_id → SandboxDatabase → SandboxTables → tabel nyata di primmbot_sandbox
```

- **Chapter 1 Equi Join:** 5 materials + 12 activities (1P + 1R + 4I + 3Mod + 3Make)

---

## 6. Struktur Folder

```
primmbot/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   ├── AuthenticatedSessionController.php
│   │   │   │   ├── GoogleController.php
│   │   │   │   └── RegisteredUserController.php
│   │   │   ├── Api/
│   │   │   │   ├── SqlRunnerController.php       ← eksekusi query ke sandbox
│   │   │   │   ├── SubmissionController.php      ← submit jawaban final
│   │   │   │   └── ChatController.php            ← chat dengan virtual assistant
│   │   │   ├── Admin/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── CourseController.php
│   │   │   │   ├── ChapterController.php
│   │   │   │   ├── ChapterContentController.php
│   │   │   │   ├── LessonMaterialController.php
│   │   │   │   ├── ActivityController.php
│   │   │   │   ├── StudentController.php
│   │   │   │   ├── SandboxDatabaseController.php
│   │   │   │   └── SandboxTableController.php
│   │   │   ├── CourseController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── HomeController.php
│   │   │   ├── LearningController.php            ← routing tahapan PRIMM
│   │   │   └── ProfileController.php
│   │   ├── Middleware/
│   │   │   ├── EnsureProfileComplete.php
│   │   │   └── RoleMiddleware.php
│   │   └── Requests/Auth/LoginRequest.php
│   ├── Models/
│   │   ├── User.php / Profile.php
│   │   ├── Course.php / Chapter.php / LessonMaterial.php
│   │   ├── Activity.php / Submission.php / MaterialCompletion.php
│   │   ├── AiInteractionLog.php / TeacherReview.php
│   │   └── SandboxDatabase.php / SandboxTable.php
│   └── Services/
│       └── AI/
│           ├── AIService.php                    ← satu-satunya pintu masuk dari controller
│           ├── GroqClient.php                   ← HTTP layer saja (kirim prompt → terima teks)
│           ├── ContextLoader.php                ← ambil ringkasan materi + struktur tabel sandbox
│           ├── ResponseParser.php               ← parse JSON dari AI → EvaluationResult
│           ├── EvaluationResult.php             ← value object: keruntutan, berargumen,
│           │                                       kesimpulan, total, isCorrect, feedback
│           ├── Prompts/
│           │   ├── SystemPrompt.php             ← identitas & aturan PRIMM Bot (1 tempat)
│           │   ├── ChatPrompt.php               ← bangun prompt chat untuk semua stage
│           │   └── Stages/
│           │       ├── PredictPrompt.php        ← rubrik + prompt evaluasi khusus Predict
│           │       ├── RunPrompt.php            ← rubrik + prompt evaluasi khusus Run
│           │       │                               (inject jawaban Predict sebelumnya)
│           │       ├── InvestigatePrompt.php    ← rubrik + prompt per level (atoms/blocks/
│           │       │                               relations/macro)
│           │       ├── ModifyPrompt.php         ← rubrik + prompt evaluasi khusus Modify
│           │       │                               (inject editor_default_code)
│           │       └── MakePrompt.php           ← rubrik + prompt evaluasi khusus Make
│           └── Evaluators/
│               ├── NarrativeEvaluator.php       ← evaluasi jawaban teks (Predict, Run,
│               │                                   Investigate) → panggil GroqClient
│               └── SqlEvaluator.php             ← jalankan SQL siswa ke sandbox,
│                                                   bandingkan output → panggil GroqClient
│
├── docs/
│   ├── virtual-assistant.md           ← dokumentasi lengkap sistem AI
│   └── fitur-download-hasil-belajar.md
│
├── resources/views/
│   ├── components/layouts/
│   │   ├── app.blade.php              # Layout dark siswa
│   │   ├── auth.blade.php             # Layout dark auth
│   │   └── admin.blade.php            # Layout admin (topbar biru tua, sidebar putih)
│   ├── layouts/
│   │   └── learning.blade.php         # Layout LKPD siswa
│   ├── auth/
│   │   ├── login.blade.php
│   │   └── register.blade.php
│   ├── pages/
│   │   ├── home.blade.php
│   │   ├── dashboard.blade.php
│   │   ├── profile/edit.blade.php
│   │   └── courses/ (index, show)
│   ├── learning/
│   │   ├── material.blade.php
│   │   ├── summary.blade.php
│   │   └── stages/
│   │       ├── predict.blade.php
│   │       ├── run.blade.php
│   │       ├── investigate.blade.php
│   │       ├── modify.blade.php
│   │       └── make.blade.php
│   └── admin/
│       ├── dashboard.blade.php
│       ├── courses / chapters / materials / activities / students / sandbox
│
├── routes/web.php
└── .env
```

---

## 7. Route Penting

| Name | Path | Keterangan |
|---|---|---|
| `learning.activity` | `/belajar/{chapter}/aktivitas/{activity}` | Halaman tahap PRIMM |
| `api.sql.run` | `POST /api/sql/run` | Jalankan query ke sandbox |
| `api.submission.submit` | `POST /api/submission/submit` | Submit jawaban → evaluasi + skor |
| `api.chat` | `POST /api/chat` | Chat dengan Virtual Assistant |
| `admin.*` | `/admin/...` | Manajemen konten & sandbox |

> **Catatan:** Route `api.submission.check` (tombol CEK) dihapus. Tidak ada lagi tombol CEK — siswa langsung chat dengan AI jika butuh bantuan, lalu Submit jika sudah yakin.

---

## 8. Konvensi & Hal Penting

- **Frontend:** Blade + Vanilla CSS, style inline — **bukan Tailwind utility class**
- **Dua koneksi DB:** `mysql` (utama) dan `sandbox` (query siswa) — jangan campur
- Query siswa ke sandbox **wajib dibungkus transaction + rollback** untuk INSERT/UPDATE/DELETE
- Layout belajar: grid 2 kolom — kiri (database panel / editor) — kanan (soal + jawaban)
- Panel Database (tabel + ERD Mermaid) tampil di kolom kiri **semua tahap PRIMM**
- `$sandboxTables` dikirim dari `LearningController` ke semua view — berisi struktur kolom
- ERD dirender client-side dengan **Mermaid.js v10**, dinamis dari `$sandboxTables`
- **Tidak ada tombol CEK** — siswa hanya punya dua aksi: Chat (tanya AI) dan Submit (kumpulkan jawaban)
- **Submit bisa berkali-kali** hingga skor ≥ KKM — setiap submit disimpan sebagai record baru di `submissions`
- **Feedback submit bersifat scaffolding** (pertanyaan pemantik), bukan evaluatif — ditampilkan di chat widget
- **Skor dihitung oleh PHP**, bukan AI — AI hanya memberi skor per indikator (0–100), PHP menghitung rata-rata
- **Rubrik logical thinking ada di kode** (per file stage prompt), bukan di DB — ini konstanta penelitian
- **LLM:** Groq API (model dikonfigurasi via `.env`) — lihat `docs/virtual-assistant.md` untuk detail lengkap

---

## 9. Backlog / TODO

### `reference_answer` — Acuan Kualitas Berpikir untuk Evaluasi AI

**Latar belakang:**
Saat ini AI menilai jawaban siswa berdasarkan rubrik logical thinking saja (keruntutan, berargumen, kesimpulan). Tanpa contoh jawaban ideal, AI tidak tahu apakah *argumen* siswa logis untuk soal spesifik itu — hanya tahu apakah *struktur* argumen ada atau tidak.

Contoh masalah: siswa menjawab urut + ada argumen + ada kesimpulan, tapi mengabaikan konsep JOIN sepenuhnya → AI memberi skor tinggi karena struktur terpenuhi, padahal konten keliru.

**Solusi: tambah field `reference_answer` (text, nullable) di tabel `activities`**

Bukan kunci jawaban, tapi **contoh jawaban ideal** yang ditulis guru — menunjukkan kualitas reasoning yang diharapkan per indikator untuk soal itu. AI menggunakannya sebagai acuan kalibrasi, bukan cek kecocokan kata.

Berlaku untuk semua 5 stage:
- predict/run/investigate → acuan untuk jawaban teks utama
- modify/make → acuan untuk bagian penjelasan teks (`answer_text`)

**Yang perlu dikerjakan:**
1. Migration: `add_reference_answer_to_activities_table` — kolom `reference_answer` text nullable
2. `Activity` model: tambah ke `$fillable`
3. `ActivityController`: tambah validasi `'reference_answer' => 'nullable|string'` di `store()` dan `update()`
4. 10 admin form (create/edit semua stage): tambah textarea `reference_answer` dengan label "Contoh Jawaban Ideal (Acuan AI)"
5. 5 file Prompt (`PredictPrompt`, `RunPrompt`, `InvestigatePrompt`, `ModifyPrompt`, `MakePrompt`): inject `$activity->reference_answer` ke prompt jika tidak null — contoh: `"Contoh jawaban ideal (gunakan sebagai acuan kualitas berpikir, bukan kunci jawaban):\n{$ref}\n"`

---

### Teacher Review — Feedback & Koreksi Skor oleh Guru

**Latar belakang:**
Sebagai fallback jika AI error atau skor tidak masuk akal. Guru berhak mengoreksi skor AI dan memberikan feedback manual. Tabel `teacher_reviews` sudah ada di DB.

**Keputusan desain:**
- Feedback guru **opsional** (tidak wajib) — feedback AI tetap yang wajib
- Guru hanya mereview **submission terakhir** per siswa per aktivitas
- Skor guru **menggantikan (override)** skor AI, bukan dirata-rata
- Saat guru simpan review → `submissions.score` dan `submissions.is_correct` langsung diupdate
- Siswa melihat feedback guru dengan **manual refresh** (bukan polling) — sesuai skenario kelas tatap muka
- Feedback guru tampil sebagai **blok terpisah** di halaman aktivitas (bukan di chat widget)
- Jika belum ada review guru → blok tidak muncul sama sekali di sisi siswa

**Skema skor:**
```
Skor final = teacher_reviews.score ?? submissions.score (AI)
is_correct = (skor_final >= activity.kkm)
```

**Yang perlu dikerjakan:**
1. **Admin — halaman baru**: `admin.submissions.index` per aktivitas → tabel daftar siswa + skor AI + status review
2. **Admin — form review**: tampil jawaban siswa (read-only) + skor AI + feedback AI → input skor guru + textarea feedback → simpan ke `teacher_reviews`
3. **`TeacherReviewController`** (baru): `store()` / `update()` → simpan ke `teacher_reviews` + update `submissions.score` dan `submissions.is_correct`
4. **Route**: `admin.activities.submissions` (index) + `admin.submissions.review` (store/update)
5. **Sisi siswa**: tambah blok "Feedback Guru" di semua 5 stage view — tampil hanya jika `$teacherReview` tidak null, posisi di atas chat widget
6. **`LearningController`**: load `teacherReview` dari `teacher_reviews` join `submissions` terbaru → kirim ke view
