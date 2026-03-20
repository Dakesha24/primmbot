# PRIMMBOT — Panduan Proyek untuk Claude

## 1. Identitas Proyek

- **Nama:** PRIMMBOT
- **Tujuan:** Platform e-LKPD (Lembar Kerja Peserta Didik digital) untuk meningkatkan *Logical Thinking* siswa SMK pada materi Basis Data (SQL Join & DCL)
- **Model Pembelajaran:** PRIMM — Predict, Run, Investigate, Modified, Make
- **Konsep AI:** Virtual Assistant berperan sebagai MKO (*More Knowledgeable Other*) menggunakan strategi Scaffolding & Fading
- **Target Pengguna:** Siswa SMKN 4 Bandung, kelas XI PPLG 1/2/3

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
`id` | `chapter_id` FK | `sandbox_database_id` FK nullable | `description` longText nullable | `stage` enum(predict, run, investigate, modified, make) | `level` enum(atoms, blocks, relations, macro, mudah, sedang, tantang) nullable | `question_text` text | `code_snippet` text nullable | `editor_default_code` text nullable | `expected_output` JSON nullable | `order` | timestamps

- Cast: `expected_output` → array
- Relasi: `sandboxDatabase()` belongsTo

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
`id` | `user_id` FK | `activity_id` FK | `answer_text` nullable | `answer_code` nullable | `is_correct` boolean | `score` integer nullable | `ai_feedback` text nullable | timestamps

### `material_completions`
`id` | `user_id` FK | `lesson_material_id` FK | timestamps | UNIQUE pair

### `ai_interaction_logs`
`id` | `user_id` FK | `activity_id` FK | `prompt_sent` | `response_received` | `tokens_used` nullable | `response_time` decimal nullable | timestamps

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
│   │   │   │   └── SubmissionController.php      ← cek & submit jawaban (AI)
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
│   └── Models/
│       ├── User.php / Profile.php
│       ├── Course.php / Chapter.php / LessonMaterial.php
│       ├── Activity.php / Submission.php / MaterialCompletion.php
│       ├── AiInteractionLog.php / TeacherReview.php
│       └── SandboxDatabase.php / SandboxTable.php
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
│   │       ├── modified.blade.php
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
| `api.submission.check` | `POST /api/submission/check` | Cek jawaban (AI feedback) |
| `api.submission.submit` | `POST /api/submission/submit` | Submit jawaban final |
| `admin.*` | `/admin/...` | Manajemen konten & sandbox |

---

## 8. Konvensi & Hal Penting

- **Frontend:** Blade + Vanilla CSS, style inline — **bukan Tailwind utility class**
- **Dua koneksi DB:** `mysql` (utama) dan `sandbox` (query siswa) — jangan campur
- Query siswa ke sandbox **wajib dibungkus transaction + rollback** untuk INSERT/UPDATE/DELETE
- Layout belajar: grid 2 kolom — kiri (database panel / editor) — kanan (soal + jawaban)
- Panel Database (tabel + ERD Mermaid) tampil di kolom kiri **semua tahap PRIMM**
- `$sandboxTables` dikirim dari `LearningController` ke semua view — berisi struktur kolom
- ERD dirender client-side dengan **Mermaid.js v10**, dinamis dari `$sandboxTables`
- Scaffolding & Fading: bantuan AI berkurang seiring kemajuan siswa (konsep pedagogis inti)
- LLM saat ini menggunakan **Gemini 1.5 Flash** (belum diimplementasi penuh)
