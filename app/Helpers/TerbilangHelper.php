<?php

namespace App\Helpers;

class TerbilangHelper
{
    /**
     * Convert number to words in Indonesian
     *
     * @param int $angka
     * @return string
     */
    public static function convert($angka)
    {
        $satuan = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];

        $angka = (int)$angka;

        if ($angka < 0 || !is_numeric($angka)) {
            return "Nol";
        }

        if ($angka < 12) {
            return $satuan[$angka];
        }

        if ($angka < 20) {
            return $satuan[$angka - 10] . " Belas";
        }

        if ($angka < 100) {
            return self::convert(floor($angka / 10)) . " Puluh " . self::convert($angka % 10);
        }

        if ($angka < 200) {
            return "Seratus " . self::convert($angka - 100);
        }

        if ($angka < 1000) {
            return self::convert(floor($angka / 100)) . " Ratus " . self::convert($angka % 100);
        }

        if ($angka < 2000) {
            return "Seribu " . self::convert($angka - 1000);
        }

        if ($angka < 1000000) {
            return self::convert(floor($angka / 1000)) . " Ribu " . self::convert($angka % 1000);
        }

        if ($angka < 1000000000) {
            return self::convert(floor($angka / 1000000)) . " Juta " . self::convert($angka % 1000000);
        }

        return "Angka terlalu besar";
    }
}
