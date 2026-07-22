<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class LaboratoriumRepository
{
    /**
     * Get query for Laboratory data based on status (ralan/ranap)
     */
    public function getLabQuery($status, $startDate, $endDate, $kd_pj = null, $ketepatan = null)
    {
        $query = DB::table('permintaan_lab')
            ->join('reg_periksa', 'permintaan_lab.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->leftJoin('permintaan_detail_permintaan_lab', 'permintaan_lab.noorder', '=', 'permintaan_detail_permintaan_lab.noorder')
            ->leftJoin('jns_perawatan_lab', 'permintaan_detail_permintaan_lab.kd_jenis_prw', '=', 'jns_perawatan_lab.kd_jenis_prw')
            ->whereBetween('permintaan_lab.tgl_sampel', [$startDate, $endDate]);

        if ($status !== 'gabungan') {
            $query->where('permintaan_lab.status', $status);
        }

        if ($kd_pj) {
            $query->where('reg_periksa.kd_pj', $kd_pj);
        }

        if ($ketepatan) {
            $diffSql = "TIMESTAMPDIFF(SECOND, CONCAT(permintaan_lab.tgl_sampel, ' ', permintaan_lab.jam_sampel), CONCAT(permintaan_lab.tgl_hasil, ' ', permintaan_lab.jam_hasil))";
            if ($ketepatan === 'tepat') {
                $query->whereRaw("$diffSql < 3600");
            } elseif ($ketepatan === 'tidak_tepat') {
                $query->whereRaw("$diffSql >= 3600");
            }
        }

        return $query->select([
                'permintaan_lab.noorder',
                'permintaan_lab.tgl_sampel',
                'pasien.nm_pasien',
                'pasien.no_rkm_medis',
                'permintaan_lab.jam_sampel',
                'permintaan_lab.tgl_hasil',
                'permintaan_lab.jam_hasil',
                'permintaan_lab.no_rawat',
                'permintaan_lab.status',
                'penjab.png_jawab',
                DB::raw("GROUP_CONCAT(DISTINCT jns_perawatan_lab.nm_perawatan SEPARATOR ', ') as pemeriksaan"),
                DB::raw("TIMEDIFF(CONCAT(permintaan_lab.tgl_hasil, ' ', permintaan_lab.jam_hasil), CONCAT(permintaan_lab.tgl_sampel, ' ', permintaan_lab.jam_sampel)) as total_waktu")
            ])
            ->groupBy('permintaan_lab.noorder', 'permintaan_lab.tgl_sampel', 'pasien.nm_pasien', 'pasien.no_rkm_medis', 'permintaan_lab.jam_sampel', 'permintaan_lab.tgl_hasil', 'permintaan_lab.jam_hasil', 'permintaan_lab.no_rawat', 'permintaan_lab.status', 'penjab.png_jawab')
            ->orderBy('permintaan_lab.tgl_sampel', 'desc')
            ->orderBy('permintaan_lab.jam_sampel', 'desc');
    }
}
