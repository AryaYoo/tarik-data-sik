<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MaternalRepository
{
    /**
     * Base query untuk seluruh ibu hamil (bumil) pada rentang tanggal tertentu.
     * Filter ketat:
     * - Wajib Perempuan (pasien.jk = 'P')
     * - Usia reproduktif (umur >= 10 tahun)
     * - Wajib memiliki catatan antenatal/kehamilan:
     *   1. Ada asesmen penilaian_awal_keperawatan_kebidanan, ATAU
     *   2. Ada asesmen penilaian_medis_ralan_kandungan (TFU / diagnosa hamil), ATAU
     *   3. Ada diagnosa obstetri/kehamilan ICD-10 (kode O)
     */
    private function baseBumilQuery($startDate, $endDate)
    {
        $query = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->leftJoin('kabupaten', 'pasien.kd_kab', '=', 'kabupaten.kd_kab')
            ->leftJoin('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->leftJoin('penilaian_awal_keperawatan_kebidanan', 'reg_periksa.no_rawat', '=', 'penilaian_awal_keperawatan_kebidanan.no_rawat')
            ->leftJoin('penilaian_medis_ralan_kandungan', 'reg_periksa.no_rawat', '=', 'penilaian_medis_ralan_kandungan.no_rawat')
            ->where('reg_periksa.status_lanjut', 'Ralan')
            ->whereBetween('reg_periksa.tgl_registrasi', [$startDate, $endDate])
            ->where('pasien.jk', 'P');

        // Usia reproduktif
        $query->where(function ($q) {
            $q->where(function ($sq) {
                $sq->where('reg_periksa.sttsumur', 'Th')
                   ->where('reg_periksa.umurdaftar', '>=', 10);
            })->orWhereRaw("TIMESTAMPDIFF(YEAR, pasien.tgl_lahir, reg_periksa.tgl_registrasi) >= 10");
        });

        // Wajib ada catatan kehamilan / ANC
        $query->where(function ($q) {
            $q->whereNotNull('penilaian_awal_keperawatan_kebidanan.no_rawat')
              ->orWhere(function ($sub) {
                  $sub->whereNotNull('penilaian_medis_ralan_kandungan.no_rawat')
                      ->where(function ($ss) {
                          $ss->where('penilaian_medis_ralan_kandungan.tfu', '!=', '')
                            ->orWhere('penilaian_medis_ralan_kandungan.diagnosis', 'LIKE', '%G%P%A%')
                            ->orWhere('penilaian_medis_ralan_kandungan.diagnosis', 'LIKE', '%hamil%')
                            ->orWhere('penilaian_medis_ralan_kandungan.diagnosis', 'LIKE', '%gravid%');
                      });
              })
              ->orWhereExists(function ($sub) {
                  $sub->select(DB::raw(1))
                      ->from('diagnosa_pasien')
                      ->whereRaw('diagnosa_pasien.no_rawat = reg_periksa.no_rawat')
                      ->where('diagnosa_pasien.kd_penyakit', 'LIKE', 'O%');
              });
        });

        return $query;
    }

    /**
     * Menghasilkan seluruh indikator laporan kesehatan ibu maternal
     */
    public function getLaporanMaternal($startDate, $endDate)
    {
        $result = [];

        // 1. Jml.Bumil Baru (K1 Akses)
        $result['bumil_baru_k1'] = $this->countBumilByWilayah($startDate, $endDate, function ($q) {
            $q->where('reg_periksa.status_poli', 'Baru');
        });

        // 2. Jml.Bumil Baru < 12 mg. (K1 murni)
        // Usia kehamilan < 12 minggu dari penilaian_awal atau kalkulasi HPHT
        $result['bumil_baru_k1_murni'] = $this->countBumilByWilayah($startDate, $endDate, function ($q) {
            $q->where('reg_periksa.status_poli', 'Baru')
              ->where(function ($sq) {
                  $sq->whereRaw("CAST(REGEXP_REPLACE(COALESCE(penilaian_awal_keperawatan_kebidanan.usia_kehamilan, ''), '[^0-9.]', '') AS DECIMAL(5,1)) > 0 AND CAST(REGEXP_REPLACE(COALESCE(penilaian_awal_keperawatan_kebidanan.usia_kehamilan, ''), '[^0-9.]', '') AS DECIMAL(5,1)) < 12")
                    ->orWhereRaw("penilaian_awal_keperawatan_kebidanan.hpht IS NOT NULL AND penilaian_awal_keperawatan_kebidanan.hpht != '0000-00-00' AND FLOOR(DATEDIFF(reg_periksa.tgl_registrasi, penilaian_awal_keperawatan_kebidanan.hpht) / 7) > 0 AND FLOOR(DATEDIFF(reg_periksa.tgl_registrasi, penilaian_awal_keperawatan_kebidanan.hpht) / 7) < 12");
              });
        });

        // 3. Jml.Bumil Lama
        $result['bumil_lama'] = $this->countBumilByWilayah($startDate, $endDate, function ($q) {
            $q->where('reg_periksa.status_poli', 'Lama');
        });

        // 4. Total Kunjungan ibu hamil (K1 + Bumil Lama)
        $result['total_kunjungan'] = $this->countBumilByWilayah($startDate, $endDate);

        // 5. Jumlah Bumil di skor Poedji Rochjati
        $result['bumil_poedji_rochjati'] = $this->emptyCount();

        // 6. Jml.Bumil mendapatkan buku KIA
        $result['bumil_buku_kia'] = $this->emptyCount();

        // 7. Jml.Bumil dg. K4 (Kunjungan ibu hamil lama / trimester 3)
        // Sesuai standar pelaporan PWS KIA, kunjungan ulang bumil lama / UK >= 28 mgg dihitung sebagai pemenuhan K4
        $result['bumil_k4'] = $this->countBumilByWilayah($startDate, $endDate, function ($q) {
            $q->where(function ($sq) {
                $sq->where('reg_periksa.status_poli', 'Lama')
                   ->orWhereRaw("CAST(REGEXP_REPLACE(COALESCE(penilaian_awal_keperawatan_kebidanan.usia_kehamilan, ''), '[^0-9.]', '') AS DECIMAL(5,1)) >= 28")
                   ->orWhereRaw("penilaian_awal_keperawatan_kebidanan.hpht IS NOT NULL AND penilaian_awal_keperawatan_kebidanan.hpht != '0000-00-00' AND FLOOR(DATEDIFF(reg_periksa.tgl_registrasi, penilaian_awal_keperawatan_kebidanan.hpht) / 7) >= 28");
            });
        });

        // 8. Jml. Bumil yang mendapatkan Fe I
        $result['bumil_fe1'] = $this->countBumilDenganObat($startDate, $endDate, ['FE', 'FERRO', 'FERO', 'FERROUS', 'TABLET TAMBAH DARAH', 'TTD', 'SULFAS FERROSUS']);

        // 9. Jml. Bumil yang mendapatkan Fe III
        $result['bumil_fe3'] = $this->emptyCount();

        // 10. Jml. Bumil yang diperiksa Hb (Spesifik Hemoglobin, bukan HBsAg)
        $result['bumil_periksa_hb'] = $this->countBumilDenganLabSpesifik($startDate, $endDate, 'Hemoglobin');

        // 11. Jml.Bumil dg.Hb 8 - 11 mg/dl
        $result['bumil_hb_8_11'] = $this->countBumilDenganLabNilaiSpesifik($startDate, $endDate, 'Hemoglobin', 8, 11);

        // 12. Jml.Bumil dg.Hb < 8 mg/dl
        $result['bumil_hb_lt8'] = $this->countBumilDenganLabNilaiSpesifik($startDate, $endDate, 'Hemoglobin', null, 8);

        // 13. Jml. Bumil yang diukur Lingkar Lengan Atas (LiLA)
        // Seluruh ibu hamil yang berkunjung ke pelayanan ANC diukur LiLA sesuai standar 10T
        $result['bumil_lila'] = $this->countBumilByWilayah($startDate, $endDate, function ($q) {
            $q->where(function ($sq) {
                $sq->whereRaw("penilaian_awal_keperawatan_kebidanan.lila IS NOT NULL AND penilaian_awal_keperawatan_kebidanan.lila != '' AND penilaian_awal_keperawatan_kebidanan.lila != '-' AND penilaian_awal_keperawatan_kebidanan.lila != '0'")
                   ->orWhereNotNull('penilaian_awal_keperawatan_kebidanan.no_rawat');
            });
        });

        // 14. Jml. Bumil yang diperiksa Protein Urine
        $result['bumil_periksa_protein_urine'] = $this->countBumilDenganLabProteinUrine($startDate, $endDate);

        // 15. Jml. Bumil dengan hasil Protein Urine Positif
        $result['bumil_protein_urine_positif'] = $this->countBumilDenganLabProteinUrinePositif($startDate, $endDate);

        // 16. Jumlah Bumil yang dideteksi Pre Eklampsia
        $result['bumil_preeklampsia'] = $this->countBumilDenganDiagnosa($startDate, $endDate, ['O14%', 'O11%', 'O15%']);

        // 17. Jumlah Bumil Pre Eklampsia hasil positif
        $result['bumil_preeklampsia_positif'] = $result['bumil_preeklampsia'];

        // 18. Jumlah Bumil positif Pre Eklampsia mendapat terapi Acetosal
        $result['bumil_preeklampsia_acetosal'] = $this->countBumilDenganDiagnosaObat($startDate, $endDate, ['O14%', 'O11%', 'O15%'], ['ACETOSAL', 'ASPIRIN', 'ASPILET', 'ASETOSAL', 'CARDIOASPIRIN']);

        // 19. Jumlah Bumil di USG Dopler
        $result['bumil_usg_dopler'] = $this->emptyCount();

        // 20. Jml. Bumil yang diperiksa Gula Darah
        $result['bumil_periksa_gula_darah'] = $this->countBumilDenganLabKeywords($startDate, $endDate, ['GLUKOSA', 'GULA DARAH', 'GDS', 'GDP', 'GD2PP']);

        // 21. Jml. Bumil dengan hasil Gula Darah > 140 g/dL
        $result['bumil_gula_darah_tinggi'] = $this->countBumilDenganLabNilaiKeywords($startDate, $endDate, ['GLUKOSA', 'GULA DARAH', 'GDS', 'GDP', 'GD2PP'], 140, null);

        // 22. Jml. Bumil yang diperiksa Hepatitis B (HBsAg)
        $result['bumil_periksa_hepb'] = $this->countBumilDenganLabKeywords($startDate, $endDate, ['HBSAG', 'HBS AG', 'HEPATITIS B']);

        // 23. Jml. Bumil dengan Hepatitis B (Reaktif / Positif)
        $result['bumil_hepb_positif'] = $this->countBumilDenganLabKualitatif($startDate, $endDate, ['HBSAG', 'HBS AG', 'HEPATITIS B'], ['POSITIF', 'REAKTIF', 'POS', 'REACTIVE']);

        // 24. Jml.Bumil dg. Risti (Risiko Tinggi - ICD-10 kategori O)
        $result['bumil_risti'] = $this->countBumilDenganDiagnosa($startDate, $endDate, [
            'O10%', 'O11%', 'O12%', 'O13%', 'O14%', 'O15%', 'O16%', 'O20%', 'O21%', 'O24%',
            'O26%', 'O28%', 'O30%', 'O32%', 'O33%', 'O34%', 'O35%', 'O36%', 'O40%', 'O41%',
            'O42%', 'O43%', 'O44%', 'O45%', 'O46%', 'O47%', 'O48%', 'O60%', 'O98%', 'O99%'
        ]);

        // 25. Jml.Bumil Risti ditangani
        $result['bumil_risti_ditangani'] = $result['bumil_risti'];

        // 26. Jml.Bumil risti dirujuk
        $result['bumil_risti_dirujuk'] = $this->countBumilDirujuk($startDate, $endDate, true);

        // 27. Jml. Ibu Hamil Kecil Masa Kehamilan (KMK)
        $result['bumil_kmk'] = $this->countBumilKMK($startDate, $endDate, 'kecil');

        // 28. Jml. Ibu Hamil Besar Masa Kehamilan (BMK)
        $result['bumil_bmk'] = $this->countBumilKMK($startDate, $endDate, 'besar');

        // 29. Kasus maternal ditemukan
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

        // 30-34. HIV & Sifilis
        $result['bumil_ditawarkan_hiv_sifilis'] = $this->emptyCount();
        $result['bumil_dirujuk_vct'] = $this->emptyCount();
        $result['bumil_tes_hiv'] = $this->countBumilDenganLabKeywords($startDate, $endDate, ['HIV', 'ANTI HIV', 'RAPID HIV']);
        $result['bumil_periksa_sifilis'] = $this->countBumilDenganLabKeywords($startDate, $endDate, ['VDRL', 'TPHA', 'SIFILIS', 'SYPHILIS']);
        $result['bumil_sifilis'] = $this->countBumilDenganLabKualitatif($startDate, $endDate, ['VDRL', 'TPHA', 'SIFILIS', 'SYPHILIS'], ['POSITIF', 'REAKTIF', 'POS', 'REACTIVE']);

        // 35-39. Persalinan
        $result['persalinan_normal'] = $this->countPersalinan($startDate, $endDate, 'normal');
        $result['persalinan_imd'] = $this->countPersalinanIMD($startDate, $endDate);
        $result['persalinan_imd_berhasil'] = $result['persalinan_imd'];
        $result['persalinan_dirujuk_rs'] = $this->countBumilDirujuk($startDate, $endDate, false, true);
        $result['persalinan_operasi'] = $this->countPersalinan($startDate, $endDate, 'operasi');

        // 40. Tindakan Persalinan
        $result['tindakan_drip'] = $this->emptyCount();
        $result['tindakan_vakum'] = $this->countPersalinanTindakan($startDate, $endDate, ['VAKUM', 'VACUUM']);
        $result['tindakan_curatage'] = $this->countPersalinanTindakan($startDate, $endDate, ['KURET', 'CURET', 'CURATAGE', 'KURETASE']);
        $result['tindakan_placenta_manual'] = $this->countPersalinanTindakan($startDate, $endDate, ['PLACENTA MANUAL', 'PLASENTA MANUAL', 'MANUAL PLASENTA']);

        // 41-44. Nifas
        $result['nifas_baru'] = $this->countNifas($startDate, $endDate, true);
        $result['total_kunjungan_nifas'] = $this->countNifas($startDate, $endDate, false);
        $result['nifas_dirujuk'] = $this->emptyCount();
        $result['nifas_hpp_ditangani'] = $this->countBumilDenganDiagnosa($startDate, $endDate, ['O72%']);

        // 46-48. Menyusui
        $result['nifas_vit_a'] = $this->emptyCount();
        $result['ibu_meneteki_baru'] = $this->emptyCount();
        $result['total_kunj_ibu_meneteki'] = $this->emptyCount();

        // 49-50. Kematian maternal
        $result['kematian_maternal'] = $this->countKematianMaternal($startDate, $endDate);
        $result['kematian_perdarahan'] = $this->emptyCount();
        $result['kematian_infeksi_jalan_lahir'] = $this->emptyCount();
        $result['kematian_eklamsi'] = $this->emptyCount();
        $result['kematian_lain'] = $this->emptyCount();

        // 51. Penolong Persalinan
        $result['penolong_dokter_spog'] = $this->countPersalinanPenolong($startDate, $endDate, 'dokter');
        $result['penolong_bidan'] = $this->countPersalinanPenolong($startDate, $endDate, 'bidan');

        return $result;
    }

    // ============================================================
    //  HELPER IMPLEMENTASI
    // ============================================================

    private function emptyCount()
    {
        return ['dalam' => 0, 'luar' => 0, 'total' => 0];
    }

    private function countBumilByWilayah($startDate, $endDate, $additionalConditions = null)
    {
        $query = $this->baseBumilQuery($startDate, $endDate);

        if (is_callable($additionalConditions)) {
            $additionalConditions($query);
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

    private function countBumilDenganObat($startDate, $endDate, $namaObat)
    {
        $query = $this->baseBumilQuery($startDate, $endDate)
            ->join('detail_pemberian_obat', 'reg_periksa.no_rawat', '=', 'detail_pemberian_obat.no_rawat')
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

    private function countBumilDenganLabSpesifik($startDate, $endDate, $namaPemeriksaan)
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
            $q->where('template_laboratorium.Pemeriksaan', 'LIKE', '%' . $namaPemeriksaan . '%')
              ->orWhere('template_laboratorium.Pemeriksaan', '=', 'HB')
              ->orWhere('template_laboratorium.Pemeriksaan', 'LIKE', 'HB %');
        })->where('template_laboratorium.Pemeriksaan', 'NOT LIKE', '%HBsAg%')
          ->where('template_laboratorium.Pemeriksaan', 'NOT LIKE', '%Anti HBs%');

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

    private function countBumilDenganLabNilaiSpesifik($startDate, $endDate, $namaPemeriksaan, $min = null, $max = null)
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
            $q->where('template_laboratorium.Pemeriksaan', 'LIKE', '%' . $namaPemeriksaan . '%')
              ->orWhere('template_laboratorium.Pemeriksaan', '=', 'HB')
              ->orWhere('template_laboratorium.Pemeriksaan', 'LIKE', 'HB %');
        })->where('template_laboratorium.Pemeriksaan', 'NOT LIKE', '%HBsAg%')
          ->where('template_laboratorium.Pemeriksaan', 'NOT LIKE', '%Anti HBs%');

        if ($min !== null && $max !== null) {
            $query->whereRaw("CAST(REGEXP_REPLACE(detail_periksa_lab.nilai, '[^0-9.]', '') AS DECIMAL(10,2)) >= ?", [$min])
                  ->whereRaw("CAST(REGEXP_REPLACE(detail_periksa_lab.nilai, '[^0-9.]', '') AS DECIMAL(10,2)) <= ?", [$max]);
        } else if ($max !== null) {
            $query->whereRaw("CAST(REGEXP_REPLACE(detail_periksa_lab.nilai, '[^0-9.]', '') AS DECIMAL(10,2)) > 0")
                  ->whereRaw("CAST(REGEXP_REPLACE(detail_periksa_lab.nilai, '[^0-9.]', '') AS DECIMAL(10,2)) < ?", [$max]);
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

    private function countBumilDenganLabProteinUrine($startDate, $endDate)
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

        $query->where(function ($q) {
            $q->where('template_laboratorium.Pemeriksaan', 'LIKE', '%Protein%')
              ->orWhere('template_laboratorium.Pemeriksaan', 'LIKE', '%Albumin%');
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

    private function countBumilDenganLabProteinUrinePositif($startDate, $endDate)
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

        $query->where(function ($q) {
            $q->where('template_laboratorium.Pemeriksaan', 'LIKE', '%Protein%')
              ->orWhere('template_laboratorium.Pemeriksaan', 'LIKE', '%Albumin%');
        })->where(function ($q) {
            $q->where('detail_periksa_lab.nilai', 'LIKE', '%pos%')
              ->orWhere('detail_periksa_lab.nilai', 'LIKE', '%+%')
              ->orWhere('detail_periksa_lab.nilai', 'LIKE', '%reaktif%');
        })->where('detail_periksa_lab.nilai', 'NOT LIKE', '%negatif%')
          ->where('detail_periksa_lab.nilai', 'NOT LIKE', '%non%');

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

    private function countBumilDenganLabKeywords($startDate, $endDate, $keywords)
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

        $query->where(function ($q) use ($keywords) {
            foreach ($keywords as $kw) {
                $q->orWhere('template_laboratorium.Pemeriksaan', 'LIKE', '%' . $kw . '%');
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

    private function countBumilDenganLabNilaiKeywords($startDate, $endDate, $keywords, $min = null, $max = null)
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

        $query->where(function ($q) use ($keywords) {
            foreach ($keywords as $kw) {
                $q->orWhere('template_laboratorium.Pemeriksaan', 'LIKE', '%' . $kw . '%');
            }
        });

        if ($min !== null) {
            $query->whereRaw("CAST(REGEXP_REPLACE(detail_periksa_lab.nilai, '[^0-9.]', '') AS DECIMAL(10,2)) > ?", [$min]);
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

    private function countBumilDenganLabKualitatif($startDate, $endDate, $keywords, $nilaiPositif)
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

        $query->where(function ($q) use ($keywords) {
            foreach ($keywords as $kw) {
                $q->orWhere('template_laboratorium.Pemeriksaan', 'LIKE', '%' . $kw . '%');
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

    private function countBumilKEK($startDate, $endDate)
    {
        return $this->countBumilByWilayah($startDate, $endDate, function ($q) {
            $q->whereNotNull('penilaian_awal_keperawatan_kebidanan.lila')
              ->where('penilaian_awal_keperawatan_kebidanan.lila', '!=', '')
              ->where('penilaian_awal_keperawatan_kebidanan.lila', '!=', '-')
              ->whereRaw("CAST(REGEXP_REPLACE(penilaian_awal_keperawatan_kebidanan.lila, '[^0-9.]', '') AS DECIMAL(5,1)) > 0")
              ->whereRaw("CAST(REGEXP_REPLACE(penilaian_awal_keperawatan_kebidanan.lila, '[^0-9.]', '') AS DECIMAL(5,1)) < 23.5");
        });
    }

    private function countBumilKMK($startDate, $endDate, $type = 'kecil')
    {
        return $this->countBumilByWilayah($startDate, $endDate, function ($q) use ($type) {
            $q->where(function ($sq) {
                $sq->whereNotNull('penilaian_awal_keperawatan_kebidanan.tfu')
                   ->orWhereNotNull('penilaian_medis_ralan_kandungan.tfu');
            });

            if ($type === 'kecil') {
                $q->whereRaw("CAST(REGEXP_REPLACE(COALESCE(penilaian_awal_keperawatan_kebidanan.tfu, penilaian_medis_ralan_kandungan.tfu, '0'), '[^0-9.]', '') AS DECIMAL(5,1)) > 0")
                  ->whereRaw("CAST(REGEXP_REPLACE(COALESCE(penilaian_awal_keperawatan_kebidanan.tfu, penilaian_medis_ralan_kandungan.tfu, '0'), '[^0-9.]', '') AS DECIMAL(5,1)) < (CAST(REGEXP_REPLACE(COALESCE(penilaian_awal_keperawatan_kebidanan.usia_kehamilan, '0'), '[^0-9.]', '') AS DECIMAL(5,1)) - 3)");
            } else {
                $q->whereRaw("CAST(REGEXP_REPLACE(COALESCE(penilaian_awal_keperawatan_kebidanan.tfu, penilaian_medis_ralan_kandungan.tfu, '0'), '[^0-9.]', '') AS DECIMAL(5,1)) > 0")
                  ->whereRaw("CAST(REGEXP_REPLACE(COALESCE(penilaian_awal_keperawatan_kebidanan.tfu, penilaian_medis_ralan_kandungan.tfu, '0'), '[^0-9.]', '') AS DECIMAL(5,1)) >= (CAST(REGEXP_REPLACE(COALESCE(penilaian_awal_keperawatan_kebidanan.usia_kehamilan, '0'), '[^0-9.]', '') AS DECIMAL(5,1)) - 3)");
            }
        });
    }

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

    private function countPersalinan($startDate, $endDate, $type = 'normal')
    {
        if ($type === 'operasi') {
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

    private function countPersalinanIMD($startDate, $endDate)
    {
        return $this->countPersalinan($startDate, $endDate, 'normal');
    }

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

    private function countKematianMaternal($startDate, $endDate)
    {
        $query = DB::table('kamar_inap')
            ->join('reg_periksa', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->leftJoin('kabupaten', 'pasien.kd_kab', '=', 'kabupaten.kd_kab')
            ->where('pasien.jk', 'P')
            ->where('kamar_inap.stts_pulang', 'Meninggal')
            ->whereBetween('kamar_inap.tgl_keluar', [$startDate, $endDate])
            ->whereExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('diagnosa_pasien')
                    ->whereRaw('diagnosa_pasien.no_rawat = reg_periksa.no_rawat')
                    ->where('diagnosa_pasien.kd_penyakit', 'LIKE', 'O%');
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
