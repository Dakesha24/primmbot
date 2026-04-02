<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    'groq' => [
        // Daftarkan sebanyak yang dibutuhkan — key null (belum diisi di .env) otomatis diabaikan.
        // Urutan = prioritas: key-1 dipakai pertama, key-2 backup, key-3 cadangan, dst.
        'api_keys' => array_values(array_filter([
            env('GROQ_API_KEY'),    // akun utama
            env('GROQ_API_KEY_2'),  // backup 1
            env('GROQ_API_KEY_3'),  // backup 2 (opsional)
        ])),
        'model' => env('GROQ_MODEL', 'openai/gpt-oss-120b'),
    ],

];
