# Dokumentasi Asisten Virtual PRIMM Bot

## 1. Gambaran Umum

PRIMM Bot adalah asisten virtual berbasis AI yang berperan sebagai **MKO (More Knowledgeable Other)** dalam platform e-LKPD PRIMMBOT. Asisten ini dirancang khusus untuk membantu siswa SMK kelas XI PPLG belajar SQL JOIN dan DCL melalui pendekatan pedagogis **Scaffolding dan Fading**.

**Filosofi utama:** PRIMM Bot **tidak pernah memberikan jawaban langsung**. Sebaliknya, bot membimbing siswa untuk berpikir sendiri melalui pertanyaan pemantik yang membangun *Logical Thinking* secara bertahap (Bloom's Taxonomy: ingat → pahami → terapkan → analisis).

---

## 2. Teknologi

| Komponen | Detail |
|---|---|
| LLM | Groq — `openai/gpt-oss-120b` |
| API Endpoint | `https://api.groq.com/openai/v1/chat/completions` |
| Format API | OpenAI-compatible (format `messages` dengan role system/user) |
| Autentikasi | Bearer Token (`GROQ_API_KEY` di `.env`) |
| Service Class | `app/Services/GeminiService.php` |
| Controller | `app/Http/Controllers/Api/SubmissionController.php` |
| Log Interaksi | Tabel `ai_interaction_logs` di database `primmbot` |

### Parameter Generasi

```
temperature : 0.7   (sedikit kreatif, tidak terlalu kaku)
max_tokens  : 256   (hemat quota, cukup untuk 2-3 kalimat)
```

### Quota Free Tier Groq (per API key)

| Limit | Nilai |
|---|---|
| Request per Menit (RPM) | 30 |
| Request per Hari (RPD) | 14.400 |

> Dengan 14.400 RPD, Groq jauh lebih besar dari Gemini (1.500 RPD). Cukup untuk 3 kelas (±120 siswa) dalam 1 hari penuh.

---

## 3. Struktur File & Letak Logika

Ini adalah peta lengkap file mana yang bertanggung jawab atas apa:

```
.env
└── GROQ_API_KEY, GROQ_MODEL
    → Menyimpan kredensial API. TIDAK boleh di-commit ke git.

config/services.php
└── 'groq' => ['api_key' => ..., 'model' => ...]
    → Jembatan antara .env dan kode PHP. Dibaca via config('services.groq.*').

app/Services/GeminiService.php          ← PUSAT LOGIKA AI
├── __construct()                        → Baca config API key & model
├── loadContext()                        → Ambil ringkasan materi + struktur tabel sandbox dari DB
├── formatMaterials()                    → Format materi untuk dimasukkan ke prompt
├── formatSandboxTables()                → Format struktur tabel untuk dimasukkan ke prompt
├── getSystemPrompt()                    → Instruksi dasar karakter & aturan PRIMM Bot
├── isCasualMessage()                    → Deteksi apakah pesan siswa bersifat kasual/salam
├── buildFeedbackPrompt()                → Susun prompt untuk tombol CEK
├── buildEvaluationPrompt()              → Susun prompt untuk tombol SUBMIT
├── buildChatPrompt()                    → Susun prompt untuk chat bebas
├── call()                               → Kirim prompt ke Groq API, kembalikan respons teks
├── parseEvaluation()                    → Parse JSON respons dari Groq untuk SUBMIT
├── getLevelFocus()                      → Teks fokus per level Investigate
├── fallbackFeedback()                   → Pesan statis jika API gagal (CEK)
└── fallbackEvaluation()                 → Scoring keyword jika API gagal (SUBMIT)

app/Http/Controllers/Api/SubmissionController.php
├── check()    → Endpoint tombol CEK  → panggil GeminiService::getFeedback()
├── submit()   → Endpoint tombol SUBMIT → panggil GeminiService::evaluateSubmission()
│                                         atau compareOutput() untuk modified/make
├── chat()     → Endpoint chat bebas  → panggil GeminiService::chat()
└── compareOutput() → Eksekusi SQL siswa ke sandbox, bandingkan dengan expected_output

routes/web.php
├── POST /api/submission/check   → SubmissionController::check()
├── POST /api/submission/submit  → SubmissionController::submit()
└── POST /api/chat               → SubmissionController::chat()

resources/views/learning/stages/*.blade.php
└── JS di tiap file (predict, run, investigate, modified, make)
    ├── fetch('/api/submission/check')  → dipanggil saat klik tombol Cek
    ├── fetch('/api/submission/submit') → dipanggil saat klik tombol Submit
    └── fetch('/api/chat')              → dipanggil saat siswa kirim pesan chat
```

---

## 4. Tiga Mode Fungsi

PRIMM Bot memiliki tiga fungsi utama yang dipanggil dalam situasi berbeda:

### 4.1 Tombol CEK — Feedback Scaffolding

**Kapan dipanggil:** Ketika siswa mengklik tombol **Cek** di halaman aktivitas.

**Tujuan:** Memberikan umpan balik yang membimbing, bukan mengoreksi secara langsung. Bot membaca jawaban siswa dan merespons dengan pertanyaan pemantik yang mendorong siswa memperbaiki jawabannya sendiri.

**Method:** `GeminiService::getFeedback(Activity $activity, Submission $submission): string`

**Alur:**
1. Siswa mengisi jawaban → klik Cek
2. `SubmissionController::check()` dipanggil via `POST /api/submission/check`
3. Jawaban disimpan sebagai draft di tabel `submissions` (`is_correct = false`)
4. `getFeedback()` → `loadContext()` → `buildFeedbackPrompt()` → `call()` → Groq API
5. Feedback dikembalikan ke UI dan disimpan ke `submissions.ai_feedback`
6. Interaksi dicatat di `ai_interaction_logs`

**Konten prompt per tahap:**

| Tahap | Yang dimasukkan ke prompt |
|---|---|
| Predict | Query SQL + pertanyaan + prediksi siswa. Arah bimbingan: kolom apa dipilih, tabel mana digabung, kondisi JOIN. |
| Run | Query SQL + pertanyaan refleksi + jawaban siswa. Arah: bandingkan prediksi vs hasil nyata. |
| Investigate | Query SQL + level (atoms/blocks/relations/macro) + jawaban. Arah: analisis sesuai fokus level. |
| Modified | Instruksi soal + kode SQL siswa + penjelasan. Arah: apakah arah modifikasi sudah benar. |
| Make | Instruksi soal + kode SQL siswa + penjelasan. Arah: cek kelengkapan SELECT/FROM/JOIN/ON. |

**Fallback (jika API gagal):** Pesan statis per tahap, contoh:
- Predict: *"Coba perhatikan query dengan teliti. Kolom apa saja yang dipilih oleh SELECT? Tabel mana yang digabungkan oleh JOIN?"*

---

### 4.2 Tombol SUBMIT — Evaluasi & Penilaian

**Kapan dipanggil:** Ketika siswa mengklik tombol **Submit** (jawaban final).

**Tujuan:** Mengevaluasi kualitas jawaban siswa dan memberikan skor 0–100 beserta feedback singkat.

**Method:** `GeminiService::evaluateSubmission(Activity $activity, ?string $answerText, ?string $answerCode): array`

**Alur:**
1. Siswa submit → `SubmissionController::submit()` via `POST /api/submission/submit`
2. Sistem menentukan metode evaluasi berdasarkan tahap:
   - **Predict / Run / Investigate** → evaluasi via Groq (jawaban berbasis narasi/teks)
   - **Modified / Make** → eksekusi SQL langsung ke `primmbot_sandbox` → bandingkan output dengan `expected_output` (lebih akurat, tidak perlu AI)
3. Submission final disimpan ke tabel `submissions` dengan `is_correct`, `score`, `ai_feedback`

**Format respons Groq untuk evaluasi:**
```json
{"score": 85, "is_correct": true, "feedback": "Analisis kamu sudah sangat baik..."}
```

**Kriteria penilaian per tahap:**

| Tahap | 85-100 | 70-84 | 50-69 | 0-49 |
|---|---|---|---|---|
| Predict | Sebut kolom + tabel + kondisi JOIN dengan jelas | Relevan sebagian | Kurang detail | Singkat/tidak relevan |
| Run | Bandingkan prediksi vs hasil + alasan jelas | Bandingkan tanpa alasan | Sebut hasil saja | Tidak relevan |
| Investigate | Analisis mendalam sesuai level | Cukup baik | Dangkal | Tidak mencakup aspek level |

**Fallback (jika API gagal):** Scoring berbasis keyword — cek apakah jawaban mengandung kata kunci SQL (`join`, `tabel`, `kolom`, `select`, dll). Minimal 15 karakter, skor 65–100 tergantung jumlah keyword yang cocok.

---

### 4.3 Chat Bebas — Virtual Assistant

**Kapan dipanggil:** Kapan saja — siswa dapat langsung chat tanpa harus klik Cek/Submit terlebih dahulu.

**Tujuan:** Ruang bebas bertanya. Siswa bisa bertanya apa saja seputar materi, minta penjelasan konsep, atau sekadar menyapa. Bot tetap menggunakan pendekatan scaffolding jika pertanyaan menyangkut SQL/materi.

**Method:** `GeminiService::chat(Activity $activity, string $userMessage, array $history = []): string`

**Endpoint:** `POST /api/chat`

**Alur:**
1. Siswa ketik pesan → Enter atau klik tombol kirim
2. `SubmissionController::chat()` dipanggil
3. `isCasualMessage()` mendeteksi apakah pesan **kasual** atau **terkait materi**
4. `buildChatPrompt()` menyusun prompt (dengan atau tanpa konteks aktivitas)
5. `call()` mengirim ke Groq API → respons dikembalikan ke chat UI
6. Riwayat percakapan disimpan di frontend (array `chatHistory`, max 6 pesan terakhir dikirim ke API)
7. Setiap interaksi dicatat di `ai_interaction_logs`

**Deteksi pesan kasual (`isCasualMessage()`):**
Jika pesan mengandung kata seperti `halo`, `hai`, `hi`, `hello`, `siapa namamu`, `tes`, `selamat`, atau panjangnya ≤ 5 karakter → dianggap kasual. Konteks soal SQL **tidak** diinjeksi ke prompt sehingga bot merespons secara natural tanpa memaksakan konteks SQL.

**Deteksi pesan terkait materi:**
Jika pesan tidak kasual → konteks aktivitas diinjeksi: ringkasan materi (maks 800 karakter) + struktur tabel sandbox + tahap PRIMM + soal yang sedang dikerjakan.

---

## 5. Injeksi Konteks (Context Injection)

Setiap kali AI dipanggil untuk CEK, SUBMIT, atau CHAT terkait materi, prompt dibangun dengan menyertakan konteks dari database. Ini dilakukan di `loadContext()` yang membaca:
- `lesson_materials` (type = `ringkasan_materi`) dari chapter aktivitas
- Struktur kolom tabel di `primmbot_sandbox` via query `DESCRIBE`

**Struktur prompt yang dikirim ke Groq:**
```
[System Prompt — karakter & aturan PRIMM Bot]
[MATERI] ... ringkasan_materi dari lesson_materials (maks 800 karakter) ... [/MATERI]
[DB]
nama_tabel: kolom1(PK), kolom2, kolom3(FK)
...
[/DB]
Konteks aktivitas — Tahap: ... Soal: ... SQL: ...
[Riwayat percakapan (maks 6 pesan terakhir)]
Siswa: [pesan siswa]
Jawab sebagai PRIMM Bot, 2-3 kalimat:
```

**Kenapa tidak pakai RAG/vector search?**
- Ringkasan materi per chapter kecil (< 2000 karakter) → muat di prompt langsung
- Tidak perlu infrastruktur tambahan (Supabase + pgvector)
- Lebih sederhana dan cukup untuk skala SMKN 4 Bandung

---

## 6. Sistem Prompt & Prinsip Scaffolding

```
Kamu adalah PRIMM Bot, asisten belajar SQL untuk siswa SMK kelas XI.
Jika siswa menyapa atau berkenalan, balas dengan ramah dan natural.
Jika siswa bertanya soal SQL/materi, gunakan scaffolding: bimbing dengan pertanyaan pemantik,
JANGAN beri jawaban/kode langsung.
Maks 3 kalimat, Bahasa Indonesia ramah.
```

**Teknik scaffolding yang diterapkan (Bloom's Taxonomy):**

| Level | Teknik | Contoh Pertanyaan Bot |
|---|---|---|
| Ingat (Remember) | Recall | "Ingat di materi tadi, apa fungsi klausa JOIN?" |
| Pahami (Understand) | Parafrase | "Coba jelaskan dengan bahasamu sendiri, apa arti ON dalam query ini?" |
| Terapkan (Apply) | Aplikasi konkret | "Kalau ingin menampilkan nama buku dan penerbitnya, kolom mana yang dihubungkan?" |
| Analisis (Analyze) | Eksplorasi sebab-akibat | "Kenapa hasilnya berbeda jika kamu ganti kondisi ON-nya?" |

---

## 7. Level Investigate & Fokusnya

Tahap Investigate memiliki 4 level dengan fokus analisis yang berbeda:

| Level | Fokus Analisis |
|---|---|
| `atoms` | Elemen terkecil query: karakter, tanda baca, kata kunci individual (SELECT, FROM, JOIN, ON, titik pemisah tabel.kolom) |
| `blocks` | Baris/klausa query: fungsi setiap klausa, urutan penulisan, kondisi ON dalam JOIN |
| `relations` | Relasi antar tabel: Primary Key, Foreign Key, dan cara tabel dihubungkan melalui JOIN |
| `macro` | Konteks keseluruhan: kapan dan mengapa query jenis ini digunakan di dunia nyata |

---

## 8. Alur Data Lengkap

```
Siswa (UI Browser)
    │
    ├─[Klik Cek]──────────► POST /api/submission/check
    │                            │
    │                            ├─ Simpan draft ke submissions
    │                            ├─ GeminiService::getFeedback()
    │                            │      └─ loadContext()         → DB: materi + struktur tabel
    │                            │      └─ buildFeedbackPrompt() → susun prompt per tahap
    │                            │      └─ call()                → Groq API
    │                            ├─ Simpan ai_feedback ke submissions
    │                            ├─ Catat ke ai_interaction_logs
    │                            └─ Return { success, feedback }
    │
    ├─[Klik Submit]────────► POST /api/submission/submit
    │                            │
    │                            ├─ evaluateAnswer()
    │                            │      ├─ predict/run/investigate
    │                            │      │      └─ GeminiService::evaluateSubmission()
    │                            │      │             └─ call() → Groq API → parse JSON
    │                            │      └─ modified/make
    │                            │             └─ compareOutput() → eksekusi SQL ke sandbox
    │                            ├─ Simpan submission final ke submissions
    │                            ├─ Catat ke ai_interaction_logs
    │                            └─ Return { success, is_correct, score, feedback }
    │
    └─[Chat]───────────────► POST /api/chat
                                 │
                                 ├─ GeminiService::chat()
                                 │      ├─ isCasualMessage()   → deteksi jenis pesan
                                 │      ├─ loadContext()        → DB: materi + struktur tabel
                                 │      ├─ buildChatPrompt()    → susun prompt (dengan/tanpa konteks)
                                 │      └─ call()               → Groq API
                                 ├─ Catat ke ai_interaction_logs
                                 └─ Return { success, response }
```

---

## 9. Log Interaksi (`ai_interaction_logs`)

Setiap panggilan ke Groq (CEK, SUBMIT, CHAT) dicatat dengan struktur:

| Kolom | Isi |
|---|---|
| `user_id` | ID siswa yang melakukan interaksi |
| `activity_id` | ID aktivitas PRIMM yang sedang dikerjakan |
| `prompt_sent` | Label singkat: `check:predict`, `submit:run`, atau isi pesan chat |
| `response_received` | Teks respons dari Groq |
| `tokens_used` | Nullable — belum diisi |
| `response_time` | Nullable — belum diisi |

> Log ini dapat dilihat oleh admin untuk memantau kualitas interaksi siswa dengan AI (fitur tampilan admin belum diimplementasi).

---

## 10. Konfigurasi & Environment

**File: `.env`** — menyimpan kredensial, tidak boleh di-commit ke git
```env
GROQ_API_KEY=your_groq_api_key_here
GROQ_MODEL=openai/gpt-oss-120b
```

**File: `config/services.php`** — jembatan ke kode PHP
```php
'groq' => [
    'api_key' => env('GROQ_API_KEY'),
    'model'   => env('GROQ_MODEL', 'openai/gpt-oss-120b'),
],
```

**File: `app/Services/GeminiService.php`** — membaca konfigurasi via:
```php
$this->apiKey = config('services.groq.api_key', '');
$this->model  = config('services.groq.model', 'openai/gpt-oss-120b');
```

Setelah mengubah `.env`, **wajib** jalankan:
```bash
php artisan config:clear
```

---

## 11. Mengganti API Key (jika quota habis)

1. Buat API key baru di [console.groq.com](https://console.groq.com) → API Keys
2. Ganti nilai `GROQ_API_KEY` di `.env`
3. Jalankan `php artisan config:clear`

Untuk memantau sisa quota, cek di [console.groq.com](https://console.groq.com) → Usage.
