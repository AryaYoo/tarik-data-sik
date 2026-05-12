<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class FarmasiRepository
{
    public function getWaktuTungguRalan($startDate, $endDate, $kd_pj = null)
    {
        $query = DB::table('resep_obat')
            ->join('reg_periksa', 'resep_obat.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->where('resep_obat.status', 'ralan')
            ->whereBetween('resep_obat.tgl_perawatan', [$startDate, $endDate]);

        if ($kd_pj) {
            $query->where('reg_periksa.kd_pj', $kd_pj);
        }

        return $query->select([
                'pasien.nm_pasien',
                'pasien.no_rkm_medis',
                'penjab.png_jawab',
                'resep_obat.tgl_perawatan',
                'resep_obat.jam as jam_validasi',
                'resep_obat.tgl_penyerahan',
                'resep_obat.jam_penyerahan',
                DB::raw("TIMEDIFF(
                    CONCAT(resep_obat.tgl_penyerahan, ' ', resep_obat.jam_penyerahan), 
                    CONCAT(resep_obat.tgl_perawatan, ' ', resep_obat.jam)
                ) as total_waktu")
            ])
            ->orderBy('resep_obat.tgl_perawatan', 'desc')
            ->orderBy('resep_obat.jam', 'desc');
    }

    public function getAverageWaktuTunggu($startDate, $endDate, $kd_pj = null)
    {
        $query = DB::table('resep_obat')
            ->join('reg_periksa', 'resep_obat.no_rawat', '=', 'reg_periksa.no_rawat')
            ->where('resep_obat.status', 'ralan')
            ->whereBetween('resep_obat.tgl_perawatan', [$startDate, $endDate]);

        if ($kd_pj) {
            $query->where('reg_periksa.kd_pj', $kd_pj);
        }

        return $query->select(
            DB::raw("AVG(TIME_TO_SEC(TIMEDIFF(
                CONCAT(resep_obat.tgl_penyerahan, ' ', resep_obat.jam_penyerahan), 
                CONCAT(resep_obat.tgl_perawatan, ' ', resep_obat.jam)
            ))) as avg_seconds")
        )->first()->avg_seconds;
    }
}
