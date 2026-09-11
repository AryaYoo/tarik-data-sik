<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class RekamMedisRepository
{
    /**
     * Mengambil daftar poliklinik
     */
    public function getPoliklinik()
    {
        return DB::table('poliklinik')
            ->select('kd_poli', 'nm_poli')
            ->orderBy('nm_poli', 'asc')
            ->get();
    }

    /**
     * Mengambil daftar penjamin / cara bayar
     */
    public function getPenjamin()
    {
        return DB::table('penjab')
            ->select('kd_pj', 'png_jawab')
            ->orderBy('png_jawab', 'asc')
            ->get();
    }

    /**
     * Query data kunjungan pasien RS
     *
     * @param string $startDate
     * @param string $endDate
     * @param string|null $status_lanjut (Ralan / Ranap)
     * @param string|null $kd_poli
     * @param string|null $kd_pj
     * @return \Illuminate\Database\Query\Builder
     */
    public function getKunjunganRsQuery($startDate, $endDate, $status_lanjut = null, $kd_poli = null, $kd_pj = null)
    {
        $query = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->leftJoin('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->leftJoin('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->leftJoin('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->whereBetween('reg_periksa.tgl_registrasi', [$startDate, $endDate]);

        if (!empty($status_lanjut)) {
            $query->where('reg_periksa.status_lanjut', $status_lanjut);
        }

        if (!empty($kd_poli)) {
            $query->where('reg_periksa.kd_poli', $kd_poli);
        }

        if (!empty($kd_pj)) {
            $query->where('reg_periksa.kd_pj', $kd_pj);
        }

        return $query->select([
            'reg_periksa.no_rawat',
            'reg_periksa.tgl_registrasi',
            'reg_periksa.jam_reg',
            'reg_periksa.no_rkm_medis',
            'pasien.nm_pasien',
            'pasien.jk',
            'poliklinik.nm_poli',
            'dokter.nm_dokter',
            'penjab.png_jawab',
            'reg_periksa.stts_daftar as jns_kunjungan',
            'reg_periksa.status_lanjut',
        ])
        ->orderBy('reg_periksa.tgl_registrasi', 'desc')
        ->orderBy('reg_periksa.jam_reg', 'desc');
    }
}
