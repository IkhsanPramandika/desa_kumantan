<?php

namespace App\Interfaces;

use App\Models\Masyarakat; // atau model Masyarakat jika pemohon adalah masyarakat

interface PermohonanInterface
{
    /**
     * Mendapatkan judul notifikasi yang spesifik untuk jenis permohonan ini.
     * Contoh: "Permohonan KK Baru"
     */
    public function getJudulNotifikasi(): string;

    /**
     * Mendapatkan objek User (pemohon) yang terkait dengan permohonan ini.
     */
    public function getPemohon(): Masyarakat;

    /**
     * Mendapatkan ID unik dari permohonan.
     */
    public function getId(): int;

    /**
     * Mendapatkan route tujuan saat notifikasi diklik.
     */
    public function getRouteTujuan(): string;
}