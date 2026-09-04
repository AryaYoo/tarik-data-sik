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

    /**
     * Get query for Kategori Pasien Laboratorium
     * Kategori usia:
     *   - Neonatus : < 1 bulan
     *   - Bayi     : 1 – 11 bulan
     *   - Anak     : 1 – 17 tahun
     *   - Dewasa   : > 17 tahun
     */
    public function getKategoriPasienQuery($startDate, $endDate, $kd_pj = null, $kategori_usia = null)
    {
        $query = DB::table('permintaan_lab')
            ->join('reg_periksa', 'permintaan_lab.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->leftJoin('permintaan_detail_permintaan_lab', 'permintaan_lab.noorder', '=', 'permintaan_detail_permintaan_lab.noorder')
            ->leftJoin('jns_perawatan_lab', 'permintaan_detail_permintaan_lab.kd_jenis_prw', '=', 'jns_perawatan_lab.kd_jenis_prw')
            ->whereBetween('permintaan_lab.tgl_sampel', [$startDate, $endDate]);

        if ($kd_pj) {
            $query->where('reg_periksa.kd_pj', $kd_pj);
        }

        // Filter berdasarkan kategori usia menggunakan TIMESTAMPDIFF dari tgl_lahir ke tgl_sampel
        if ($kategori_usia) {
            switch ($kategori_usia) {
                case 'neonatus':
                    // < 1 bulan
                    $query->whereRaw("TIMESTAMPDIFF(MONTH, pasien.tgl_lahir, permintaan_lab.tgl_sampel) < 1");
                    break;
                case 'bayi':
                    // 1 – 11 bulan
                    $query->whereRaw("TIMESTAMPDIFF(MONTH, pasien.tgl_lahir, permintaan_lab.tgl_sampel) BETWEEN 1 AND 11");
                    break;
                case 'anak':
                    // 12 bulan s/d 17 tahun
                    $query->whereRaw("TIMESTAMPDIFF(MONTH, pasien.tgl_lahir, permintaan_lab.tgl_sampel) >= 12")
                          ->whereRaw("TIMESTAMPDIFF(YEAR, pasien.tgl_lahir, permintaan_lab.tgl_sampel) <= 17");
                    break;
                case 'dewasa':
                    // > 17 tahun
                    $query->whereRaw("TIMESTAMPDIFF(YEAR, pasien.tgl_lahir, permintaan_lab.tgl_sampel) > 17");
                    break;
            }
        }

        return $query->select([
                'permintaan_lab.noorder',
                'permintaan_lab.tgl_sampel',
                'permintaan_lab.no_rawat',
                'permintaan_lab.status',
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.tgl_lahir',
                'penjab.png_jawab',
                DB::raw("TIMESTAMPDIFF(YEAR, pasien.tgl_lahir, permintaan_lab.tgl_sampel) as umur_tahun"),
                DB::raw("TIMESTAMPDIFF(MONTH, pasien.tgl_lahir, permintaan_lab.tgl_sampel) as umur_bulan_total"),
                DB::raw("GROUP_CONCAT(DISTINCT jns_perawatan_lab.nm_perawatan ORDER BY jns_perawatan_lab.nm_perawatan SEPARATOR ', ') as pemeriksaan"),
                DB::raw("
                    CASE
                        WHEN TIMESTAMPDIFF(MONTH, pasien.tgl_lahir, permintaan_lab.tgl_sampel) < 1 THEN 'Neonatus'
                        WHEN TIMESTAMPDIFF(MONTH, pasien.tgl_lahir, permintaan_lab.tgl_sampel) BETWEEN 1 AND 11 THEN 'Bayi'
                        WHEN TIMESTAMPDIFF(YEAR, pasien.tgl_lahir, permintaan_lab.tgl_sampel) BETWEEN 1 AND 17 THEN 'Anak'
                        ELSE 'Dewasa'
                    END as kategori_usia
                "),
            ])
            ->groupBy(
                'permintaan_lab.noorder',
                'permintaan_lab.tgl_sampel',
                'permintaan_lab.no_rawat',
                'permintaan_lab.status',
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.tgl_lahir',
                'penjab.png_jawab'
            )
            ->orderBy('permintaan_lab.tgl_sampel', 'desc')
            ->orderBy('pasien.nm_pasien', 'asc');
    }
}
