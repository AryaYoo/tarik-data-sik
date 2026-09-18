<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class BedahSentralRepository
{
    /**
     * Query data pasien OK dengan diagnosa indikasi NST (ICD-10) dan/atau prosedur tindakan NST (ICD-9 CM).
     *
     * Berdasarkan standar kebidanan & rekam medis:
     * 1. ICD-9 CM (Prosedur Tindakan):
     *    - 75.34: Fetal monitoring / Pemantauan janin (termasuk pemeriksaan DJJ dan NST / CTG)
     * 2. ICD-10 (Diagnosis Indikasi Medis):
     *    - O68 / 068: Gawat janin / Fetal distress saat persalinan (Labour/delivery complicated by fetal stress)
     *    - O36.83: Kelainan detak atau irama jantung janin (antepartum)
     *    - O36.3: Tanda-tanda hipoksia/gawat janin sebelum persalinan
     *    - O41.0: Kondisi Oligohidramnion (air ketuban sedikit)
     *    - O48: Kehamilan lewat waktu / post-term (lewat HPL)
     *    - Z35.9 / Z35: Pengawasan kehamilan risiko tinggi
     *
     * Diagnosa non-NST (seperti A00, I50, K30, dll) secara ketat dikecualikan.
     *
     * @param string      $startDate
     * @param string      $endDate
     * @param string|null $status_operasi (Menunggu / Proses Operasi / Selesai)
     * @param string|null $jenis_nst     Filter spesifik (all / icd10 / icd9 / O68 / O36.83 / O36.3 / O41.0 / O48 / Z35.9 / 75.34)
     * @return \Illuminate\Database\Query\Builder
     */
    public function getDataOkNstQuery($startDate, $endDate, $status_operasi = null, $jenis_nst = 'all')
    {
        // Query 1: Diagnosa Indikasi Medis NST (ICD-10) dari diagnosa_pasien + penyakit
        $queryIcd10 = DB::table('booking_operasi')
            ->join('reg_periksa', 'booking_operasi.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('diagnosa_pasien', 'booking_operasi.no_rawat', '=', 'diagnosa_pasien.no_rawat')
            ->join('penyakit', 'diagnosa_pasien.kd_penyakit', '=', 'penyakit.kd_penyakit')
            ->leftJoin('paket_operasi', 'booking_operasi.kode_paket', '=', 'paket_operasi.kode_paket')
            ->whereBetween('booking_operasi.tanggal', [$startDate, $endDate])
            ->where(function ($q) use ($jenis_nst) {
                if ($jenis_nst === 'O68') {
                    $q->where('penyakit.kd_penyakit', 'LIKE', 'O68%')
                      ->orWhere('penyakit.kd_penyakit', 'LIKE', '068%');
                } elseif ($jenis_nst === 'O36.83') {
                    $q->where('penyakit.kd_penyakit', 'LIKE', 'O36.8%')
                      ->orWhere('penyakit.kd_penyakit', 'LIKE', '036.8%');
                } elseif ($jenis_nst === 'O36.3') {
                    $q->where('penyakit.kd_penyakit', 'LIKE', 'O36.3%')
                      ->orWhere('penyakit.kd_penyakit', 'LIKE', '036.3%');
                } elseif ($jenis_nst === 'O41.0') {
                    $q->where('penyakit.kd_penyakit', 'LIKE', 'O41.0%')
                      ->orWhere('penyakit.kd_penyakit', 'LIKE', '041.0%');
                } elseif ($jenis_nst === 'O48') {
                    $q->where('penyakit.kd_penyakit', 'LIKE', 'O48%')
                      ->orWhere('penyakit.kd_penyakit', 'LIKE', '048%');
                } elseif ($jenis_nst === 'Z35.9') {
                    $q->where('penyakit.kd_penyakit', 'LIKE', 'Z35%');
                } else {
                    // Default seluruh ICD-10 NST
                    $q->where('penyakit.kd_penyakit', 'LIKE', 'O68%')
                      ->orWhere('penyakit.kd_penyakit', 'LIKE', '068%')
                      ->orWhere('penyakit.kd_penyakit', 'LIKE', 'O36.8%')
                      ->orWhere('penyakit.kd_penyakit', 'LIKE', '036.8%')
                      ->orWhere('penyakit.kd_penyakit', 'LIKE', 'O36.3%')
                      ->orWhere('penyakit.kd_penyakit', 'LIKE', '036.3%')
                      ->orWhere('penyakit.kd_penyakit', 'LIKE', 'O41.0%')
                      ->orWhere('penyakit.kd_penyakit', 'LIKE', '041.0%')
                      ->orWhere('penyakit.kd_penyakit', 'LIKE', 'O48%')
                      ->orWhere('penyakit.kd_penyakit', 'LIKE', '048%')
                      ->orWhere('penyakit.kd_penyakit', 'LIKE', 'Z35.9%')
                      ->orWhere('penyakit.kd_penyakit', 'LIKE', 'Z35%')
                      ->orWhere('penyakit.kd_penyakit', 'LIKE', '%NST%')
                      ->orWhere('penyakit.nm_penyakit', 'LIKE', '%NST%')
                      ->orWhere('penyakit.nm_penyakit', 'LIKE', '%Non Stress Test%')
                      ->orWhere('penyakit.nm_penyakit', 'LIKE', '%Fetal Distress%')
                      ->orWhere('penyakit.nm_penyakit', 'LIKE', '%Gawat Janin%');
                }
            });

        if (!empty($status_operasi)) {
            $queryIcd10->where('booking_operasi.status', $status_operasi);
        }

        $queryIcd10->select([
            'pasien.no_rkm_medis',
            'pasien.nm_pasien',
            'booking_operasi.no_rawat',
            'penyakit.kd_penyakit as kode_diagnosa',
            'penyakit.nm_penyakit as nama_diagnosa',
            DB::raw("'ICD-10 (Indikasi)' as tipe_nst"),
            'diagnosa_pasien.prioritas',
            'paket_operasi.nm_perawatan as nama_paket',
            'booking_operasi.tanggal as tgl_operasi',
            'booking_operasi.jam_mulai',
            'booking_operasi.jam_selesai',
            'booking_operasi.status as status_operasi',
        ]);

        // Query 2: Tindakan Prosedur NST (ICD-9 CM: 75.34 Fetal monitoring / DJJ / CTG)
        $queryIcd9 = DB::table('booking_operasi')
            ->join('reg_periksa', 'booking_operasi.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('prosedur_pasien', 'booking_operasi.no_rawat', '=', 'prosedur_pasien.no_rawat')
            ->join('icd9', 'prosedur_pasien.kode', '=', 'icd9.kode')
            ->leftJoin('paket_operasi', 'booking_operasi.kode_paket', '=', 'paket_operasi.kode_paket')
            ->whereBetween('booking_operasi.tanggal', [$startDate, $endDate])
            ->where(function ($q) {
                $q->where('icd9.kode', 'LIKE', '75.34%')
                  ->orWhere('icd9.kode', 'LIKE', '75.3%')
                  ->orWhere('icd9.deskripsi_panjang', 'LIKE', '%fetal monitoring%')
                  ->orWhere('icd9.deskripsi_panjang', 'LIKE', '%NST%');
            });

        if (!empty($status_operasi)) {
            $queryIcd9->where('booking_operasi.status', $status_operasi);
        }

        $queryIcd9->select([
            'pasien.no_rkm_medis',
            'pasien.nm_pasien',
            'booking_operasi.no_rawat',
            'icd9.kode as kode_diagnosa',
            DB::raw("COALESCE(icd9.deskripsi_panjang, icd9.deskripsi_pendek) as nama_diagnosa"),
            DB::raw("'ICD-9 CM (Prosedur)' as tipe_nst"),
            'prosedur_pasien.prioritas',
            'paket_operasi.nm_perawatan as nama_paket',
            'booking_operasi.tanggal as tgl_operasi',
            'booking_operasi.jam_mulai',
            'booking_operasi.jam_selesai',
            'booking_operasi.status as status_operasi',
        ]);

        // Jika filter khusus ICD-9 CM
        if ($jenis_nst === '75.34' || $jenis_nst === 'icd9') {
            return $queryIcd9->orderBy('booking_operasi.tanggal', 'desc')
                ->orderBy('booking_operasi.jam_mulai', 'asc')
                ->orderBy('prosedur_pasien.prioritas', 'asc');
        }

        // Jika filter kode ICD-10 spesifik
        if ($jenis_nst && $jenis_nst !== 'all') {
            return $queryIcd10->orderBy('booking_operasi.tanggal', 'desc')
                ->orderBy('booking_operasi.jam_mulai', 'asc')
                ->orderBy('diagnosa_pasien.prioritas', 'asc');
        }

        // Jika 'all', gabungkan ICD-10 dan ICD-9 CM via UNION
        return $queryIcd10->union($queryIcd9)
            ->orderBy('tgl_operasi', 'desc')
            ->orderBy('jam_mulai', 'asc');
    }

    /**
     * Hitung jumlah kasus per kategori / kode NST untuk periode tertentu.
     * Digunakan untuk menampilkan ringkasan kategori pada halaman Data OK NST.
     *
     * Mengembalikan collection of objects dengan properti:
     *   - kode_diagnosa  : kode ICD-10 / ICD-9 CM
     *   - nama_diagnosa  : nama/deskripsi singkat diagnosa
     *   - tipe_nst       : 'ICD-10 (Indikasi)' | 'ICD-9 CM (Prosedur)'
     *   - jumlah         : jumlah kasus pada periode
     *
     * @param string      $startDate
     * @param string      $endDate
     * @param string|null $status_operasi
     * @return \Illuminate\Support\Collection
     */
    public function getDataOkNstSummary($startDate, $endDate, $status_operasi = null)
    {
        // ── Bagian ICD-10 ──────────────────────────────────────────────────────
        $icd10 = DB::table('booking_operasi')
            ->join('reg_periksa', 'booking_operasi.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('diagnosa_pasien', 'booking_operasi.no_rawat', '=', 'diagnosa_pasien.no_rawat')
            ->join('penyakit', 'diagnosa_pasien.kd_penyakit', '=', 'penyakit.kd_penyakit')
            ->whereBetween('booking_operasi.tanggal', [$startDate, $endDate])
            ->where(function ($q) {
                $q->where('penyakit.kd_penyakit', 'LIKE', 'O68%')
                  ->orWhere('penyakit.kd_penyakit', 'LIKE', '068%')
                  ->orWhere('penyakit.kd_penyakit', 'LIKE', 'O36.8%')
                  ->orWhere('penyakit.kd_penyakit', 'LIKE', '036.8%')
                  ->orWhere('penyakit.kd_penyakit', 'LIKE', 'O36.3%')
                  ->orWhere('penyakit.kd_penyakit', 'LIKE', '036.3%')
                  ->orWhere('penyakit.kd_penyakit', 'LIKE', 'O41.0%')
                  ->orWhere('penyakit.kd_penyakit', 'LIKE', '041.0%')
                  ->orWhere('penyakit.kd_penyakit', 'LIKE', 'O48%')
                  ->orWhere('penyakit.kd_penyakit', 'LIKE', '048%')
                  ->orWhere('penyakit.kd_penyakit', 'LIKE', 'Z35%')
                  ->orWhere('penyakit.nm_penyakit', 'LIKE', '%NST%')
                  ->orWhere('penyakit.nm_penyakit', 'LIKE', '%Non Stress Test%')
                  ->orWhere('penyakit.nm_penyakit', 'LIKE', '%Fetal Distress%')
                  ->orWhere('penyakit.nm_penyakit', 'LIKE', '%Gawat Janin%');
            });

        if (!empty($status_operasi)) {
            $icd10->where('booking_operasi.status', $status_operasi);
        }

        $icd10->select([
            'penyakit.kd_penyakit as kode_diagnosa',
            'penyakit.nm_penyakit as nama_diagnosa',
            DB::raw("'ICD-10 (Indikasi)' as tipe_nst"),
            DB::raw('COUNT(*) as jumlah'),
        ])->groupBy('penyakit.kd_penyakit', 'penyakit.nm_penyakit');

        // ── Bagian ICD-9 CM ────────────────────────────────────────────────────
        $icd9 = DB::table('booking_operasi')
            ->join('reg_periksa', 'booking_operasi.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('prosedur_pasien', 'booking_operasi.no_rawat', '=', 'prosedur_pasien.no_rawat')
            ->join('icd9', 'prosedur_pasien.kode', '=', 'icd9.kode')
            ->whereBetween('booking_operasi.tanggal', [$startDate, $endDate])
            ->where(function ($q) {
                $q->where('icd9.kode', 'LIKE', '75.34%')
                  ->orWhere('icd9.kode', 'LIKE', '75.3%')
                  ->orWhere('icd9.deskripsi_panjang', 'LIKE', '%fetal monitoring%')
                  ->orWhere('icd9.deskripsi_panjang', 'LIKE', '%NST%');
            });

        if (!empty($status_operasi)) {
            $icd9->where('booking_operasi.status', $status_operasi);
        }

        $icd9->select([
            'icd9.kode as kode_diagnosa',
            DB::raw("COALESCE(icd9.deskripsi_panjang, icd9.deskripsi_pendek) as nama_diagnosa"),
            DB::raw("'ICD-9 CM (Prosedur)' as tipe_nst"),
            DB::raw('COUNT(*) as jumlah'),
        ])->groupBy('icd9.kode', 'icd9.deskripsi_panjang', 'icd9.deskripsi_pendek');

        // UNION kedua query lalu urutkan berdasarkan jumlah terbanyak
        return $icd10->union($icd9)
            ->orderBy('jumlah', 'desc')
            ->get();
    }
}
