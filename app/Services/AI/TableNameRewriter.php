<?php

namespace App\Services\AI;

use Illuminate\Support\Collection;

/**
 * Helper untuk mengganti nama tabel pendek (display_name) menjadi nama lengkap
 * di database sandbox (format: {prefix}__{nama_tabel}).
 *
 * Contoh: "products" → "toko_ayam__products"
 *
 * Logika ini sebelumnya duplikat di SqlRunnerController dan SqlEvaluator.
 * Sekarang ada di satu tempat — ubah di sini, berlaku di keduanya.
 */
class TableNameRewriter
{
    /**
     * Bangun peta nama pendek → nama lengkap dari koleksi sandbox tables.
     *
     * Setiap tabel bisa dipanggil dengan dua cara oleh siswa:
     *   - display_name          → "products"
     *   - bagian setelah "__"   → "products" (sama, sebagai alias fallback)
     *
     * @param  Collection  $sandboxTables  Koleksi SandboxTable dari DB
     * @return array  ['products' => 'toko_ayam__products', ...]
     */
    public static function buildMap(Collection $sandboxTables): array
    {
        $tableMap = [];

        foreach ($sandboxTables as $t) {
            // Map dari display_name, misal: "products"
            $tableMap[strtolower($t->display_name)] = $t->table_name;

            // Map juga dari bagian setelah __ sebagai alias tambahan
            // Contoh: "toko_ayam__products" → split → "products" juga valid
            $parts = explode('__', $t->table_name, 2);
            if (isset($parts[1])) {
                $tableMap[strtolower($parts[1])] = $t->table_name;
            }
        }

        return $tableMap;
    }

    /**
     * Ganti semua nama pendek dalam query SQL dengan nama lengkap.
     *
     * Kenapa diurutkan dari nama terpanjang?
     * Agar nama seperti "orders_detail" tidak ter-replace sebagian oleh "orders".
     *
     * @param  string  $query     Query SQL dari siswa
     * @param  array   $tableMap  Hasil buildMap()
     * @return string  Query dengan nama tabel lengkap
     */
    public static function rewrite(string $query, array $tableMap): string
    {
        // Urutkan: nama terpanjang diproses duluan
        uksort($tableMap, fn($a, $b) => strlen($b) - strlen($a));

        foreach ($tableMap as $shortName => $fullName) {
            // Skip jika nama pendek dan nama lengkap sudah sama
            if (strtolower($shortName) === strtolower($fullName)) {
                continue;
            }

            // Ganti nama dalam backtick: `products` → `toko_ayam__products`
            $query = preg_replace(
                '/`' . preg_quote($shortName, '/') . '`/i',
                "`{$fullName}`",
                $query
            );

            // Ganti nama tanpa backtick (word boundary):
            // FROM products → FROM toko_ayam__products
            $query = preg_replace(
                '/\b' . preg_quote($shortName, '/') . '\b/i',
                $fullName,
                $query
            );
        }

        return $query;
    }
}
