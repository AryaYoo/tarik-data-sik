<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MaternalRepository
{
    /**
     * Mendapatkan data base ibu hamil (bumil) yang berkunjung pada periode tertentu.
     * Join ke penilaian_awal_keperawatan_kebidanan untuk data ANC.
     *
     * Definisi wilayah: Dalam Wilayah Surabaya vs Luar Wilayah Surabaya
     * berdasarkan kabupaten.nm_kab LIKE '%Surabaya%' atau '%KOTA SURABAYA%'.
     */

    // ============================================================
    //  HELPER: Base Query Kunjungan Bumil (Rawat Jalan + Ranap)
    // ============================================================

    /**
     * Base query untuk semua bumil yang berkunjung di poli kebidanan/kandungan
     * pada rentang tanggal tertentu.
     */
    private function baseBumilQuery($startDate, $endDate)
    {
        return DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->leftJoin('kabupaten', 'pasien.kd_kab', '=', 'kabupaten.kd_kab')
            ->leftJoin('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->leftJoin('penilaian_awal_keperawatan_kebidanan', 'reg_periksa.no_rawat', '=', 'penilaian_awal_keperawatan_kebidanan.no_rawat')
            ->leftJoin('penilaian_medis_ralan_kandungan', 'reg_periksa.no_rawat', '=', 'penilaian_medis_ralan_kandungan.no_rawat')
            ->whereBetween('reg_periksa.tgl_registrasi', [$startDate, $endDate])
            ->where(function ($q) {
                $q->whereNotNull('penilaian_awal_keperawatan_kebidanan.no_rawat')
                  ->orWhereNotNull('penilaian_medis_ralan_kandungan.no_rawat')
                  ->orWhere('poliklinik.nm_poli', 'LIKE', '%andungan%')
                  ->orWhere('poliklinik.nm_poli', 'LIKE', '%bstetri%')
                  ->orWhere('poliklinik.nm_poli', 'LIKE', '%OBG%');
            })
            ->where('pasien.jk', 'P');
    }

    /**
     * Cek apakah pasien dalam wilayah Surabaya
     */
    private function isInWilayah()
    {
        return DB::raw("IF(LOWER(kabupaten.nm_kab) LIKE '%surabaya%', 'dalam', 'luar')");
    }

    // ============================================================
    //  MAIN METHOD: Menghasilkan seluruh indikator maternal
    // ============================================================
    public function getLaporanMaternal($startDate, $endDate)
    {
        $result = [];

        // ======== SECTION A: MATERNAL ========

        // --- No 1: Jml.Bumil Baru (K1 Akses) ---
        // Bumil yang baru pertama kali berkunjung (status_poli = 'Baru')
        $result['bumil_baru_k1'] = $this->countBumilByWilayah($startDate, $endDate, function ($q) {
            $q->where('reg_periksa.status_poli', 'Baru');
        });

        // --- No 2: Jml.Bumil Baru < 12 mg. (K1 murni) ---
        // K1 murni: kunjungan pertama dengan usia kehamilan < 12 minggu
        $result['bumil_baru_k1_murni'] = $this->countBumilByWilayah($startDate, $endDate, function ($q) {
            $q->where('reg_periksa.status_poli', 'Baru')
              ->whereNotNull('penilaian_awal_keperawatan_kebidanan.usia_kehamilan')
              ->where('penilaian_awal_keperawatan_kebidanan.usia_kehamilan', '<', '12');
        });

        // --- No 3: Jml.Bumil Lama ---
        $result['bumil_lama'] = $this->countBumilByWilayah($startDate, $endDate, function ($q) {
            $q->where('reg_periksa.status_poli', 'Lama');
        });

        // --- No 4: Total Kunjungan ibu hamil ---
        $result['total_kunjungan'] = $this->countBumilByWilayah($startDate, $endDate);

        // --- No 5: Jumlah Bumil di skor Poedji Rochjati ---
        // Tidak ada kolom khusus di SIMRS Khanza untuk skor Poedji Rochjati
        $result['bumil_poedji_rochjati'] = $this->emptyCount();

        // --- No 6: Jml.Bumil mendapatkan buku KIA ---
        // Tidak ada pencatatan distribusi buku KIA di SIMRS
        $result['bumil_buku_kia'] = $this->emptyCount();

        // --- No 7: Jml.Bumil dg. K4 ---
        // Bumil yang sudah kunjungan >= 4 kali di periode
        $result['bumil_k4'] = $this->countBumilK4($startDate, $endDate);

        // --- No 8: Jml. Bumil yang mendapatkan Fe I ---
        // Cek dari pemberian obat yang mengandung zat besi (Fe / tablet tambah darah)
        $result['bumil_fe1'] = $this->countBumilDenganObat($startDate, $endDate, ['FE', 'FERRO', 'FERO', 'FERROUS', 'TABLET TAMBAH DARAH', 'TTD']);

        // --- No 9: Jml. Bumil yang mendapatkan Fe III ---
        // Sama seperti Fe I tapi sudah mendapat >= 3 kali
        $result['bumil_fe3'] = $this->emptyCount();

        // --- No 10: Jml. Bumil yang diperiksa Hb ---
        $result['bumil_periksa_hb'] = $this->countBumilDenganLab($startDate, $endDate, ['HEMOGLOBIN', 'HB', 'HAEMOGLOBIN']);

        // --- No 11: Jml.Bumil dg.Hb 8 - 11 mg/dl ---
        $result['bumil_hb_8_11'] = $this->countBumilDenganLabNilai($startDate, $endDate, ['HEMOGLOBIN', 'HB', 'HAEMOGLOBIN'], 8, 11);

        // --- No 12: Jml.Bumil dg.Hb < 8 mg/dl ---
        $result['bumil_hb_lt8'] = $this->countBumilDenganLabNilai($startDate, $endDate, ['HEMOGLOBIN', 'HB', 'HAEMOGLOBIN'], null, 8);

        // --- No 13: Jml. Bumil yang diukur LiLA ---
        $result['bumil_lila'] = $this->countBumilByWilayah($startDate, $endDate, function ($q) {
            $q->whereNotNull('penilaian_awal_keperawatan_kebidanan.lila')
              ->where('penilaian_awal_keperawatan_kebidanan.lila', '!=', '')
              ->where('penilaian_awal_keperawatan_kebidanan.lila', '!=', '0');
        });

        // --- No 14: Jml. Bumil yang diperiksa Protein Urine ---
        $result['bumil_periksa_protein_urine'] = $this->countBumilDenganLab($startDate, $endDate, ['PROTEIN URINE', 'PROTEIN URIN', 'ALBUMIN URINE', 'ALBUMIN URIN']);

        // --- No 15: Jml. Bumil dengan hasil Protein Urine Positif ---
        $result['bumil_protein_urine_positif'] = $this->countBumilDenganLabKualitatif($startDate, $endDate, ['PROTEIN URINE', 'PROTEIN URIN', 'ALBUMIN URINE', 'ALBUMIN URIN'], ['POSITIF', '+1', '+2', '+3', '+4', '++', '+++', '++++', 'POS']);

        // --- No 16: Jumlah Bumil yang dideteksi Pre Eklampsia ---
        $result['bumil_preeklampsia'] = $this->countBumilDenganDiagnosa($startDate, $endDate, ['O14%', 'O11%']);

        // --- No 17: Jumlah Bumil Pre Eklampsia hasil positif ---
        $result['bumil_preeklampsia_positif'] = $result['bumil_preeklampsia']; // Sama karena diagnosa = terdeteksi

        // --- No 18: Jumlah Bumil positif Pre Eklampsia mendapat terapi Acetosal ---
        $result['bumil_preeklampsia_acetosal'] = $this->countBumilDenganDiagnosaObat($startDate, $endDate, ['O14%', 'O11%'], ['ACETOSAL', 'ASPIRIN', 'ASPILET', 'ASETOSAL']);

        // --- No 19: Jumlah Bumil di USG Dopler ---
        $result['bumil_usg_dopler'] = $this->emptyCount();

        // --- No 20: Jml. Bumil yang diperiksa Gula Darah ---
        $result['bumil_periksa_gula_darah'] = $this->countBumilDenganLab($startDate, $endDate, ['GLUKOSA', 'GULA DARAH', 'GDS', 'GDP', 'GD2PP', 'GLUCOSE']);

        // --- No 21: Jml. Bumil dengan Gula Darah > 140 g/dL ---
        $result['bumil_gula_darah_tinggi'] = $this->countBumilDenganLabNilai($startDate, $endDate, ['GLUKOSA', 'GULA DARAH', 'GDS', 'GDP', 'GD2PP', 'GLUCOSE'], 140, null);

        // --- No 22: Jml. Bumil yang diperiksa Hepatitis B ---
        $result['bumil_periksa_hepb'] = $this->countBumilDenganLab($startDate, $endDate, ['HBSAG', 'HBS AG', 'HEPATITIS B', 'HEP B']);

        // --- No 23: Jml. Bumil dengan Hepatitis B ---
        $result['bumil_hepb_positif'] = $this->countBumilDenganLabKualitatif($startDate, $endDate, ['HBSAG', 'HBS AG', 'HEPATITIS B', 'HEP B'], ['POSITIF', 'REAKTIF', 'POS', 'REACTIVE']);

        // --- No 24: Jml.Bumil dg. Risti ---
        // Risiko Tinggi berdasarkan diagnosa ICD
        $result['bumil_risti'] = $this->countBumilDenganDiagnosa($startDate, $endDate, ['O10%', 'O11%', 'O12%', 'O13%', 'O14%', 'O15%', 'O16%', 'O24%', 'O26%', 'O28%', 'O30%', 'O32%', 'O33%', 'O34%', 'O35%', 'O36%', 'O40%', 'O41%', 'O42%', 'O43%', 'O44%', 'O45%', 'O46%', 'O47%', 'O48%', 'O60%', 'O98%', 'O99%']);

        // --- No 25: Jml.Bumil Risti ditangani ---
        $result['bumil_risti_ditangani'] = $result['bumil_risti'];

        // --- No 26: Jml.Bumil risti dirujuk ---
        $result['bumil_risti_dirujuk'] = $this->countBumilDirujuk($startDate, $endDate, true);

        // --- No 27: Jml. Ibu Hamil Kecil Masa Kehamilan ---
        $result['bumil_kmk'] = $this->countBumilKMK($startDate, $endDate, 'kecil');

        // --- No 28: Jml. Ibu Hamil Besar Masa Kehamilan ---
        $result['bumil_bmk'] = $this->countBumilKMK($startDate, $endDate, 'besar');

        // --- No 29: Kasus maternal ditemukan ---
        $result['kasus_hyperemisis'] = $this->countBumilDenganDiagnosa($startDate, $endDate, ['O21%']);
        $result['kasus_keguguran'] = $this->countBumilDenganDiagnosa($startDate, $endDate, ['O03%', 'O02%', 'O06%']);
        $result['kasus_eklampsia'] = $this->countBumilDenganDiagnosa($startDate, $endDate, ['O14%', 'O15%']);
        $result['kasus_perdarahan_kehamilan'] = $this->countBumilDenganDiagnosa($startDate, $endDate, ['O20%', 'O44%', 'O45%', 'O46%']);
        $result['kasus_perdarahan_persalinan'] = $this->countBumilDenganDiagnosa($startDate, $endDate, ['O67%', 'O72%']);
        $result['kasus_perdarahan_nifas'] = $this->countBumilDenganDiagnosa($startDate, $endDate, ['O72%']);
        $result['kasus_partus_lama'] = $this->countBumilDenganDiagnosa($startDate, $endDate, ['O63%', 'O64%', 'O65%', 'O66%']);
        $result['kasus_infeksi'] = $this->countBumilDenganDiagnosa($startDate, $endDate, ['O85%', 'O86%']);
        $result['kasus_tb'] = $this->countBumilDenganDiagnosa($startDate, $endDate, ['O99.0%', 'A15%', 'A16%']);
        $result['kasus_malaria'] = $this->countBumilDenganDiagnosa($startDate, $endDate, ['O99.1%', 'B50%', 'B51%', 'B52%', 'B53%', 'B54%']);
        $result['kasus_jantung'] = $this->countBumilDenganDiagnosa($startDate, $endDate, ['O99.4%']);
        $result['kasus_kek'] = $this->countBumilKEK($startDate, $endDate);
        $result['kasus_hiv'] = $this->countBumilDenganDiagnosa($startDate, $endDate, ['O98.7%', 'B20%', 'B21%', 'B22%', 'B23%', 'B24%', 'Z21%']);
        $result['kasus_lain'] = $this->emptyCount();

        // --- No 30-34: HIV/AIDS & Sifilis ---
        $result['bumil_ditawarkan_hiv_sifilis'] = $this->emptyCount();
        $result['bumil_dirujuk_vct'] = $this->emptyCount();
        $result['bumil_tes_hiv'] = $this->countBumilDenganLab($startDate, $endDate, ['HIV', 'ANTI HIV', 'ANTI-HIV']);
        $result['bumil_periksa_sifilis'] = $this->countBumilDenganLab($startDate, $endDate, ['VDRL', 'TPHA', 'RPR', 'SIFILIS', 'SYPHILIS']);
        $result['bumil_sifilis'] = $this->countBumilDenganLabKualitatif($startDate, $endDate, ['VDRL', 'TPHA', 'RPR', 'SIFILIS', 'SYPHILIS'], ['POSITIF', 'REAKTIF', 'POS', 'REACTIVE']);

        // --- No 35-39: Persalinan ---
        $result['persalinan_normal'] = $this->countPersalinan($startDate, $endDate, 'normal');
        $result['persalinan_imd'] = $this->emptyCount();
        $result['persalinan_imd_berhasil'] = $this->emptyCount();
        $result['persalinan_dirujuk_rs'] = $this->countBumilDirujuk($startDate, $endDate, false, true);
        $result['persalinan_operasi'] = $this->countPersalinan($startDate, $endDate, 'operasi');

        // --- No 40: Persalinan dengan tindakan ---
        $result['tindakan_drip'] = $this->emptyCount();
        $result['tindakan_vakum'] = $this->countPersalinanTindakan($startDate, $endDate, ['VAKUM', 'VACUUM']);
        $result['tindakan_curatage'] = $this->countPersalinanTindakan($startDate, $endDate, ['KURET', 'CURET', 'CURATAGE', 'KURETASE']);
        $result['tindakan_placenta_manual'] = $this->countPersalinanTindakan($startDate, $endDate, ['PLACENTA MANUAL', 'PLASENTA MANUAL', 'MANUAL PLASENTA']);

        // --- No 41-44: Nifas ---
        $result['nifas_baru'] = $this->countNifas($startDate, $endDate, true);
        $result['total_kunjungan_nifas'] = $this->countNifas($startDate, $endDate, false);
        $result['nifas_dirujuk'] = $this->emptyCount();
        $result['nifas_hpp_ditangani'] = $this->countBumilDenganDiagnosa($startDate, $endDate, ['O72%']);

        // --- No 46-48: Ibu menyusui ---
        $result['nifas_vit_a'] = $this->emptyCount();
        $result['ibu_meneteki_baru'] = $this->emptyCount();
        $result['total_kunj_ibu_meneteki'] = $this->emptyCount();

        // --- No 49-50: Kematian maternal ---
        $result['kematian_maternal'] = $this->countKematianMaternal($startDate, $endDate);
        $result['kematian_perdarahan'] = $this->emptyCount();
        $result['kematian_infeksi_jalan_lahir'] = $this->emptyCount();
        $result['kematian_eklamsi'] = $this->emptyCount();
        $result['kematian_lain'] = $this->emptyCount();

        // --- No 51: Penolong Persalinan Terakhir ---
        $result['penolong_dokter_spog'] = $this->countPersalinanPenolong($startDate, $endDate, 'dokter');
        $result['penolong_bidan'] = $this->countPersalinanPenolong($startDate, $endDate, 'bidan');

        return $result;
    }

    // ============================================================
    //  HELPER METHODS
    // ============================================================

    /**
     * Empty count (0 dalam/luar/total)
     */
    private function emptyCount()
    {
        return ['dalam' => 0, 'luar' => 0, 'total' => 0];
    }

    /**
     * Count bumil by wilayah (dalam/luar Surabaya)
     */
    private function countBumilByWilayah($startDate, $endDate, $additionalConditions = null)
    {
        $query = $this->baseBumilQuery($startDate, $endDate);

        if (is_callable($additionalConditions)) {
            $additionalConditions($query);
        }

        $counts = $query->select([
            DB::raw("SUM(CASE WHEN LOWER(COALESCE(kabupaten.nm_kab,'')) LIKE '%surabaya%' THEN 1 ELSE 0 END) as dalam"),
            DB::raw("SUM(CASE WHEN LOWER(COALESCE(kabupaten.nm_kab,'')) NOT LIKE '%surabaya%' THEN 1 ELSE 0 END) as luar"),
            DB::raw("COUNT(*) as total")
        ])->first();

        return [
            'dalam' => (int)($counts->dalam ?? 0),
            'luar' => (int)($counts->luar ?? 0),
            'total' => (int)($counts->total ?? 0),
        ];
    }

    /**
     * K4: Bumil yang sudah kunjungan >= 4 kali
     */
    private function countBumilK4($startDate, $endDate)
    {
        $subQuery = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->leftJoin('kabupaten', 'pasien.kd_kab', '=', 'kabupaten.kd_kab')
            ->leftJoin('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->leftJoin('penilaian_awal_keperawatan_kebidanan', 'reg_periksa.no_rawat', '=', 'penilaian_awal_keperawatan_kebidanan.no_rawat')
            ->leftJoin('penilaian_medis_ralan_kandungan', 'reg_periksa.no_rawat', '=', 'penilaian_medis_ralan_kandungan.no_rawat')
            ->whereBetween('reg_periksa.tgl_registrasi', [$startDate, $endDate])
            ->where('pasien.jk', 'P')
            ->where(function ($q) {
                $q->whereNotNull('penilaian_awal_keperawatan_kebidanan.no_rawat')
                  ->orWhereNotNull('penilaian_medis_ralan_kandungan.no_rawat')
                  ->orWhere('poliklinik.nm_poli', 'LIKE', '%andungan%')
                  ->orWhere('poliklinik.nm_poli', 'LIKE', '%bstetri%')
                  ->orWhere('poliklinik.nm_poli', 'LIKE', '%OBG%');
            })
            ->groupBy('reg_periksa.no_rkm_medis', 'kabupaten.nm_kab')
            ->havingRaw('COUNT(*) >= 4')
            ->select([
                'reg_periksa.no_rkm_medis',
                DB::raw("IF(LOWER(COALESCE(kabupaten.nm_kab,'')) LIKE '%surabaya%', 'dalam', 'luar') as wilayah")
            ]);

        $result = DB::table(DB::raw("({$subQuery->toSql()}) as sub"))
            ->mergeBindings($subQuery)
            ->select([
                DB::raw("SUM(CASE WHEN wilayah = 'dalam' THEN 1 ELSE 0 END) as dalam"),
                DB::raw("SUM(CASE WHEN wilayah = 'luar' THEN 1 ELSE 0 END) as luar"),
                DB::raw("COUNT(*) as total")
            ])->first();

        return [
            'dalam' => (int)($result->dalam ?? 0),
            'luar' => (int)($result->luar ?? 0),
            'total' => (int)($result->total ?? 0),
        ];
    }

    /**
     * Count bumil yang mendapatkan obat tertentu
     */
    private function countBumilDenganObat($startDate, $endDate, $namaObat)
    {
        $query = $this->baseBumilQuery($startDate, $endDate)
            ->join('detail_pemberian_obat', function ($join) {
                $join->on('reg_periksa.no_rawat', '=', 'detail_pemberian_obat.no_rawat');
            })
            ->join('databarang', 'detail_pemberian_obat.kode_brng', '=', 'databarang.kode_brng');

        $query->where(function ($q) use ($namaObat) {
            foreach ($namaObat as $nama) {
                $q->orWhere('databarang.nama_brng', 'LIKE', '%' . $nama . '%');
            }
        });

        $counts = $query->select([
            DB::raw("COUNT(DISTINCT CASE WHEN LOWER(COALESCE(kabupaten.nm_kab,'')) LIKE '%surabaya%' THEN reg_periksa.no_rkm_medis END) as dalam"),
            DB::raw("COUNT(DISTINCT CASE WHEN LOWER(COALESCE(kabupaten.nm_kab,'')) NOT LIKE '%surabaya%' THEN reg_periksa.no_rkm_medis END) as luar"),
            DB::raw("COUNT(DISTINCT reg_periksa.no_rkm_medis) as total")
        ])->first();

        return [
            'dalam' => (int)($counts->dalam ?? 0),
            'luar' => (int)($counts->luar ?? 0),
            'total' => (int)($counts->total ?? 0),
        ];
    }

    /**
     * Count bumil yang diperiksa lab tertentu
     */
    private function countBumilDenganLab($startDate, $endDate, $namaPemeriksaan)
    {
        $query = $this->baseBumilQuery($startDate, $endDate)
            ->join('periksa_lab', 'reg_periksa.no_rawat', '=', 'periksa_lab.no_rawat')
            ->join('detail_periksa_lab', function ($join) {
                $join->on('periksa_lab.no_rawat', '=', 'detail_periksa_lab.no_rawat')
                     ->on('periksa_lab.kd_jenis_prw', '=', 'detail_periksa_lab.kd_jenis_prw')
                     ->on('periksa_lab.tgl_periksa', '=', 'detail_periksa_lab.tgl_periksa')
                     ->on('periksa_lab.jam', '=', 'detail_periksa_lab.jam');
            })
            ->join('template_laboratorium', 'detail_periksa_lab.id_template', '=', 'template_laboratorium.id_template');

        $query->where(function ($q) use ($namaPemeriksaan) {
            foreach ($namaPemeriksaan as $nama) {
                $q->orWhere('template_laboratorium.Pemeriksaan', 'LIKE', '%' . $nama . '%');
            }
        });

        $counts = $query->select([
            DB::raw("COUNT(DISTINCT CASE WHEN LOWER(COALESCE(kabupaten.nm_kab,'')) LIKE '%surabaya%' THEN reg_periksa.no_rkm_medis END) as dalam"),
            DB::raw("COUNT(DISTINCT CASE WHEN LOWER(COALESCE(kabupaten.nm_kab,'')) NOT LIKE '%surabaya%' THEN reg_periksa.no_rkm_medis END) as luar"),
            DB::raw("COUNT(DISTINCT reg_periksa.no_rkm_medis) as total")
        ])->first();

        return [
            'dalam' => (int)($counts->dalam ?? 0),
            'luar' => (int)($counts->luar ?? 0),
            'total' => (int)($counts->total ?? 0),
        ];
    }

    /**
     * Count bumil dengan hasil lab numerik dalam range
     */
    private function countBumilDenganLabNilai($startDate, $endDate, $namaPemeriksaan, $min = null, $max = null)
    {
        $query = $this->baseBumilQuery($startDate, $endDate)
            ->join('periksa_lab', 'reg_periksa.no_rawat', '=', 'periksa_lab.no_rawat')
            ->join('detail_periksa_lab', function ($join) {
                $join->on('periksa_lab.no_rawat', '=', 'detail_periksa_lab.no_rawat')
                     ->on('periksa_lab.kd_jenis_prw', '=', 'detail_periksa_lab.kd_jenis_prw')
                     ->on('periksa_lab.tgl_periksa', '=', 'detail_periksa_lab.tgl_periksa')
                     ->on('periksa_lab.jam', '=', 'detail_periksa_lab.jam');
            })
            ->join('template_laboratorium', 'detail_periksa_lab.id_template', '=', 'template_laboratorium.id_template');

        $query->where(function ($q) use ($namaPemeriksaan) {
            foreach ($namaPemeriksaan as $nama) {
                $q->orWhere('template_laboratorium.Pemeriksaan', 'LIKE', '%' . $nama . '%');
            }
        });

        // Nilai numerik
        $query->whereRaw("CAST(detail_periksa_lab.nilai AS DECIMAL(10,2)) > 0");

        if ($min !== null && $max !== null) {
            $query->whereRaw("CAST(detail_periksa_lab.nilai AS DECIMAL(10,2)) >= ?", [$min])
                  ->whereRaw("CAST(detail_periksa_lab.nilai AS DECIMAL(10,2)) <= ?", [$max]);
        } elseif ($min !== null) {
            $query->whereRaw("CAST(detail_periksa_lab.nilai AS DECIMAL(10,2)) > ?", [$min]);
        } elseif ($max !== null) {
            $query->whereRaw("CAST(detail_periksa_lab.nilai AS DECIMAL(10,2)) < ?", [$max]);
        }

        $counts = $query->select([
            DB::raw("COUNT(DISTINCT CASE WHEN LOWER(COALESCE(kabupaten.nm_kab,'')) LIKE '%surabaya%' THEN reg_periksa.no_rkm_medis END) as dalam"),
            DB::raw("COUNT(DISTINCT CASE WHEN LOWER(COALESCE(kabupaten.nm_kab,'')) NOT LIKE '%surabaya%' THEN reg_periksa.no_rkm_medis END) as luar"),
            DB::raw("COUNT(DISTINCT reg_periksa.no_rkm_medis) as total")
        ])->first();

        return [
            'dalam' => (int)($counts->dalam ?? 0),
            'luar' => (int)($counts->luar ?? 0),
            'total' => (int)($counts->total ?? 0),
        ];
    }

    /**
     * Count bumil dengan hasil lab kualitatif (positif/reaktif/dll)
     */
    private function countBumilDenganLabKualitatif($startDate, $endDate, $namaPemeriksaan, $nilaiPositif)
    {
        $query = $this->baseBumilQuery($startDate, $endDate)
            ->join('periksa_lab', 'reg_periksa.no_rawat', '=', 'periksa_lab.no_rawat')
            ->join('detail_periksa_lab', function ($join) {
                $join->on('periksa_lab.no_rawat', '=', 'detail_periksa_lab.no_rawat')
                     ->on('periksa_lab.kd_jenis_prw', '=', 'detail_periksa_lab.kd_jenis_prw')
                     ->on('periksa_lab.tgl_periksa', '=', 'detail_periksa_lab.tgl_periksa')
                     ->on('periksa_lab.jam', '=', 'detail_periksa_lab.jam');
            })
            ->join('template_laboratorium', 'detail_periksa_lab.id_template', '=', 'template_laboratorium.id_template');

        $query->where(function ($q) use ($namaPemeriksaan) {
            foreach ($namaPemeriksaan as $nama) {
                $q->orWhere('template_laboratorium.Pemeriksaan', 'LIKE', '%' . $nama . '%');
            }
        });

        $query->where(function ($q) use ($nilaiPositif) {
            foreach ($nilaiPositif as $val) {
                $q->orWhere('detail_periksa_lab.nilai', 'LIKE', '%' . $val . '%');
            }
        });

        $counts = $query->select([
            DB::raw("COUNT(DISTINCT CASE WHEN LOWER(COALESCE(kabupaten.nm_kab,'')) LIKE '%surabaya%' THEN reg_periksa.no_rkm_medis END) as dalam"),
            DB::raw("COUNT(DISTINCT CASE WHEN LOWER(COALESCE(kabupaten.nm_kab,'')) NOT LIKE '%surabaya%' THEN reg_periksa.no_rkm_medis END) as luar"),
            DB::raw("COUNT(DISTINCT reg_periksa.no_rkm_medis) as total")
        ])->first();

        return [
            'dalam' => (int)($counts->dalam ?? 0),
            'luar' => (int)($counts->luar ?? 0),
            'total' => (int)($counts->total ?? 0),
        ];
    }

    /**
     * Count bumil dengan diagnosa ICD tertentu
     */
    private function countBumilDenganDiagnosa($startDate, $endDate, $icdCodes)
    {
        $query = $this->baseBumilQuery($startDate, $endDate)
            ->join('diagnosa_pasien', 'reg_periksa.no_rawat', '=', 'diagnosa_pasien.no_rawat');

        $query->where(function ($q) use ($icdCodes) {
            foreach ($icdCodes as $code) {
                $q->orWhere('diagnosa_pasien.kd_penyakit', 'LIKE', $code);
            }
        });

        $counts = $query->select([
            DB::raw("COUNT(DISTINCT CASE WHEN LOWER(COALESCE(kabupaten.nm_kab,'')) LIKE '%surabaya%' THEN reg_periksa.no_rkm_medis END) as dalam"),
            DB::raw("COUNT(DISTINCT CASE WHEN LOWER(COALESCE(kabupaten.nm_kab,'')) NOT LIKE '%surabaya%' THEN reg_periksa.no_rkm_medis END) as luar"),
            DB::raw("COUNT(DISTINCT reg_periksa.no_rkm_medis) as total")
        ])->first();

        return [
            'dalam' => (int)($counts->dalam ?? 0),
            'luar' => (int)($counts->luar ?? 0),
            'total' => (int)($counts->total ?? 0),
        ];
    }

    /**
     * Count bumil dengan diagnosa tertentu DAN mendapat obat tertentu
     */
    private function countBumilDenganDiagnosaObat($startDate, $endDate, $icdCodes, $namaObat)
    {
        $query = $this->baseBumilQuery($startDate, $endDate)
            ->join('diagnosa_pasien', 'reg_periksa.no_rawat', '=', 'diagnosa_pasien.no_rawat')
            ->join('detail_pemberian_obat', 'reg_periksa.no_rawat', '=', 'detail_pemberian_obat.no_rawat')
            ->join('databarang', 'detail_pemberian_obat.kode_brng', '=', 'databarang.kode_brng');

        $query->where(function ($q) use ($icdCodes) {
            foreach ($icdCodes as $code) {
                $q->orWhere('diagnosa_pasien.kd_penyakit', 'LIKE', $code);
            }
        });

        $query->where(function ($q) use ($namaObat) {
            foreach ($namaObat as $nama) {
                $q->orWhere('databarang.nama_brng', 'LIKE', '%' . $nama . '%');
            }
        });

        $counts = $query->select([
            DB::raw("COUNT(DISTINCT CASE WHEN LOWER(COALESCE(kabupaten.nm_kab,'')) LIKE '%surabaya%' THEN reg_periksa.no_rkm_medis END) as dalam"),
            DB::raw("COUNT(DISTINCT CASE WHEN LOWER(COALESCE(kabupaten.nm_kab,'')) NOT LIKE '%surabaya%' THEN reg_periksa.no_rkm_medis END) as luar"),
            DB::raw("COUNT(DISTINCT reg_periksa.no_rkm_medis) as total")
        ])->first();

        return [
            'dalam' => (int)($counts->dalam ?? 0),
            'luar' => (int)($counts->luar ?? 0),
            'total' => (int)($counts->total ?? 0),
        ];
    }

    /**
     * Count bumil KEK (LiLA < 23.5 cm)
     */
    private function countBumilKEK($startDate, $endDate)
    {
        return $this->countBumilByWilayah($startDate, $endDate, function ($q) {
            $q->whereNotNull('penilaian_awal_keperawatan_kebidanan.lila')
              ->where('penilaian_awal_keperawatan_kebidanan.lila', '!=', '')
              ->whereRaw("CAST(penilaian_awal_keperawatan_kebidanan.lila AS DECIMAL(5,1)) < 23.5");
        });
    }

    /**
     * Count bumil KMK/BMK berdasarkan TFU vs usia kehamilan
     */
    private function countBumilKMK($startDate, $endDate, $type = 'kecil')
    {
        $condition = $type === 'kecil'
            ? "CAST(COALESCE(penilaian_awal_keperawatan_kebidanan.tfu, penilaian_medis_ralan_kandungan.tfu, '0') AS DECIMAL(5,1)) < (CAST(COALESCE(penilaian_awal_keperawatan_kebidanan.usia_kehamilan, '0') AS DECIMAL(5,1)) - 3)"
            : "CAST(COALESCE(penilaian_awal_keperawatan_kebidanan.tfu, penilaian_medis_ralan_kandungan.tfu, '0') AS DECIMAL(5,1)) >= (CAST(COALESCE(penilaian_awal_keperawatan_kebidanan.usia_kehamilan, '0') AS DECIMAL(5,1)) - 3)";

        return $this->countBumilByWilayah($startDate, $endDate, function ($q) use ($condition) {
            $q->whereRaw("COALESCE(penilaian_awal_keperawatan_kebidanan.tfu, penilaian_medis_ralan_kandungan.tfu) IS NOT NULL")
              ->whereRaw("COALESCE(penilaian_awal_keperawatan_kebidanan.tfu, penilaian_medis_ralan_kandungan.tfu) != ''")
              ->whereRaw("COALESCE(penilaian_awal_keperawatan_kebidanan.tfu, penilaian_medis_ralan_kandungan.tfu) != '0'")
              ->whereNotNull('penilaian_awal_keperawatan_kebidanan.usia_kehamilan')
              ->where('penilaian_awal_keperawatan_kebidanan.usia_kehamilan', '!=', '')
              ->whereRaw($condition);
        });
    }

    /**
     * Count bumil dirujuk
     */
    private function countBumilDirujuk($startDate, $endDate, $risti = false, $persalinan = false)
    {
        $query = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->leftJoin('kabupaten', 'pasien.kd_kab', '=', 'kabupaten.kd_kab')
            ->join('rujuk', 'reg_periksa.no_rawat', '=', 'rujuk.no_rawat')
            ->whereBetween('reg_periksa.tgl_registrasi', [$startDate, $endDate])
            ->where('pasien.jk', 'P');

        if ($persalinan) {
            $query->where('rujuk.kat_rujuk', 'Kebidanan');
        }

        $counts = $query->select([
            DB::raw("COUNT(DISTINCT CASE WHEN LOWER(COALESCE(kabupaten.nm_kab,'')) LIKE '%surabaya%' THEN reg_periksa.no_rkm_medis END) as dalam"),
            DB::raw("COUNT(DISTINCT CASE WHEN LOWER(COALESCE(kabupaten.nm_kab,'')) NOT LIKE '%surabaya%' THEN reg_periksa.no_rkm_medis END) as luar"),
            DB::raw("COUNT(DISTINCT reg_periksa.no_rkm_medis) as total")
        ])->first();

        return [
            'dalam' => (int)($counts->dalam ?? 0),
            'luar' => (int)($counts->luar ?? 0),
            'total' => (int)($counts->total ?? 0),
        ];
    }

    /**
     * Count persalinan (normal vs operasi) berdasarkan tabel operasi + paket_operasi
     */
    private function countPersalinan($startDate, $endDate, $type = 'normal')
    {
        if ($type === 'operasi') {
            // Persalinan operasi = paket operasi kategori Kebidanan
            $query = DB::table('operasi')
                ->join('reg_periksa', 'operasi.no_rawat', '=', 'reg_periksa.no_rawat')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->leftJoin('kabupaten', 'pasien.kd_kab', '=', 'kabupaten.kd_kab')
                ->join('paket_operasi', 'operasi.kode_paket', '=', 'paket_operasi.kode_paket')
                ->where('paket_operasi.kategori', 'Kebidanan')
                ->where('pasien.jk', 'P')
                ->whereBetween(DB::raw('DATE(operasi.tgl_operasi)'), [$startDate, $endDate]);

            $counts = $query->select([
                DB::raw("COUNT(DISTINCT CASE WHEN LOWER(COALESCE(kabupaten.nm_kab,'')) LIKE '%surabaya%' THEN reg_periksa.no_rkm_medis END) as dalam"),
                DB::raw("COUNT(DISTINCT CASE WHEN LOWER(COALESCE(kabupaten.nm_kab,'')) NOT LIKE '%surabaya%' THEN reg_periksa.no_rkm_medis END) as luar"),
                DB::raw("COUNT(DISTINCT reg_periksa.no_rkm_medis) as total")
            ])->first();

            return [
                'dalam' => (int)($counts->dalam ?? 0),
                'luar' => (int)($counts->luar ?? 0),
                'total' => (int)($counts->total ?? 0),
            ];
        }

        // Persalinan normal = kamar_inap ranap + diagnosa O80 (persalinan spontan normal)
        $query = DB::table('kamar_inap')
            ->join('reg_periksa', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->leftJoin('kabupaten', 'pasien.kd_kab', '=', 'kabupaten.kd_kab')
            ->join('diagnosa_pasien', 'reg_periksa.no_rawat', '=', 'diagnosa_pasien.no_rawat')
            ->where('pasien.jk', 'P')
            ->where('diagnosa_pasien.kd_penyakit', 'LIKE', 'O80%')
            ->whereBetween('kamar_inap.tgl_masuk', [$startDate, $endDate]);

        $counts = $query->select([
            DB::raw("COUNT(DISTINCT CASE WHEN LOWER(COALESCE(kabupaten.nm_kab,'')) LIKE '%surabaya%' THEN reg_periksa.no_rkm_medis END) as dalam"),
            DB::raw("COUNT(DISTINCT CASE WHEN LOWER(COALESCE(kabupaten.nm_kab,'')) NOT LIKE '%surabaya%' THEN reg_periksa.no_rkm_medis END) as luar"),
            DB::raw("COUNT(DISTINCT reg_periksa.no_rkm_medis) as total")
        ])->first();

        return [
            'dalam' => (int)($counts->dalam ?? 0),
            'luar' => (int)($counts->luar ?? 0),
            'total' => (int)($counts->total ?? 0),
        ];
    }

    /**
     * Count persalinan dengan tindakan tertentu (vakum, kuret, dll)
     */
    private function countPersalinanTindakan($startDate, $endDate, $namaOperasi)
    {
        $query = DB::table('operasi')
            ->join('reg_periksa', 'operasi.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->leftJoin('kabupaten', 'pasien.kd_kab', '=', 'kabupaten.kd_kab')
            ->join('paket_operasi', 'operasi.kode_paket', '=', 'paket_operasi.kode_paket')
            ->where('pasien.jk', 'P')
            ->whereBetween(DB::raw('DATE(operasi.tgl_operasi)'), [$startDate, $endDate]);

        $query->where(function ($q) use ($namaOperasi) {
            foreach ($namaOperasi as $nama) {
                $q->orWhere('paket_operasi.nm_perawatan', 'LIKE', '%' . $nama . '%');
            }
        });

        $counts = $query->select([
            DB::raw("COUNT(DISTINCT CASE WHEN LOWER(COALESCE(kabupaten.nm_kab,'')) LIKE '%surabaya%' THEN reg_periksa.no_rkm_medis END) as dalam"),
            DB::raw("COUNT(DISTINCT CASE WHEN LOWER(COALESCE(kabupaten.nm_kab,'')) NOT LIKE '%surabaya%' THEN reg_periksa.no_rkm_medis END) as luar"),
            DB::raw("COUNT(DISTINCT reg_periksa.no_rkm_medis) as total")
        ])->first();

        return [
            'dalam' => (int)($counts->dalam ?? 0),
            'luar' => (int)($counts->luar ?? 0),
            'total' => (int)($counts->total ?? 0),
        ];
    }

    /**
     * Count nifas (post-partum)
     * Nifas = pasien rawat inap dengan diagnosa nifas (O85-O92)
     */
    private function countNifas($startDate, $endDate, $baru = false)
    {
        $query = DB::table('kamar_inap')
            ->join('reg_periksa', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->leftJoin('kabupaten', 'pasien.kd_kab', '=', 'kabupaten.kd_kab')
            ->join('diagnosa_pasien', 'reg_periksa.no_rawat', '=', 'diagnosa_pasien.no_rawat')
            ->where('pasien.jk', 'P')
            ->whereBetween('kamar_inap.tgl_masuk', [$startDate, $endDate])
            ->where(function ($q) {
                // Diagnosa persalinan & nifas
                for ($i = 80; $i <= 92; $i++) {
                    $q->orWhere('diagnosa_pasien.kd_penyakit', 'LIKE', 'O' . $i . '%');
                }
            });

        $counts = $query->select([
            DB::raw("COUNT(DISTINCT CASE WHEN LOWER(COALESCE(kabupaten.nm_kab,'')) LIKE '%surabaya%' THEN reg_periksa.no_rkm_medis END) as dalam"),
            DB::raw("COUNT(DISTINCT CASE WHEN LOWER(COALESCE(kabupaten.nm_kab,'')) NOT LIKE '%surabaya%' THEN reg_periksa.no_rkm_medis END) as luar"),
            DB::raw("COUNT(DISTINCT reg_periksa.no_rkm_medis) as total")
        ])->first();

        return [
            'dalam' => (int)($counts->dalam ?? 0),
            'luar' => (int)($counts->luar ?? 0),
            'total' => (int)($counts->total ?? 0),
        ];
    }

    /**
     * Count kematian maternal
     */
    private function countKematianMaternal($startDate, $endDate)
    {
        $query = DB::table('kamar_inap')
            ->join('reg_periksa', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->leftJoin('kabupaten', 'pasien.kd_kab', '=', 'kabupaten.kd_kab')
            ->where('pasien.jk', 'P')
            ->where('kamar_inap.stts_pulang', 'Meninggal')
            ->whereBetween('kamar_inap.tgl_keluar', [$startDate, $endDate])
            ->where(function ($q) {
                // Diagnosa obstetri
                $q->whereExists(function ($sub) {
                    $sub->select(DB::raw(1))
                        ->from('diagnosa_pasien')
                        ->whereRaw('diagnosa_pasien.no_rawat = reg_periksa.no_rawat')
                        ->where('diagnosa_pasien.kd_penyakit', 'LIKE', 'O%');
                });
            });

        $counts = $query->select([
            DB::raw("COUNT(DISTINCT CASE WHEN LOWER(COALESCE(kabupaten.nm_kab,'')) LIKE '%surabaya%' THEN reg_periksa.no_rkm_medis END) as dalam"),
            DB::raw("COUNT(DISTINCT CASE WHEN LOWER(COALESCE(kabupaten.nm_kab,'')) NOT LIKE '%surabaya%' THEN reg_periksa.no_rkm_medis END) as luar"),
            DB::raw("COUNT(DISTINCT reg_periksa.no_rkm_medis) as total")
        ])->first();

        return [
            'dalam' => (int)($counts->dalam ?? 0),
            'luar' => (int)($counts->luar ?? 0),
            'total' => (int)($counts->total ?? 0),
        ];
    }

    /**
     * Count penolong persalinan terakhir (Dokter SpOG vs Bidan)
     */
    private function countPersalinanPenolong($startDate, $endDate, $type = 'dokter')
    {
        $query = DB::table('operasi')
            ->join('reg_periksa', 'operasi.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->leftJoin('kabupaten', 'pasien.kd_kab', '=', 'kabupaten.kd_kab')
            ->join('paket_operasi', 'operasi.kode_paket', '=', 'paket_operasi.kode_paket')
            ->where('paket_operasi.kategori', 'Kebidanan')
            ->where('pasien.jk', 'P')
            ->whereBetween(DB::raw('DATE(operasi.tgl_operasi)'), [$startDate, $endDate]);

        if ($type === 'dokter') {
            $query->join('dokter', 'operasi.operator1', '=', 'dokter.kd_dokter')
                  ->where('dokter.kd_sps', 'LIKE', '%SpOG%');
        }

        $counts = $query->select([
            DB::raw("COUNT(DISTINCT CASE WHEN LOWER(COALESCE(kabupaten.nm_kab,'')) LIKE '%surabaya%' THEN reg_periksa.no_rkm_medis END) as dalam"),
            DB::raw("COUNT(DISTINCT CASE WHEN LOWER(COALESCE(kabupaten.nm_kab,'')) NOT LIKE '%surabaya%' THEN reg_periksa.no_rkm_medis END) as luar"),
            DB::raw("COUNT(DISTINCT reg_periksa.no_rkm_medis) as total")
        ])->first();

        return [
            'dalam' => (int)($counts->dalam ?? 0),
            'luar' => (int)($counts->luar ?? 0),
            'total' => (int)($counts->total ?? 0),
        ];
    }
}

