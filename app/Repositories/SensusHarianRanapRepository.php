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
        $hpVVIP   = $this->hitungHariPerawatan($keluar, 'VVIP');
        $hpVIP    = $this->hitungHariPerawatan($keluar, 'VIP');
        $hpK1     = $this->hitungHariPerawatan($keluar, '1');
        $hpK2     = $this->hitungHariPerawatan($keluar, '2');
        $hpK3     = $this->hitungHariPerawatan($keluar, '3');
        $hpKhusus = $this->hitungHariPerawatan($keluar, 'KHUSUS');

        // Juga hitung hari perawatan dari pasien yang masih dirawat (belum keluar)
        // dari semua data kamar_inap yang masih aktif
        $hpVVIPAktif = $this->hitungHariPerawatanAktif($awal, 'VVIP', $startDate, $endDate)
                     + $this->hitungHariPerawatanAktif($masuk->filter(fn($i) => in_array($i->stts_pulang, ['-'])), 'VVIP', $startDate, $endDate);

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
     * Helper mencocokkan tipe kelas kamar
     */
    private function matchKelas($kelasStr, $tipe)
    {
        $k = strtoupper(trim($kelasStr ?? ''));
        if ($tipe === 'VVIP') {
            return strpos($k, 'VVIP') !== false;
        } elseif ($tipe === 'VIP') {
            return strpos($k, 'VIP') !== false && strpos($k, 'VVIP') === false;
        } elseif ($tipe === '1') {
            return strpos($k, '1') !== false || $k === 'KELAS I';
        } elseif ($tipe === '2') {
            return strpos($k, '2') !== false || $k === 'KELAS II';
        } elseif ($tipe === '3') {
            return strpos($k, '3') !== false || $k === 'KELAS III';
        } elseif ($tipe === 'KHUSUS') {
            return strpos($k, 'VIP') === false &&
                   strpos($k, '1') === false &&
                   strpos($k, '2') === false &&
                   strpos($k, '3') === false &&
                   $k !== 'KELAS I' &&
                   $k !== 'KELAS II' &&
                   $k !== 'KELAS III';
        }
        return false;
    }

    /**
     * Menghitung total hari perawatan berdasarkan kelas kamar
     * Dari pasien yang sudah keluar
     */
    private function hitungHariPerawatan($collection, $tipe)
    {
        return $collection->filter(function ($item) use ($tipe) {
            return $this->matchKelas($item->kelas ?? '', $tipe);
        })->sum(function ($item) {
            return max(1, $item->lama ?? 0);
        });
    }

    /**
     * Menghitung hari perawatan pasien yang masih aktif
     */
    private function hitungHariPerawatanAktif($collection, $tipe, $startDate, $endDate)
    {
        $end = Carbon::parse($endDate);
        return $collection->filter(function ($item) use ($tipe) {
            return $this->matchKelas($item->kelas ?? '', $tipe);
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

    /**
     * Mengambil data Sensus Harian Rawat Inap 2 (Rincian per Pasien)
     *
     * Menghasilkan data granular per pasien (per no_rawat) dengan kolom:
     * - Jenis Pelayanan (Umum / BPJS)
     * - Pasien Awal Bulan (Ya / -)
     * - Pasien Masuk (IGD / Obgyn / Anak / Bedah / Lainnya / -)
     * - Pasien Pindahan (Ya / -)
     * - Pasien Dipindahkan (Ya / -)
     * - Pasien Keluar Hidup (Ya / Tidak / -)
     * - Pasien Pria Keluar Mati >= 48 Jam (Ya / -)
     * - Pasien Wanita Keluar Mati >= 48 Jam (Ya / -)
     * - Hari Perawatan VVIP, VIP, Kelas 1, Kelas 2, Kelas 3, Khusus, Total HP
     * - Alokasi TT Awal Bulan (Kapasitas TT bangsal)
     *
     * @param string $bulan Format: Y-m (e.g. "2026-10")
     * @param string|null $kd_bangsal Filter bangsal
     * @param string|null $jenis_pelayanan Filter 'Umum' atau 'BPJS'
     * @return array
     */
    public function getSensusHarianRanap2($bulan, $kd_bangsal = null, $jenis_pelayanan = null)
    {
        $startDate = Carbon::parse($bulan . '-01')->startOfMonth()->format('Y-m-d');
        $endDate   = Carbon::parse($bulan . '-01')->endOfMonth()->format('Y-m-d');

        // 1. Data Alokasi Tempat Tidur (TT) Aktif Awal Bulan
        $ttQuery = DB::table('kamar')
            ->join('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->where('kamar.statusdata', '1')
            ->where('bangsal.status', '1');

        if ($kd_bangsal) {
            $ttQuery->where('kamar.kd_bangsal', $kd_bangsal);
        }

        $alokasiTTTotal = $ttQuery->count();

        // Alokasi TT per kelas
        $ttPerKelasQuery = DB::table('kamar')
            ->join('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->select('kamar.kelas', DB::raw('COUNT(*) as total'))
            ->where('kamar.statusdata', '1')
            ->where('bangsal.status', '1');

        if ($kd_bangsal) {
            $ttPerKelasQuery->where('kamar.kd_bangsal', $kd_bangsal);
        }

        $ttPerKelas = $ttPerKelasQuery->groupBy('kamar.kelas')->pluck('total', 'kelas')->toArray();

        // Alokasi TT per bangsal
        $ttPerBangsal = DB::table('kamar')
            ->where('statusdata', '1')
            ->groupBy('kd_bangsal')
            ->pluck(DB::raw('COUNT(*)'), 'kd_bangsal')
            ->toArray();

        // 2. Query No Rawat yang aktif pada bulan ini
        $activeNoRawatQuery = DB::table('kamar_inap')
            ->join('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
            ->where('kamar_inap.tgl_masuk', '<=', $endDate)
            ->where(function ($q) use ($startDate) {
                $q->where('kamar_inap.tgl_keluar', '>=', $startDate)
                  ->orWhere('kamar_inap.tgl_keluar', '0000-00-00')
                  ->orWhereNull('kamar_inap.tgl_keluar')
                  ->orWhere('kamar_inap.stts_pulang', '-');
            });

        if ($kd_bangsal) {
            $activeNoRawatQuery->where('kamar.kd_bangsal', $kd_bangsal);
        }

        $activeNoRawats = $activeNoRawatQuery->distinct()->pluck('kamar_inap.no_rawat');

        if ($activeNoRawats->isEmpty()) {
            return [
                'patients'         => [],
                'summary'          => $this->initSummary(),
                'alokasi_tt_total' => $alokasiTTTotal,
                'tt_per_kelas'     => $ttPerKelas,
                'bulan'            => $bulan,
                'start_date'       => $startDate,
                'end_date'         => $endDate,
                'kd_bangsal'       => $kd_bangsal,
                'jenis_pelayanan'  => $jenis_pelayanan,
            ];
        }

        // 3. Query seluruh riwayat kamar_inap untuk no_rawat yang aktif
        $rawStays = DB::table('kamar_inap')
            ->join('reg_periksa', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
            ->leftJoin('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->leftJoin('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->leftJoin('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->whereIn('kamar_inap.no_rawat', $activeNoRawats)
            ->select([
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
                'reg_periksa.tgl_registrasi',
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
            ->get()
            ->groupBy('no_rawat');

        $patients = [];
        $summary  = $this->initSummary();

        $startCarbon = Carbon::parse($startDate);
        $endCarbon   = Carbon::parse($endDate);

        foreach ($rawStays as $noRawat => $stayList) {
            $firstStay = $stayList->first();
            $lastStay  = $stayList->last();

            $kdPj   = $firstStay->kd_pj ?? '';
            $isBpjs = stripos($kdPj, 'BPJ') !== false;
            $jenisPelayananStr = $isBpjs ? 'BPJS' : 'Umum';

            // Filter jenis_pelayanan jika dipilih pengguna
            if ($jenis_pelayanan && strcasecmp($jenis_pelayanan, $jenisPelayananStr) !== 0) {
                continue;
            }

            // A. Pasien Awal Bulan
            $isAwalBulan = ($firstStay->tgl_masuk < $startDate);
            $pasienAwalBulan = $isAwalBulan ? 'Ya' : '-';

            // B. Pasien Masuk (IGD / Obgyn / Anak / Bedah / Lainnya)
            $isMasukBulanIni = ($firstStay->tgl_masuk >= $startDate && $firstStay->tgl_masuk <= $endDate);
            $pasienMasukStr  = '-';
            if ($isMasukBulanIni) {
                $poli = strtolower($firstStay->nm_poli ?? '');
                if (stripos($poli, 'igd') !== false) {
                    $pasienMasukStr = 'IGD';
                    $summary['masuk_igd']++;
                } elseif (stripos($poli, 'obg') !== false || stripos($poli, 'kandung') !== false || stripos($poli, 'kebidanan') !== false) {
                    $pasienMasukStr = 'Obgyn';
                    $summary['masuk_obgyn']++;
                } elseif (stripos($poli, 'anak') !== false) {
                    $pasienMasukStr = 'Anak';
                    $summary['masuk_anak']++;
                } elseif (stripos($poli, 'bedah') !== false) {
                    $pasienMasukStr = 'Bedah';
                    $summary['masuk_bedah']++;
                } else {
                    $pasienMasukStr = !empty($firstStay->nm_poli) ? $firstStay->nm_poli : 'Lainnya';
                    $summary['masuk_lainnya']++;
                }
                $summary['pasien_masuk']++;
            }

            // C. Pasien Pindahan (Pindah kamar masuk dalam bulan ini)
            $isPindahan = false;
            for ($i = 1; $i < count($stayList); $i++) {
                if ($stayList[$i]->tgl_masuk >= $startDate && $stayList[$i]->tgl_masuk <= $endDate) {
                    $isPindahan = true;
                    break;
                }
            }
            $pasienPindahanStr = $isPindahan ? 'Ya' : '-';

            // D. Pasien Dipindahkan (Pindah kamar keluar dalam bulan ini)
            $isDipindahkan = false;
            foreach ($stayList as $stay) {
                if ($stay->stts_pulang === 'Pindah Kamar' && $stay->tgl_keluar >= $startDate && $stay->tgl_keluar <= $endDate) {
                    $isDipindahkan = true;
                    break;
                }
            }
            $pasienDipindahkanStr = $isDipindahkan ? 'Ya' : '-';

            // E. Status Pulang & Keluar Hidup/Mati
            $hasDischargedInMonth = (
                !empty($lastStay->tgl_keluar) &&
                $lastStay->tgl_keluar !== '0000-00-00' &&
                $lastStay->tgl_keluar >= $startDate &&
                $lastStay->tgl_keluar <= $endDate &&
                !in_array($lastStay->stts_pulang, ['Pindah Kamar', '-', ''])
            );

            $pasienKeluarHidupStr = '-';
            $mati48PriaStr        = '-';
            $mati48WanitaStr      = '-';

            if ($hasDischargedInMonth) {
                if ($lastStay->stts_pulang !== 'Meninggal') {
                    $pasienKeluarHidupStr = 'Ya';
                    $summary['pasien_keluar_hidup']++;
                } else {
                    $pasienKeluarHidupStr = 'Tidak';
                    $summary['pasien_keluar_mati']++;

                    // Hitung total lama rawat dari awal masuk s.d. meninggal
                    $losTotal = Carbon::parse($firstStay->tgl_masuk)->diffInDays(Carbon::parse($lastStay->tgl_keluar));
                    if ($firstStay->jk === 'L' && $losTotal >= 2) {
                        $mati48PriaStr = 'Ya';
                        $summary['mati_48_pria']++;
                    } elseif ($firstStay->jk === 'P' && $losTotal >= 2) {
                        $mati48WanitaStr = 'Ya';
                        $summary['mati_48_wanita']++;
                    }
                }
            }

            // F. Hari Perawatan per Kelas Kamar
            $hpVvip   = 0;
            $hpVip    = 0;
            $hpKelas1 = 0;
            $hpKelas2 = 0;
            $hpKelas3 = 0;
            $hpKhusus = 0;

            foreach ($stayList as $stay) {
                $masukStay  = Carbon::parse($stay->tgl_masuk);
                $hasOut     = (!empty($stay->tgl_keluar) && $stay->tgl_keluar !== '0000-00-00');
                $keluarStay = $hasOut ? Carbon::parse($stay->tgl_keluar) : $endCarbon;

                $effStart = $masukStay->gt($startCarbon) ? $masukStay : $startCarbon;
                $effEnd   = $keluarStay->lt($endCarbon) ? $keluarStay : $endCarbon;

                if ($effEnd->lt($effStart)) {
                    $days = 0;
                } else {
                    $diffDays = $effStart->diffInDays($effEnd);
                    if (!$hasOut || $keluarStay->gt($endCarbon)) {
                        $days = $diffDays + 1;
                    } else {
                        if ($diffDays == 0) {
                            $days = max(1, (int)($stay->lama ?? 1));
                        } else {
                            $days = $diffDays;
                        }
                    }
                }

                $kelas = strtoupper(trim($stay->kelas ?? ''));
                if (strpos($kelas, 'VVIP') !== false) {
                    $hpVvip += $days;
                } elseif (strpos($kelas, 'VIP') !== false) {
                    $hpVip += $days;
                } elseif (strpos($kelas, '1') !== false || $kelas === 'KELAS I') {
                    $hpKelas1 += $days;
                } elseif (strpos($kelas, '2') !== false || $kelas === 'KELAS II') {
                    $hpKelas2 += $days;
                } elseif (strpos($kelas, '3') !== false || $kelas === 'KELAS III') {
                    $hpKelas3 += $days;
                } else {
                    $hpKhusus += $days;
                }
            }

            $totalHp = $hpVvip + $hpVip + $hpKelas1 + $hpKelas2 + $hpKelas3 + $hpKhusus;

            // G. Alokasi TT Awal Bulan (Kapasitas bangsal pasien)
            $bangsalKd = $lastStay->kd_bangsal ?? ($firstStay->kd_bangsal ?? '');
            $alokasiTtPasien = $ttPerBangsal[$bangsalKd] ?? '-';

            // Hitung agregat summary
            $summary['total_pasien']++;
            if ($isBpjs) {
                $summary['bpjs_count']++;
            } else {
                $summary['umum_count']++;
            }
            if ($isAwalBulan) {
                $summary['pasien_awal_bulan']++;
            }
            if ($isPindahan) {
                $summary['pasien_pindahan']++;
            }
            if ($isDipindahkan) {
                $summary['pasien_dipindahkan']++;
            }

            $summary['total_hp_vvip']    += $hpVvip;
            $summary['total_hp_vip']     += $hpVip;
            $summary['total_hp_kelas_1'] += $hpKelas1;
            $summary['total_hp_kelas_2'] += $hpKelas2;
            $summary['total_hp_kelas_3'] += $hpKelas3;
            $summary['total_hp_khusus']  += $hpKhusus;
            $summary['total_hp_all']     += $totalHp;

            $patients[] = [
                'no_rawat'              => $noRawat,
                'no_rkm_medis'          => $firstStay->no_rkm_medis,
                'nm_pasien'             => $firstStay->nm_pasien,
                'jk'                    => $firstStay->jk,
                'png_jawab'             => $firstStay->png_jawab,
                'kd_bangsal'            => $bangsalKd,
                'nm_bangsal'            => $lastStay->nm_bangsal ?? $firstStay->nm_bangsal,
                'kd_kamar'              => $lastStay->kd_kamar ?? $firstStay->kd_kamar,
                'kelas_kamar'           => $lastStay->kelas ?? $firstStay->kelas,
                'tgl_masuk'             => $firstStay->tgl_masuk,
                'tgl_keluar'            => $lastStay->tgl_keluar,
                'stts_pulang'           => $lastStay->stts_pulang,
                'jenis_pelayanan'       => $jenisPelayananStr,
                'pasien_awal_bulan'     => $pasienAwalBulan,
                'pasien_masuk'          => $pasienMasukStr,
                'pasien_pindahan'       => $pasienPindahanStr,
                'pasien_dipindahkan'    => $pasienDipindahkanStr,
                'pasien_keluar_hidup'   => $pasienKeluarHidupStr,
                'mati_48_pria'          => $mati48PriaStr,
                'mati_48_wanita'        => $mati48WanitaStr,
                'hp_vvip'               => $hpVvip,
                'hp_vip'                => $hpVip,
                'hp_kelas_1'            => $hpKelas1,
                'hp_kelas_2'            => $hpKelas2,
                'hp_kelas_3'            => $hpKelas3,
                'hp_khusus'             => $hpKhusus,
                'total_hp'              => $totalHp,
                'alokasi_tt'            => $alokasiTtPasien,
            ];
        }

        return [
            'patients'         => $patients,
            'summary'          => $summary,
            'alokasi_tt_total' => $alokasiTTTotal,
            'tt_per_kelas'     => $ttPerKelas,
            'bulan'            => $bulan,
            'start_date'       => $startDate,
            'end_date'         => $endDate,
            'kd_bangsal'       => $kd_bangsal,
            'jenis_pelayanan'  => $jenis_pelayanan,
        ];
    }

    /**
     * Inisialisasi struktur summary sensus harian ranap 2
     */
    private function initSummary()
    {
        return [
            'total_pasien'        => 0,
            'umum_count'          => 0,
            'bpjs_count'          => 0,
            'pasien_awal_bulan'   => 0,
            'pasien_masuk'        => 0,
            'masuk_igd'           => 0,
            'masuk_obgyn'         => 0,
            'masuk_anak'          => 0,
            'masuk_bedah'         => 0,
            'masuk_lainnya'       => 0,
            'pasien_pindahan'     => 0,
            'pasien_dipindahkan'  => 0,
            'pasien_keluar_hidup' => 0,
            'pasien_keluar_mati'  => 0,
            'mati_48_pria'        => 0,
            'mati_48_wanita'      => 0,
            'total_hp_vvip'       => 0,
            'total_hp_vip'        => 0,
            'total_hp_kelas_1'    => 0,
            'total_hp_kelas_2'    => 0,
            'total_hp_kelas_3'    => 0,
            'total_hp_khusus'     => 0,
            'total_hp_all'        => 0,
        ];
    }
}

