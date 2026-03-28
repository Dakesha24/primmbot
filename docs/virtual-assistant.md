# Dokumentasi Virtual Assistant — PRIMM Bot

## 1. Gambaran Umum

PRIMM Bot adalah asisten virtual berbasis AI yang berperan sebagai **MKO (More Knowledgeable Other)** dalam platform e-LKPD PRIMMBOT. Bot ini dirancang untuk membantu siswa SMK kelas XI PPLG belajar SQL JOIN melalui pendekatan **Scaffolding** — membimbing siswa berpikir sendiri lewat pertanyaan pemantik, tidak pernah memberi jawaban langsung.

### Prinsip Utama

**Scaffolding ada di dua lapisan yang berbeda:**

1. **Lapisan PRIMM (struktural)** — Fading terjadi secara alami melalui urutan tahapan:
   - Predict → siswa dibantu sepenuhnya dengan kode yang sudah ada
   - Run → siswa menjalankan kode, lalu merefleksikan
   - Investigate → siswa menganalisis tanpa diberi tahu
   - Modify → siswa memodifikasi sendiri
   - Make → siswa membuat dari nol tanpa bantuan apapun
   - *Semakin maju tahap, semakin mandiri siswa — inilah fading*

2. **Lapisan AI (conversational)** — Bot membimbing siswa **dalam satu tahap** yang sedang dikerjakan. Bot tidak berubah karakter antar tahap; yang berubah adalah konteks soal dan rubrik evaluasinya.

### Tujuan Penelitian

Bot ini dirancang untuk melatih dan mengukur tiga indikator **Logical Thinking**:

| Indikator | Deskripsi |
|---|---|
| **Keruntutan Berpikir** | Siswa menjelaskan secara urut dan logis, ada alur yang jelas |
| **Kemampuan Berargumen** | Siswa memberikan alasan (*mengapa*), bukan sekadar menyatakan (*apa*) |
| **Penarikan Kesimpulan** | Siswa menutup jawaban dengan pernyataan yang merangkum pemahamannya |

---

## 2. Teknologi

| Komponen | Detail |
|---|---|
| LLM | Groq API — model dikonfigurasi via `GROQ_MODEL` di `.env` |
| API Endpoint | `https://api.groq.com/openai/v1/chat/completions` |
| Format API | OpenAI-compatible |
| Autentikasi | Bearer Token (`GROQ_API_KEY` di `.env`) |
| Service Utama | `app/Services/AI/AIService.php` |
| Controller Submit | `app/Http/Controllers/Api/SubmissionController.php` |
| Controller Chat | `app/Http/Controllers/Api/ChatController.php` |
| Log Interaksi | Tabel `ai_interaction_logs` |

### Parameter Generasi

```
temperature : 0.7    (sedikit kreatif, tidak terlalu kaku)
max_tokens  : 300    (cukup untuk evaluasi JSON + feedback 2-3 kalimat)
```

### Quota Free Tier Groq

| Limit | Nilai |
|---|---|
| Request per Menit (RPM) | 30 |
| Request per Hari (RPD) | 14.400 |

> Cukup untuk ±120 siswa (3 kelas) dalam 1 hari penuh.

---

## 3. Dua Mode Interaksi

Tidak ada lagi tombol **CEK**. Siswa hanya memiliki dua aksi:

```
[Baca soal aktivitas]
        │
        ├──→ CHAT dengan PRIMM Bot  (jika butuh bantuan)
        │       Bot membimbing lewat pertanyaan pemantik
        │       Siswa boleh chat sepuasnya
        │
        └──→ SUBMIT jawaban  (jika sudah yakin)
                Bot mengevaluasi → memberikan skor + feedback scaffolding
                        │
                ┌───────┴────────┐
              ≥ KKM           < KKM
                │                │
            Lanjut ke        Feedback ditampilkan di chat widget
            aktivitas        → Siswa bisa chat lagi, lalu submit ulang
            berikutnya       → Tidak ada batas percobaan
```

### Mengapa Tidak Ada Tombol CEK?

Tombol CEK dan Submit yang terpisah seringkali membingungkan siswa tentang kapan harus melakukan apa. Dengan desain baru:
- **Chat** = ruang bebas eksplorasi dan bertanya — tidak ada tekanan
- **Submit** = komitmen jawaban — siswa submit hanya jika sudah yakin
- Feedback setelah submit tetap berupa scaffolding (bukan langsung "ini salahnya"), sehingga siswa tetap perlu berpikir untuk memperbaiki

---

## 4. Mode CHAT — Bimbingan Conversational

### Kapan Digunakan
Kapan saja selama siswa merasa bingung atau butuh arahan. Tidak perlu mengisi jawaban dulu.

### Konteks yang Dimiliki AI
Saat menjawab chat, AI mengetahui:
- Stage dan level aktivitas yang sedang dikerjakan
- Soal (`question_text`) dan kode SQL (`code_snippet`) aktivitas
- Ringkasan materi dari chapter
- Struktur tabel sandbox (nama kolom, PK, FK)
- Riwayat submit terakhir siswa: skor, jawaban, percobaan ke berapa
- Riwayat percakapan chat sebelumnya (persisten, tidak hilang saat refresh)

### Yang TIDAK Dimiliki AI Secara Otomatis
- Isi jawaban/kode yang sedang ditulis siswa (kecuali siswa sengaja paste di pesan chat)

> **Mengapa?** Ini disengaja — siswa dipaksa mengartikula sendiri di mana letak kesulitannya sebelum minta bantuan. Proses itu sendiri melatih metakognisi.

### Riwayat Percakapan
Riwayat chat disimpan di tabel `ai_interaction_logs` (`type = 'chat'`). Saat siswa membuka kembali halaman aktivitas, riwayat percakapan dimuat ulang dari DB — tidak hilang meski halaman di-refresh.

### Jenis Respons AI
- Jika pesan kasual (sapa, perkenalan) → AI merespons natural tanpa injeksi konteks soal
- Jika pesan terkait materi → AI merespons dengan scaffolding: pertanyaan pemantik, hint arah, tanpa jawaban

---

## 5. Mode SUBMIT — Evaluasi dengan Scaffolding

### Kapan Digunakan
Saat siswa sudah yakin dengan jawabannya dan ingin dikumpulkan.

### Yang Dikirim ke Sistem
```
activity_id   : ID aktivitas
answer_text   : Jawaban narasi/penjelasan siswa (nullable)
answer_code   : Kode SQL yang ditulis siswa (nullable)
```

### Alur Evaluasi

```
SubmissionController::submit()
    │
    ├─ Hitung attempt ke-berapa (query submissions terakhir + 1)
    │
    ├─ AIService::evaluateSubmission(activity, submission)
    │       │
    │       ├─ ContextLoader::load(activity)
    │       │       → ambil ringkasan materi + DESCRIBE tabel sandbox
    │       │
    │       ├─ [stage = predict / run / investigate]
    │       │       NarrativeEvaluator::evaluate()
    │       │           → StagePrompt::buildEvaluationPrompt()
    │       │           → GroqClient::call()
    │       │           → ResponseParser::parseEvaluation()
    │       │
    │       └─ [stage = modify / make]
    │               SqlEvaluator::evaluate()
    │                   → Jalankan SQL siswa ke primmbot_sandbox
    │                   → Bandingkan actual output vs expected_output
    │                   → StagePrompt::buildEvaluationPrompt()
    │                   → GroqClient::call()
    │                   → ResponseParser::parseEvaluation()
    │
    ├─ ResponseParser mengembalikan EvaluationResult:
    │       { keruntutan, berargumen, kesimpulan }  ← dari AI
    │
    ├─ PHP menghitung skor total:
    │       total = round((keruntutan + berargumen + kesimpulan) / 3)
    │       is_correct = total >= activity.kkm
    │
    ├─ Simpan ke tabel submissions (record BARU, bukan update)
    │
    ├─ Catat ke ai_interaction_logs (type = 'submit')
    │
    └─ Kembalikan ke frontend:
            { is_correct, score, feedback }
            → Tampilkan feedback di chat widget
```

### Format Respons AI (JSON)

AI diminta membalas hanya dengan JSON berikut:
```json
{
  "keruntutan": 80,
  "berargumen": 65,
  "kesimpulan": 75,
  "feedback": "Analisismu sudah mengarah dengan baik! Coba perhatikan lagi — apakah kamu sudah menjelaskan *mengapa* tabel buku dan penerbit bisa digabungkan, bukan hanya *bahwa* keduanya digabungkan?"
}
```

> `feedback` selalu berupa **scaffolding** (pertanyaan pemantik / hint arah), bukan evaluasi langsung seperti "ini salah karena...".

### Penghitungan Skor (di PHP, bukan AI)

```php
// ResponseParser.php
$total = (int) round(
    ($result->keruntutan + $result->berargumen + $result->kesimpulan) / 3
);
$result->total     = $total;
$result->isCorrect = $total >= $activity->kkm;
```

AI hanya memberi skor mentah per indikator. PHP yang memutuskan lulus/tidak berdasarkan KKM aktivitas.

### Submit Ulang

Siswa boleh submit berkali-kali hingga skor ≥ KKM. Setiap submit menghasilkan record baru di `submissions` dengan `attempt` yang bertambah. Ini penting untuk:
- Siswa: bisa terus belajar tanpa takut "terkunci"
- Penelitian: dapat menganalisis perkembangan skor per indikator dari attempt ke attempt

---

## 6. Sistem Evaluasi Logical Thinking

### Mengapa Menggunakan LLM, Bukan Keyword Matching?

Penanda linguistik (kata "karena", "jadi", "pertama") adalah fitur permukaan bahasa — tidak cukup untuk menilai kualitas berpikir. Contoh:
- *"hasilnya nama buku karena join"* → mengandung "karena" tapi tidak benar-benar berargumen
- *"query menggabungkan tabel buku dengan penerbit melalui kolom id_penerbit yang merupakan FK"* → tidak ada kata "karena" tapi jelas berargumen

LLM mengevaluasi **makna dan struktur**, bukan sekadar kehadiran kata. Namun agar evaluasi konsisten dan bisa dipertanggungjawabkan, AI diberi **rubrik eksplisit** di setiap prompt.

### Rubrik Disimpan di Kode, Bukan DB

Rubrik logical thinking adalah **konstanta penelitian** — tidak boleh berubah antar aktivitas karena akan merusak konsistensi pengukuran. Setiap file `*Prompt.php` memiliki method `getRubrik()` yang mendefinisikan arti setiap indikator dalam konteks stage tersebut.

### Rubrik Per Stage

#### Predict
```
Nilai tiga indikator:
- keruntutan: apakah penjelasan siswa urut dari struktur query
              (SELECT → tabel yang di-JOIN → kondisi ON → output)?
- berargumen: apakah siswa menjelaskan MENGAPA output akan seperti itu,
              bukan sekadar menyebutkan apa outputnya?
- kesimpulan: apakah siswa menarik kesimpulan tentang output yang
              akan dihasilkan berdasarkan analisisnya?
```

#### Run
```
Nilai tiga indikator:
(Konteks: siswa baru saja menjalankan query dan melihat output nyata.
 Jawaban Predict sebelumnya diinjeksi ke prompt ini.)

- keruntutan: apakah refleksi urut dari prediksi → hasil nyata → alasan perbedaan/kesamaan?
- berargumen: apakah siswa menjelaskan MENGAPA prediksinya berbeda atau sama
              dengan output nyata?
- kesimpulan: apakah siswa menyimpulkan apa yang dipelajari dari perbandingan ini?
```

#### Investigate
```
Nilai tiga indikator:
(Fokus berbeda per level — diinjeksi ke prompt)

Level atoms    → analisis elemen terkecil query (karakter, tanda baca, kata kunci)
Level blocks   → analisis fungsi tiap klausa (SELECT, FROM, JOIN, ON)
Level relations→ analisis relasi PK-FK antar tabel
Level macro    → analisis konteks penggunaan query di dunia nyata

- keruntutan: apakah analisis urut dan sistematis sesuai fokus levelnya?
- berargumen: apakah siswa menjelaskan fungsi/alasan setiap elemen yang dianalisis?
- kesimpulan: apakah siswa menyimpulkan temuan dari analisisnya?
```

#### Modify
```
Nilai tiga indikator:
(Kode awal editor_default_code diinjeksi untuk perbandingan)

- keruntutan: apakah siswa memahami kode asal → mengidentifikasi yang perlu diubah
              → menerapkan perubahan secara logis?
- berargumen: apakah siswa menjelaskan MENGAPA memodifikasi bagian tersebut?
- kesimpulan: apakah siswa menyimpulkan apakah modifikasinya sudah menjawab perintah soal?
```

#### Make
```
Nilai tiga indikator:
(Siswa menulis SQL dari nol — tidak ada kode awal)

- keruntutan: apakah siswa membangun query secara urut
              (kebutuhan → pilih tabel → tentukan JOIN → tulis SELECT)?
- berargumen: apakah siswa menjelaskan MENGAPA memilih tabel dan kondisi JOIN tersebut?
- kesimpulan: apakah siswa menyimpulkan apakah query-nya sudah menjawab kebutuhan soal?
```

---

## 7. Struktur File Lengkap

```
app/Services/AI/
│
├── AIService.php
│   Orchestrator — satu-satunya class yang dipanggil controller.
│   Method publik:
│   - evaluateSubmission(Activity, Submission): EvaluationResult
│   - chat(Activity, string $message, array $history, ?Submission $latest): string
│
├── GroqClient.php
│   HTTP layer saja. Tidak tahu konteks apapun.
│   - call(string $prompt, int $maxTokens = 300): ?string
│
├── ContextLoader.php
│   Mengambil data dari DB untuk diinjeksi ke prompt.
│   - load(Activity): array ['materials' => ..., 'sandboxTables' => ...]
│     → materials: ringkasan materi (lesson_materials type=ringkasan_materi), maks 800 karakter
│     → sandboxTables: hasil DESCRIBE tiap tabel di primmbot_sandbox untuk activity ini
│
├── ResponseParser.php
│   Mengubah string JSON dari AI menjadi EvaluationResult.
│   - parseEvaluation(string $response, int $kkm): EvaluationResult
│     → hitung total = rata-rata tiga indikator
│     → set isCorrect = total >= kkm
│
├── EvaluationResult.php
│   Value object hasil evaluasi. Tidak punya method, hanya properti.
│   Properti: keruntutan, berargumen, kesimpulan, total, isCorrect, feedback
│
├── Prompts/
│   │
│   ├── SystemPrompt.php
│   │   Identitas dan aturan PRIMM Bot. Dipakai di semua jenis prompt.
│   │   - get(): string
│   │
│   ├── ChatPrompt.php
│   │   Membangun prompt untuk percakapan bebas.
│   │   - build(Activity, string $msg, array $history, array $context,
│   │           ?Submission $latest): string
│   │     → Jika pesan kasual → tidak injeksi konteks soal
│   │     → Jika pesan terkait materi → injeksi konteks + riwayat submit terakhir
│   │
│   └── Stages/
│       ├── PredictPrompt.php
│       │   - buildEvaluationPrompt(Activity, Submission, array $context): string
│       │   - getRubrik(): string  ← kriteria 3 indikator untuk stage Predict
│       │
│       ├── RunPrompt.php
│       │   - buildEvaluationPrompt(Activity, Submission, array $context,
│       │                           ?string $predictAnswer): string
│       │     → $predictAnswer: jawaban Predict siswa diinjeksi agar AI bisa
│       │       menilai kualitas perbandingan prediksi vs realita
│       │   - getRubrik(): string
│       │
│       ├── InvestigatePrompt.php
│       │   - buildEvaluationPrompt(Activity, Submission, array $context): string
│       │     → level (atoms/blocks/relations/macro) diinjeksi untuk mengubah fokus rubrik
│       │   - getRubrik(string $level): string
│       │   - getLevelFocus(string $level): string  ← deskripsi fokus per level
│       │
│       ├── ModifyPrompt.php
│       │   - buildEvaluationPrompt(Activity, Submission, array $context,
│       │                           array $actualOutput, array $expectedOutput): string
│       │     → actualOutput: hasil eksekusi SQL siswa
│       │     → expectedOutput: JSON yang tersimpan di activities.expected_output
│       │     → editor_default_code diinjeksi untuk perbandingan
│       │   - getRubrik(): string
│       │
│       └── MakePrompt.php
│           - buildEvaluationPrompt(Activity, Submission, array $context,
│                                   array $actualOutput, array $expectedOutput): string
│           - getRubrik(): string
│
└── Evaluators/
    ├── NarrativeEvaluator.php
    │   Menangani evaluasi untuk stage: Predict, Run, Investigate
    │   - evaluate(Activity, Submission, array $context): EvaluationResult
    │     → resolve StagePrompt berdasarkan stage
    │     → untuk Run: ambil jawaban Predict sebelumnya dari DB
    │     → panggil GroqClient → ResponseParser
    │
    └── SqlEvaluator.php
        Menangani evaluasi untuk stage: Modify, Make
        - evaluate(Activity, Submission, array $context): EvaluationResult
          → jalankan answer_code ke primmbot_sandbox (READ-ONLY, SELECT saja)
          → jika error: kembalikan skor 20 dengan feedback error SQL
          → bandingkan actual output vs expected_output
          → panggil GroqClient → ResponseParser
```

---

## 8. Perubahan Database

### Tabel `activities` — tambah kolom
```sql
ALTER TABLE activities ADD COLUMN kkm INTEGER NOT NULL DEFAULT 70
    AFTER expected_output;
```
KKM dapat diatur oleh guru dari admin panel per aktivitas. Default 70.

### Tabel `submissions` — tambah kolom + ubah constraint
```sql
-- Tambah kolom skor per indikator
ALTER TABLE submissions
    ADD COLUMN score_keruntutan INTEGER NULL AFTER score,
    ADD COLUMN score_berargumen  INTEGER NULL AFTER score_keruntutan,
    ADD COLUMN score_kesimpulan  INTEGER NULL AFTER score_berargumen,
    ADD COLUMN attempt           INTEGER NOT NULL DEFAULT 1 AFTER score_kesimpulan;

-- Hapus unique constraint (jika ada) agar bisa multi-submit
-- ALTER TABLE submissions DROP INDEX submissions_user_id_activity_id_unique;
```

Kolom `score` sekarang diisi oleh PHP (rata-rata tiga indikator), bukan AI.

### Tabel `ai_interaction_logs` — tambah kolom
```sql
ALTER TABLE ai_interaction_logs
    ADD COLUMN type ENUM('chat', 'submit') NOT NULL DEFAULT 'chat' AFTER activity_id;
```

Pemisahan type penting agar riwayat chat tidak tercampur dengan log evaluasi submit.

---

## 9. Alur Data Lengkap

```
Siswa (Browser)
    │
    ├─[Kirim pesan chat]──────► POST /api/chat
    │                               │
    │                         ChatController::chat()
    │                               │
    │                         Ambil latestSubmission dari DB
    │                         (submissions WHERE user+activity ORDER BY attempt DESC)
    │                               │
    │                         Ambil chatHistory dari DB
    │                         (ai_interaction_logs WHERE type='chat' ORDER BY created_at)
    │                               │
    │                         AIService::chat()
    │                               ├─ ContextLoader::load()
    │                               ├─ ChatPrompt::build()
    │                               └─ GroqClient::call()
    │                               │
    │                         Simpan ke ai_interaction_logs (type='chat')
    │                               │
    │                         Return { response }
    │                         → Tampilkan di chat widget
    │
    └─[Klik Submit]───────────► POST /api/submission/submit
                                    │
                              SubmissionController::submit()
                                    │
                              Hitung attempt = last attempt + 1
                                    │
                              AIService::evaluateSubmission()
                                    ├─ ContextLoader::load()
                                    ├─ [predict/run/investigate]
                                    │       NarrativeEvaluator::evaluate()
                                    │           └─ StagePrompt + GroqClient + ResponseParser
                                    └─ [modify/make]
                                            SqlEvaluator::evaluate()
                                                ├─ Eksekusi SQL → primmbot_sandbox
                                                └─ StagePrompt + GroqClient + ResponseParser
                                    │
                              PHP hitung total score + is_correct
                                    │
                              Simpan record BARU ke submissions
                              (score, score_keruntutan, score_berargumen,
                               score_kesimpulan, attempt, is_correct, ai_feedback)
                                    │
                              Simpan ke ai_interaction_logs (type='submit')
                                    │
                              Return { is_correct, score, feedback }
                              → Tampilkan feedback di chat widget
                              → Jika is_correct: tampilkan tombol Lanjut
```

---

## 10. Konfigurasi & Environment

**File `.env`:**
```env
GROQ_API_KEY=your_groq_api_key_here
GROQ_MODEL=llama-3.1-70b-versatile
```

**File `config/services.php`:**
```php
'groq' => [
    'api_key' => env('GROQ_API_KEY'),
    'model'   => env('GROQ_MODEL', 'llama-3.1-70b-versatile'),
],
```

Setelah mengubah `.env`:
```bash
php artisan config:clear
```

### Mengganti API Key (jika quota habis)
1. Buat API key baru di console.groq.com → API Keys
2. Ganti `GROQ_API_KEY` di `.env`
3. Jalankan `php artisan config:clear`

---

## 11. Fallback (jika API Gagal)

Jika Groq API tidak merespons atau error, sistem tidak boleh crash. Setiap evaluator memiliki fallback:

**NarrativeEvaluator** — scoring berbasis panjang & kandungan teks:
```
Jika jawaban < 15 karakter → skor 20 (terlalu singkat)
Jika jawaban ≥ 15 karakter → cek keyword SQL relevan
    (join, tabel, kolom, select, relasi, primary, foreign, dll)
    skor = 65 + (jumlah keyword × 5), maks 85
```

**SqlEvaluator** — jika SQL error:
```
Return skor 20 dengan feedback: "Query menghasilkan error: [pesan error]"
```

**ChatPrompt** — jika API gagal:
```
Return: "Maaf, asisten virtual sedang tidak tersedia. Coba beberapa saat lagi."
```
