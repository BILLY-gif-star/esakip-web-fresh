<?php

namespace App\Helpers;

class TextHelper
{
    /**
     * Escape teks lalu ubah URL (http/https) yang ditemukan di dalamnya
     * menjadi link <a> yang bisa diklik.
     *
     * PENTING soal keamanan: escaping HTML dilakukan SEBELUM URL diubah
     * jadi tag <a>, jadi kalau ada orang yang sengaja nulis kode HTML/script
     * di catatan, itu tetap ke-escape jadi teks biasa (tidak dieksekusi
     * browser). Hanya pola URL asli yang dibungkus jadi link.
     */
    public static function linkify(?string $text): string
    {
        if (!$text) {
            return '';
        }

        // 1. Escape dulu SEMUA karakter HTML di teks asli (cegah XSS)
        $escaped = e($text);

        // 2. Cari pola URL (http/https) di teks yang sudah di-escape,
        //    lalu bungkus jadi <a>.
        $pattern = '/(https?:\/\/[^\s<]+)/i';

        return preg_replace_callback($pattern, function ($matches) {
            $url = $matches[1];

            // Buang tanda baca penutup kalimat yang nyangkut di ujung URL
            // (misal titik/koma kalau link ditulis di akhir kalimat)
            $trailing = '';
            if (preg_match('/[.,;:!?\)\]]+$/', $url, $m)) {
                $trailing = $m[0];
                $url = substr($url, 0, -strlen($trailing));
            }

            return '<a href="' . $url . '" target="_blank" rel="noopener noreferrer" '
                 . 'style="color:#93c5fd;text-decoration:underline;">' . $url . '</a>' . $trailing;
        }, $escaped);
    }
}