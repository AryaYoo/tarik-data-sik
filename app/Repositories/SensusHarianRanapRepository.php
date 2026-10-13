<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SensusHarianRanapRepository
{
    /**
     * Mengambil daftar bangsal aktif untuk filter
     */
    public function getBangsal()
    {
        return DB::table('bangsal')
            ->select('kd_bangsal', 'nm_bangsal')
            ->where('status', '1')
            ->orderBy('nm_bangsal', 'asc')
            ->get();
    }

    /**
     * Mengambil data sensus harian rawat inap per bulan.
     *
     * Sensus Harian Rawat Inap adalah laporan statistik bulanan yang mencatat
     * pergerakan pasien rawat inap (masuk, keluar, pindah) beserta hari perawatan
     * berdasarkan kelas kamar dan jenis pelayanan (Umum/BPJS).
     *
     * @param string $bulan Format: Y-m (e.g., "2026-10")
     * @param string|null $kd_bangsal Filter bangsal tertentu
     * @return array
     */
    public function getSensusHarianRanap($bulan, $kd_bangsal = null)
    {
        $startDate = Carbon::parse($bulan . '-01')->startOfMonth()->format('Y-m-d');
        $endDate   = Carbon::parse($bulan . '-01')->endOfMonth()->format('Y-m-d');
        $prevMonth = Carbon::parse($bulan . '-01')->subMonth()->endOfMonth()->format('Y-m-d');

        // --- 1. Pasien Awal Bulan ---
        // Pasien yang masuk sebelum bulan ini dan belum keluar pada akhir bulan sebelumnya
        $pasienAwalBulanQuery = DB::table('kamar_inap')
            ->join('reg_periksa', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
            ->leftJoin('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->where('kamar_inap.tgl_masuk', '<=', $prevMonth)
            ->where(function ($q) use ($startDate) {
                $q->where('kamar_inap.tgl_keluar', '>=', $startDate)
                  ->orWhere('kamar_inap.tgl_keluar', '0000-00-00')
                  ->orWhereNull('kamar_inap.tgl_keluar');
            })
            ->where('kamar_inap.stts_pulang', '!=', 'Pindah Kamar');

        if ($kd_bangsal) {
            $pasienAwalBulanQuery->where('kamar.kd_bangsal', $kd_bangsal);
        }

        // --- 2. Pasien Masuk (Bulan Ini) ---
        // Pasien yang masuk kamar inap dalam rentang bulan ini
        $pasienMasukQuery = DB::table('kamar_inap')
            ->join('reg_periksa', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
            ->leftJoin('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->leftJoin('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->whereBetween('kamar_inap.tgl_masuk', [$startDate, $endDate])
            ->where('kamar_inap.stts_pulang', '!=', 'Pindah Kamar');

        if ($kd_bangsal) {
            $pasienMasukQuery->where('kamar.kd_bangsal', $kd_bangsal);
        }

        // --- 3. Pasien Pindahan (Masuk dari bangsal/kamar lain) ---
        // Pasien yang record sebelumnya di kamar lain dengan status 'Pindah Kamar'
        $pasienPindahanQuery = DB::table('kamar_inap')
            ->join('reg_periksa', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
            ->leftJoin('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->whereBetween('kamar_inap.tgl_masuk', [$startDate, $endDate])
            ->whereExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('kamar_inap as ki_prev')
                    ->whereColumn('ki_prev.no_rawat', 'kamar_inap.no_rawat')
                    ->where('ki_prev.stts_pulang', 'Pindah Kamar')
                    ->whereColumn('ki_prev.tgl_keluar', 'kamar_inap.tgl_masuk');
            });

        if ($kd_bangsal) {
            $pasienPindahanQuery->where('kamar.kd_bangsal', $kd_bangsal);
        }

        // --- 4. Pasien Dipindahkan (Keluar dari bangsal ini ke bangsal lain) ---
        $pasienDipindahkanQuery = DB::table('kamar_inap')
            ->join('reg_periksa', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
            ->leftJoin('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->whereBetween('kamar_inap.tgl_keluar', [$startDate, $endDate])
            ->where('kamar_inap.stts_pulang', 'Pindah Kamar');

        if ($kd_bangsal) {
            $pasienDipindahkanQuery->where('kamar.kd_bangsal', $kd_bangsal);
        }

        // --- 5. Pasien Keluar (Hidup / Mati) ---
        $pasienKeluarQuery = DB::table('kamar_inap')
            ->join('reg_periksa', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
            ->leftJoin('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->whereBetween('kamar_inap.tgl_keluar', [$startDate, $endDate])
            ->whereNotIn('kamar_inap.stts_pulang', ['Pindah Kamar', '-']);

        if ($kd_bangsal) {
            $pasienKeluarQuery->where('kamar.kd_bangsal', $kd_bangsal);
        }

        // Kategorisasi jenis pelayanan: BPJS vs Umum
        // BPJS diidentifikasi berdasarkan kode penjab yang mengandung 'BPJ'
        $isBpjs = function ($kd_pj) {
            return stripos($kd_pj, 'BPJ') !== false;
        };

        // Execute queries dan kategorikan
        $awalBulanData = $pasienAwalBulanQuery->select([
            'kamar_inap.*',
            'reg_periksa.kd_pj',
            'reg_periksa.kd_poli',
            'reg_periksa.no_rkm_medis',
            'pasien.nm_pasien',
            'pasien.jk',
            'kamar.kelas',
            'kamar.kd_bangsal',
            'penjab.png_jawab',
        ])->get();

        $masukData = $pasienMasukQuery->select([
            'kamar_inap.*',
            'reg_periksa.kd_pj',
            'reg_periksa.kd_poli',
            'reg_periksa.no_rkm_medis',
            'pasien.nm_pasien',
            'pasien.jk',
            'kamar.kelas',
            'kamar.kd_bangsal',
            'penjab.png_jawab',
            'poliklinik.nm_poli',
        ])->get();

        $pindahanData = $pasienPindahanQuery->select([
            'kamar_inap.*',
            'reg_periksa.kd_pj',
            'reg_periksa.no_rkm_medis',
            'pasien.nm_pasien',
            'pasien.jk',
            'kamar.kelas',
            'kamar.kd_bangsal',
            'penjab.png_jawab',
        ])->get();

        $dipindahkanData = $pasienDipindahkanQuery->select([
            'kamar_inap.*',
            'reg_periksa.kd_pj',
            'reg_periksa.no_rkm_medis',
            'pasien.nm_pasien',
            'pasien.jk',
            'kamar.kelas',
            'kamar.kd_bangsal',
            'penjab.png_jawab',
        ])->get();

        $keluarData = $pasienKeluarQuery->select([
            'kamar_inap.*',
            'reg_periksa.kd_pj',
            'reg_periksa.no_rkm_medis',
            'pasien.nm_pasien',
            'pasien.jk',
            'kamar.kelas',
            'kamar.kd_bangsal',
            'penjab.png_jawab',
        ])->get();

        // --- Hitung Statistik per Jenis Pelayanan ---
        $result = [
            'umum' => $this->hitungStatistik($awalBulanData, $masukData, $pindahanData, $dipindahkanData, $keluarData, false, $startDate, $endDate),
            'bpjs' => $this->hitungStatistik($awalBulanData, $masukData, $pindahanData, $dipindahkanData, $keluarData, true, $startDate, $endDate),
        ];

        // Total gabungan
        $result['total'] = $this->gabungStatistik($result['umum'], $result['bpjs']);

        // --- Alokasi TT Awal Bulan ---
        $ttQuery = DB::table('kamar')
            ->join('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->where('kamar.statusdata', '1')
            ->where('bangsal.status', '1');

        if ($kd_bangsal) {
            $ttQuery->where('kamar.kd_bangsal', $kd_bangsal);
        }

        $result['alokasi_tt'] = $ttQuery->count();

        // Detail per kelas kamar
        $ttDetailQuery = DB::table('kamar')
            ->join('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->select('kamar.kelas', DB::raw('COUNT(*) as jumlah'))
            ->where('kamar.statusdata', '1')
            ->where('bangsal.status', '1')
            ->groupBy('kamar.kelas');

        if ($kd_bangsal) {
            $ttDetailQuery->where('kamar.kd_bangsal', $kd_bangsal);
        }

        $result['alokasi_tt_detail'] = $ttDetailQuery->get()->keyBy('kelas');

        // Metadata
        $result['bulan'] = $bulan;
        $result['start_date'] = $startDate;
        $result['end_date'] = $endDate;

        return $result;
    }

    /**
     * Menghitung statistik sensus berdasarkan jenis pelayanan
     */
    private function hitungStatistik($awalBulanData, $masukData, $pindahanData, $dipindahkanData, $keluarData, $isBpjs, $startDate, $endDate)
    {
        $filterByPj = function ($collection) use ($isBpjs) {
            return $collection->filter(function ($item) use ($isBpjs) {
                $hasBpj = stripos($item->kd_pj ?? '', 'BPJ') !== false;
                return $isBpjs ? $hasBpj : !$hasBpj;
            });
        };

        $awal     = $filterByPj($awalBulanData);
        $masuk    = $filterByPj($masukData);
        $pindahan = $filterByPj($pindahanData);
        $dipindah = $filterByPj($dipindahkanData);
        $keluar   = $filterByPj($keluarData);

        // Pasien Keluar Hidup vs Mati
        $keluarHidup = $keluar->filter(function ($item) {
            return !in_array($item->stts_pulang, ['Meninggal']);
        });

        $keluarMati = $keluar->filter(function ($item) {
            return $item->stts_pulang === 'Meninggal';
        });

        // Pasien Mati >= 48 jam (lama rawat >= 2 hari)
        $keluarMati48JamPria = $keluarMati->filter(function ($item) {
            return $item->jk === 'L' && ($item->lama ?? 0) >= 2;
        });

        $keluarMati48JamWanita = $keluarMati->filter(function ($item) {
            return $item->jk === 'P' && ($item->lama ?? 0) >= 2;
        });

        // Keluar Mati < 48 jam
        $keluarMatiKurang48JamPria = $keluarMati->filter(function ($item) {
            return $item->jk === 'L' && ($item->lama ?? 0) < 2;
        });

        $keluarMatiKurang48JamWanita = $keluarMati->filter(function ($item) {
            return $item->jk === 'P' && ($item->lama ?? 0) < 2;
        });

        // Pasien Masuk berdasarkan asal (dari poliklinik)
        $masukIGD = $masuk->filter(function ($item) {
            return stripos($item->nm_poli ?? '', 'IGD') !== false;
        });
        $masukObgyn = $masuk->filter(function ($item) {
            $poli = strtolower($item->nm_poli ?? '');
            return stripos($poli, 'obg') !== false || stripos($poli, 'kandung') !== false;
        });
        $masukAnak = $masuk->filter(function ($item) {
            $poli = strtolower($item->nm_poli ?? '');
            return stripos($poli, 'anak') !== false;
        });
        $masukBedah = $masuk->filter(function ($item) {
            $poli = strtolower($item->nm_poli ?? '');
            return stripos($poli, 'bedah') !== false;
        });
        $masukLainnya = $masuk->filter(function ($item) {
            $poli = strtolower($item->nm_poli ?? '');
            return stripos($poli, 'igd') === false
                && stripos($poli, 'obg') === false
                && stripos($poli, 'kandung') === false
                && stripos($poli, 'anak') === false
                && stripos($poli, 'bedah') === false;
        });

        // Hari Perawatan per kelas kamar
        $hpVVIP = $this->hitungHariPerawatan($keluar, 'Kelas VVIP');
        $hpVIP  = $this->hitungHariPerawatan($keluar, 'Kelas VIP');
        $hpK1   = $this->hitungHariPerawatan($keluar, 'Kelas 1');
        $hpK2   = $this->hitungHariPerawatan($keluar, 'Kelas 2');
        $hpK3   = $this->hitungHariPerawatan($keluar, 'Kelas 3');
        $hpKhusus = $this->hitungHariPerawatan($keluar, 'Kelas Utama');

        // Juga hitung hari perawatan dari pasien yang masih dirawat (belum keluar)
        // dari semua data kamar_inap yang masih aktif
        $hpVVIPAktif = $this->hitungHariPerawatanAktif($awal, 'Kelas VVIP', $startDate, $endDate)
                     + $this->hitungHariPerawatanAktif($masuk->filter(fn($i) => in_array($i->stts_pulang, ['-'])), 'Kelas VVIP', $startDate, $endDate);

        return [
            'pasien_awal_bulan'    => $awal->count(),
            'pasien_masuk'         => $masuk->count(),
            'pasien_masuk_igd'     => $masukIGD->count(),
            'pasien_masuk_obgyn'   => $masukObgyn->count(),
            'pasien_masuk_anak'    => $masukAnak->count(),
            'pasien_masuk_bedah'   => $masukBedah->count(),
            'pasien_masuk_lainnya' => $masukLainnya->count(),
            'pasien_pindahan'      => $pindahan->count(),
            'pasien_dipindahkan'   => $dipindah->count(),
            'pasien_keluar_hidup'  => $keluarHidup->count(),
            'pasien_keluar_mati'   => $keluarMati->count(),
            'mati_48_pria'         => $keluarMati48JamPria->count(),
            'mati_48_wanita'       => $keluarMati48JamWanita->count(),
            'mati_kurang_48_pria'         => $keluarMatiKurang48JamPria->count(),
            'mati_kurang_48_wanita'       => $keluarMatiKurang48JamWanita->count(),
            'hp_vvip'              => $hpVVIP,
            'hp_vip'               => $hpVIP,
            'hp_kelas_1'           => $hpK1,
            'hp_kelas_2'           => $hpK2,
            'hp_kelas_3'           => $hpK3,
            'hp_khusus'            => $hpKhusus,
            'total_hari_perawatan' => $hpVVIP + $hpVIP + $hpK1 + $hpK2 + $hpK3 + $hpKhusus,
        ];
    }

    /**
     * Menghitung total hari perawatan berdasarkan kelas kamar
     * Dari pasien yang sudah keluar
     */
    private function hitungHariPerawatan($collection, $kelas)
    {
        return $collection->filter(function ($item) use ($kelas) {
            return ($item->kelas ?? '') === $kelas;
        })->sum(function ($item) {
            return max(1, $item->lama ?? 0);
        });
    }

    /**
     * Menghitung hari perawatan pasien yang masih aktif
     */
    private function hitungHariPerawatanAktif($collection, $kelas, $startDate, $endDate)
    {
        $end = Carbon::parse($endDate);
        return $collection->filter(function ($item) use ($kelas) {
            return ($item->kelas ?? '') === $kelas;
        })->sum(function ($item) use ($end, $startDate) {
            $masuk = Carbon::parse($item->tgl_masuk);
            $start = Carbon::parse($startDate);
            $fromDate = $masuk->greaterThan($start) ? $masuk : $start;
            return max(1, $fromDate->diffInDays($end) + 1);
        });
    }

    /**
     * Menggabungkan statistik Umum + BPJS
     */
    private function gabungStatistik($umum, $bpjs)
    {
        $result = [];
        foreach ($umum as $key => $value) {
            $result[$key] = $value + ($bpjs[$key] ?? 0);
        }
        return $result;
    }

    /**
     * Mengambil detail data per hari untuk tabel detail sensus
     */
    public function getDetailSensusPerHari($bulan, $kd_bangsal = null)
    {
        $startDate = Carbon::parse($bulan . '-01')->startOfMonth()->format('Y-m-d');
        $endDate   = Carbon::parse($bulan . '-01')->endOfMonth()->format('Y-m-d');

        $query = DB::table('kamar_inap')
            ->join('reg_periksa', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
            ->leftJoin('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->leftJoin('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->leftJoin('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->where(function ($q) use ($startDate, $endDate) {
                // Pasien yang masuk atau keluar dalam bulan ini, atau masih dirawat
                $q->whereBetween('kamar_inap.tgl_masuk', [$startDate, $endDate])
                  ->orWhereBetween('kamar_inap.tgl_keluar', [$startDate, $endDate])
                  ->orWhere(function ($sub) use ($startDate) {
                      $sub->where('kamar_inap.tgl_masuk', '<', $startDate)
                          ->where(function ($s) use ($startDate) {
                              $s->where('kamar_inap.tgl_keluar', '>=', $startDate)
                                ->orWhere('kamar_inap.tgl_keluar', '0000-00-00')
                                ->orWhereNull('kamar_inap.tgl_keluar');
                          });
                  });
            });

        if ($kd_bangsal) {
            $query->where('kamar.kd_bangsal', $kd_bangsal);
        }

        return $query->select([
            'kamar_inap.no_rawat',
            'kamar_inap.kd_kamar',
            'kamar_inap.tgl_masuk',
            'kamar_inap.jam_masuk',
            'kamar_inap.tgl_keluar',
            'kamar_inap.jam_keluar',
            'kamar_inap.lama',
            'kamar_inap.stts_pulang',
            'reg_periksa.no_rkm_medis',
            'reg_periksa.kd_pj',
            'reg_periksa.kd_poli',
            'pasien.nm_pasien',
            'pasien.jk',
            'kamar.kelas',
            'kamar.kd_bangsal',
            'bangsal.nm_bangsal',
            'penjab.png_jawab',
            'poliklinik.nm_poli',
        ])
        ->orderBy('kamar_inap.tgl_masuk', 'asc')
        ->orderBy('kamar_inap.jam_masuk', 'asc')
        ->get();
    }
}
