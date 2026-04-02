<?php

/*
|--------------------------------------------------------------------------
| Konfigurasi AI Service
|--------------------------------------------------------------------------
| Semua magic value terkait AI dan sandbox dikumpulkan di sini.
| Ubah di satu tempat → berlaku di seluruh aplikasi.
*/

return [

    // ── Groq API ─────────────────────────────────────────────────────────────

    // Timeout koneksi ke Groq API (detik)
    'timeout' => 20,

    // Rate limit akun Groq (sesuaikan jika plan berubah)
    // Sumber: console.groq.com → Settings → Limits
    'rate_limits' => [
        'rpm'  => 30,       // Requests per Minute
        'rpd'  => 1000,     // Requests per Day
        'tpm'  => 8000,     // Tokens per Minute
        'tpd'  => 200000,   // Tokens per Day
    ],

    // Token maksimum untuk evaluasi jawaban siswa
    'eval_max_tokens' => 300,

    // Token maksimum untuk respons chat virtual assistant
    'chat_max_tokens' => 300,

    // Temperature evaluasi: rendah = deterministik/konsisten (cocok untuk rubrik)
    'eval_temperature' => 0.2,

    // Temperature chat: lebih tinggi = respons lebih natural/bervariasi
    'chat_temperature' => 0.7,

    // ── Konteks Prompt ───────────────────────────────────────────────────────

    // Batas karakter ringkasan materi yang disertakan di prompt AI (hemat token)
    'material_context_limit' => 800,

    // Jumlah riwayat chat yang disertakan dalam prompt (pasang Siswa + AI)
    // Contoh: 10 = 5 pasang percakapan terakhir
    'chat_history_limit' => 10,

    // ── Validasi Jawaban Pre-AI ───────────────────────────────────────────────

    // Panjang minimum jawaban teks agar tidak ditolak langsung (karakter)
    'min_answer_length' => 20,

    // Panjang minimum untuk scoring fallback keyword-based (karakter)
    'min_answer_length_fallback' => 15,

    // Jumlah kata minimum dalam jawaban
    'min_word_count' => 3,

    // Rasio minimum huruf terhadap total karakter (filter jawaban acak/spam)
    'min_letter_ratio' => 0.5,

    // ── Sandbox ──────────────────────────────────────────────────────────────

    // Baris maksimum yang ditampilkan sebagai preview SELECT setelah DML
    'sandbox_preview_limit' => 50,

];
