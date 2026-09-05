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
    /**
     * Kode periksa paket / internal yang secara default TIDAK ditampilkan (sesuai request)
     */
    public static $defaultExcludedKode = [
        'XBPJS',
        'XBPJS_1',
        'XBPJS_10',
        'XBPJS_15',
        'XBPJS_16',
        'XBPJS_17',
        'XBPJS_18',
        'XBPJS_2',
        'XBPJS_20',
        'XBPJS_21',
        'XBPJS_24',
        'XBPJS_25',
        'XBPJS_26',
        'XBPJS_28',
        'XBPJS_29',
        'XBPJS_3',
        'XBPJS_30',
        'XBPJS_31',
        'XBPJS_4',
        'XBPJS_5',
        'XBPJS_6',
        'XBPJS_8',
        'XBPJS_9',
        'BPJS_BRWJY 1',
        'BPJS_DKT30',
        'BPJS_GR32',
        'BPJS_KD 32',
        'LIBI_DKT29',
        'LIBI_GR31',
    ];

    /**
     * Get all available kode periksa (kd_jenis_prw) from jns_perawatan_lab.
     * Used to populate the settings modal filter.
     */
    public function getAvailableKodePeriksa()
    {
        return DB::table('jns_perawatan_lab')
            ->select('kd_jenis_prw', 'nm_perawatan')
            ->orderBy('kd_jenis_prw', 'asc')
            ->get();
    }

    /**
     * Get query for Kategori Pasien Laboratorium
     * Kategori usia:
     *   - Neonatus : < 1 bulan
     *   - Bayi     : 1 – 11 bulan
     *   - Anak     : 1 – 17 tahun
     *   - Dewasa   : > 17 tahun
     *
     * @param string      $startDate
     * @param string      $endDate
     * @param string|null $kd_pj              Filter jenis pembayar
     * @param string|null $kategori_usia      Filter kategori usia
     * @param array|null  $kode_periksa_list  Filter kode jenis perawatan lab
     */
    public function getKategoriPasienQuery($startDate, $endDate, $kd_pj = null, $kategori_usia = null, $kode_periksa_list = null)
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

        // Filter berdasarkan kode periksa yang dipilih user (setting kode periksa)
        if (!empty($kode_periksa_list) && is_array($kode_periksa_list)) {
            $query->whereIn('jns_perawatan_lab.kd_jenis_prw', $kode_periksa_list);
        } else {
            // Default: jangan tampilkan kode periksa paket / non-standar (XBPJS, LIBI, dll)
            $query->whereNotIn('jns_perawatan_lab.kd_jenis_prw', self::$defaultExcludedKode);
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

    /**
     * Dapatkan daftar pasien unik berdasarkan No. RM atau Nama Pasien
     */
    public function getUniquePatients($collection)
    {
        $unique   = collect();
        $seenRm   = [];
        $seenNama = [];

        foreach ($collection as $item) {
            $rm   = trim((string) ($item->no_rkm_medis ?? ''));
            $nama = strtoupper(trim((string) ($item->nm_pasien ?? '')));

            $isMatch = false;
            if ($rm !== '' && $rm !== '-' && isset($seenRm[$rm])) {
                $isMatch = true;
            }
            if ($nama !== '' && isset($seenNama[$nama])) {
                $isMatch = true;
            }

            if (!$isMatch) {
                if ($rm !== '' && $rm !== '-') {
                    $seenRm[$rm] = true;
                }
                if ($nama !== '') {
                    $seenNama[$nama] = true;
                }
                $unique->push($item);
            }
        }

        return $unique;
    }

    /**
     * Hitung summary kategori pasien (pasien unik dihitung berdasarkan No. RM atau Nama Pasien)
     */
    public function calculateKategoriPasienSummary($collection)
    {
        $unique = $this->getUniquePatients($collection);

        return [
            'neonatus' => $unique->where('kategori_usia', 'Neonatus')->count(),
            'bayi'     => $unique->where('kategori_usia', 'Bayi')->count(),
            'anak'     => $unique->where('kategori_usia', 'Anak')->count(),
            'dewasa'   => $unique->where('kategori_usia', 'Dewasa')->count(),
            'total'    => $unique->count(),
        ];
    }
}

