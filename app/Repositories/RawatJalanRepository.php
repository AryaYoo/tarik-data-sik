<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class RawatJalanRepository
{
    public function getPoliklinik()
    {
        return DB::table('poliklinik')
            ->select('kd_poli', 'nm_poli')
            ->orderBy('nm_poli', 'asc')
            ->get();
    }

    public function getAlamatDanKontak($startDate, $endDate, $kd_poli = null)
    {
        $query = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->where('reg_periksa.status_lanjut', 'Ralan')
            ->whereBetween('reg_periksa.tgl_registrasi', [$startDate, $endDate]);

        if ($kd_poli) {
            $query->where('reg_periksa.kd_poli', $kd_poli);
        }

        return $query->select([
                'reg_periksa.no_rkm_medis',
                'pasien.nm_pasien',
                'poliklinik.nm_poli',
                'pasien.alamat',
                'pasien.no_tlp'
            ])
            ->orderBy('reg_periksa.tgl_registrasi', 'desc')
            ->orderBy('reg_periksa.jam_reg', 'desc');
    }
}
