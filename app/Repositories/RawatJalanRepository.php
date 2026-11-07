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
            ->distinct()
            ->orderBy('pasien.nm_pasien', 'asc');
    }

    public function getPelayananIbuHamil($startDate, $endDate, $kd_poli = null)
    {
        $query = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->leftJoin('kelurahan', 'pasien.kd_kel', '=', 'kelurahan.kd_kel')
            ->leftJoin('kecamatan', 'pasien.kd_kec', '=', 'kecamatan.kd_kec')
            ->leftJoin('kabupaten', 'pasien.kd_kab', '=', 'kabupaten.kd_kab')
            ->leftJoin('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->leftJoin('penilaian_awal_keperawatan_kebidanan', 'reg_periksa.no_rawat', '=', 'penilaian_awal_keperawatan_kebidanan.no_rawat')
            ->leftJoin('penilaian_medis_ralan_kandungan', 'reg_periksa.no_rawat', '=', 'penilaian_medis_ralan_kandungan.no_rawat')
            ->where('reg_periksa.status_lanjut', 'Ralan')
            ->whereBetween('reg_periksa.tgl_registrasi', [$startDate, $endDate])
            ->where('pasien.jk', 'P') // Wajib Perempuan
            ->where(function ($q) {
                // Usia reproduktif (bukan balita/anak-anak)
                $q->where(function ($sq) {
                    $sq->where('reg_periksa.sttsumur', 'Th')
                       ->where('reg_periksa.umurdaftar', '>=', 10);
                })->orWhereRaw("TIMESTAMPDIFF(YEAR, pasien.tgl_lahir, reg_periksa.tgl_registrasi) >= 10");
            })
            ->where(function ($q) {
                // Wajib memiliki catatan pemeriksaan antenatal / kehamilan:
                // 1. Ada asesmen awal keperawatan kebidanan (ANC)
                $q->whereNotNull('penilaian_awal_keperawatan_kebidanan.no_rawat')
                // 2. Atau ada TFU / diagnosa kehamilan pada asesmen medis kandungan
                  ->orWhere(function ($sub) {
                      $sub->whereNotNull('penilaian_medis_ralan_kandungan.no_rawat')
                          ->where(function ($ss) {
                              $ss->where('penilaian_medis_ralan_kandungan.tfu', '!=', '')
                                ->orWhere('penilaian_medis_ralan_kandungan.diagnosis', 'LIKE', '%G%P%A%')
                                ->orWhere('penilaian_medis_ralan_kandungan.diagnosis', 'LIKE', '%hamil%')
                                ->orWhere('penilaian_medis_ralan_kandungan.diagnosis', 'LIKE', '%gravid%');
                          });
                  })
                // 3. Atau ada diagnosa kehamilan ICD-10 (O00-O99)
                  ->orWhereExists(function ($sub) {
                      $sub->select(DB::raw(1))
                          ->from('diagnosa_pasien')
                          ->whereRaw('diagnosa_pasien.no_rawat = reg_periksa.no_rawat')
                          ->where('diagnosa_pasien.kd_penyakit', 'LIKE', 'O%');
                  });
            });

        if ($kd_poli) {
            $query->where('reg_periksa.kd_poli', $kd_poli);
        }

        return $query->select([
                'reg_periksa.no_rawat',
                'reg_periksa.no_rkm_medis',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'pasien.nm_pasien',
                'pasien.no_ktp',
                'pasien.namakeluarga',
                'pasien.keluarga',
                'reg_periksa.p_jawab',
                'reg_periksa.hubunganpj',
                'pasien.alamat',
                'kelurahan.nm_kel',
                'kecamatan.nm_kec',
                'kabupaten.nm_kab',
                'reg_periksa.umurdaftar',
                'reg_periksa.sttsumur',
                'pasien.tgl_lahir',
                'penilaian_awal_keperawatan_kebidanan.hpht',
                'penilaian_awal_keperawatan_kebidanan.usia_kehamilan',
                'penilaian_awal_keperawatan_kebidanan.tfu as tfu_kebidanan',
                'penilaian_medis_ralan_kandungan.tfu as tfu_dokter',
                'penilaian_awal_keperawatan_kebidanan.lila',
                'penilaian_awal_keperawatan_kebidanan.g',
                'penilaian_awal_keperawatan_kebidanan.p',
                'penilaian_awal_keperawatan_kebidanan.a',
                'penilaian_medis_ralan_kandungan.diagnosis as diagnosa_dokter',
                'poliklinik.nm_poli'
            ])
            ->orderBy('reg_periksa.tgl_registrasi', 'asc')
            ->orderBy('reg_periksa.jam_reg', 'asc');
    }
}
