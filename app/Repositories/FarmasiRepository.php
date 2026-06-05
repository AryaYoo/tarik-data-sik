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

    // ==========================================
    // EXTRACTION RAWAT INAP
    // ==========================================
    
    public function getRanapQuery($startDate, $endDate)
    {
        return DB::table('kamar_inap')
            ->join('reg_periksa', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->leftJoin('resume_pasien_ranap', 'kamar_inap.no_rawat', '=', 'resume_pasien_ranap.no_rawat')
            ->whereBetween('kamar_inap.tgl_masuk', [$startDate, $endDate])
            ->select([
                'kamar_inap.no_rawat',
                'pasien.nm_pasien',
                DB::raw("CONCAT(reg_periksa.umurdaftar, ' ', reg_periksa.sttsumur) as umur"),
                'pasien.jk',
                'resume_pasien_ranap.prosedur_utama',
                'resume_pasien_ranap.diagnosa_utama',
                DB::raw("SUM(kamar_inap.lama) as lama")
            ])
            ->groupBy(
                'kamar_inap.no_rawat',
                'pasien.nm_pasien',
                'reg_periksa.umurdaftar',
                'reg_periksa.sttsumur',
                'pasien.jk',
                'resume_pasien_ranap.prosedur_utama',
                'resume_pasien_ranap.diagnosa_utama'
            )
            ->orderBy('kamar_inap.tgl_masuk', 'desc');
    }

    public function getInstruksi($noRawatList)
    {
        return DB::table('pemeriksaan_ranap')
            ->whereIn('no_rawat', $noRawatList)
            ->whereNotNull('instruksi')
            ->where('instruksi', '!=', '')
            ->select('no_rawat', 'jam_rawat', 'instruksi')
            ->orderBy('tgl_perawatan', 'asc')
            ->orderBy('jam_rawat', 'asc')
            ->get()
            ->groupBy('no_rawat');
    }

    // ==========================================
    // EXTRACTION RAWAT JALAN
    // ==========================================
    
    public function getRalanQuery($startDate, $endDate)
    {
        return DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->where('reg_periksa.status_lanjut', 'Ralan')
            ->whereBetween('reg_periksa.tgl_registrasi', [$startDate, $endDate])
            ->select([
                'reg_periksa.no_rawat',
                'pasien.nm_pasien',
                DB::raw("CONCAT(reg_periksa.umurdaftar, ' ', reg_periksa.sttsumur) as umur"),
                'pasien.jk'
            ])
            ->orderBy('reg_periksa.tgl_registrasi', 'desc');
    }

    public function getObatRalan($noRawatList)
    {
        return DB::table('detail_pemberian_obat')
            ->join('databarang', 'detail_pemberian_obat.kode_brng', '=', 'databarang.kode_brng')
            ->leftJoin('kodesatuan', 'databarang.kode_sat', '=', 'kodesatuan.kode_sat')
            ->whereIn('detail_pemberian_obat.no_rawat', $noRawatList)
            ->select('detail_pemberian_obat.no_rawat', 'databarang.nama_brng', 'kodesatuan.satuan', DB::raw("SUM(detail_pemberian_obat.jml) as jml"))
            ->groupBy('detail_pemberian_obat.no_rawat', 'databarang.nama_brng', 'kodesatuan.satuan')
            ->get()
            ->groupBy('no_rawat');
    }

    // ==========================================
    // PEMBERIAN OBAT & BHP
    // ==========================================
    
    public function getPemberianQuery($startDate, $endDate)
    {
        return DB::table('detail_pemberian_obat')
            ->join('databarang', 'detail_pemberian_obat.kode_brng', '=', 'databarang.kode_brng')
            ->leftJoin('kodesatuan', 'databarang.kode_sat', '=', 'kodesatuan.kode_sat')
            ->whereBetween('detail_pemberian_obat.tgl_perawatan', [$startDate, $endDate])
            ->select([
                'databarang.nama_brng as barang',
                'kodesatuan.satuan',
                DB::raw('SUM(detail_pemberian_obat.jml) as jumlah')
            ])
            ->groupBy('databarang.nama_brng', 'kodesatuan.satuan');
    }

    public function getPemberianDetails($startDate, $endDate)
    {
        return DB::table('detail_pemberian_obat')
            ->join('databarang', 'detail_pemberian_obat.kode_brng', '=', 'databarang.kode_brng')
            ->join('reg_periksa', 'detail_pemberian_obat.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->leftJoin('kodesatuan', 'databarang.kode_sat', '=', 'kodesatuan.kode_sat')
            ->whereBetween('detail_pemberian_obat.tgl_perawatan', [$startDate, $endDate])
            ->select([
                'databarang.nama_brng as barang',
                'dokter.nm_dokter as dokter',
                'kodesatuan.satuan',
                DB::raw('SUM(detail_pemberian_obat.jml) as jumlah')
            ])
            ->groupBy('databarang.nama_brng', 'dokter.nm_dokter', 'kodesatuan.satuan')
            ->get()
            ->groupBy('barang');
    }

    // ==========================================
    // PENERIMAAN OBAT & BHP FARMASI
    // ==========================================
    
    public function getPenerimaanQuery($startDate, $endDate)
    {
        // Dalam SIMKES Khanza, data penerimaan/pemesanan barang biasanya 
        // dicatat di tabel detailpesan dan pemesanan
        return DB::table('detailpesan')
            ->join('pemesanan', 'detailpesan.no_faktur', '=', 'pemesanan.no_faktur')
            ->join('databarang', 'detailpesan.kode_brng', '=', 'databarang.kode_brng')
            ->leftJoin('kodesatuan', 'databarang.kode_sat', '=', 'kodesatuan.kode_sat')
            ->whereBetween('pemesanan.tgl_pesan', [$startDate, $endDate])
            ->select([
                'databarang.nama_brng as barang',
                'kodesatuan.satuan',
                DB::raw('SUM(detailpesan.jumlah) as jumlah')
            ])
            ->groupBy('databarang.nama_brng', 'kodesatuan.satuan');
    }

    // ==========================================
    // HARGA BARANG
    // ==========================================
    
    public function getHargaBarangQuery($search = null)
    {
        $query = DB::table('databarang')
            ->leftJoin('kodesatuan', 'databarang.kode_sat', '=', 'kodesatuan.kode_sat')
            ->select([
                'databarang.kode_brng',
                'databarang.nama_brng',
                'kodesatuan.satuan',
                'databarang.dasar',
                'databarang.h_beli',
                'databarang.ralan',
                'databarang.kelas1',
                'databarang.kelas2',
                'databarang.kelas3',
                'databarang.utama',
                'databarang.vip',
                'databarang.vvip',
                'databarang.beliluar',
                'databarang.jualbebas',
                'databarang.karyawan',
                'databarang.status'
            ]);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('databarang.kode_brng', 'like', "%{$search}%")
                  ->orWhere('databarang.nama_brng', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('databarang.nama_brng', 'asc');
    }

    public function getWaktuTungguBpjs($startDate, $endDate)
    {
        return DB::table('resep_obat')
            ->join('reg_periksa', 'resep_obat.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->where('resep_obat.status', 'ralan')
            ->where('penjab.png_jawab', 'LIKE', '%BPJS%')
            ->whereBetween('resep_obat.tgl_perawatan', [$startDate, $endDate])
            ->select([
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

    public function getAverageWaktuTungguBpjs($startDate, $endDate)
    {
        return DB::table('resep_obat')
            ->join('reg_periksa', 'resep_obat.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->where('resep_obat.status', 'ralan')
            ->where('penjab.png_jawab', 'LIKE', '%BPJS%')
            ->whereBetween('resep_obat.tgl_perawatan', [$startDate, $endDate])
            ->select(
                DB::raw("AVG(TIME_TO_SEC(TIMEDIFF(
                    CONCAT(resep_obat.tgl_penyerahan, ' ', resep_obat.jam_penyerahan), 
                    CONCAT(resep_obat.tgl_perawatan, ' ', resep_obat.jam)
                ))) as avg_seconds")
            )->first()->avg_seconds;
    }

    public function getSirkulasiObatQuery($startDate, $endDate)
    {
        // Sumber data: tabel riwayat_barang_medis (buku besar mutasi stok SIMRS Khanza).
        // Kolom masuk/keluar di SIMRS Khanza TIDAK selalu menyimpan delta kuantitas murni
        // (kadang berisi nilai absolut, misal saat stok opname), sehingga SUM(masuk)/SUM(keluar)
        // tidak akurat untuk perhitungan sirkulasi.
        //
        // SOLUSI: Hitung pergerakan nyata per-baris dari selisih stok_akhir vs stok_awal:
        //   Penerimaan = SUM(GREATEST(stok_akhir - stok_awal, 0))  → total kenaikan stok per transaksi
        //   Pemberian  = SUM(GREATEST(stok_awal - stok_akhir, 0))  → total penurunan stok per transaksi
        //
        // DIJAMIN BALANCE: Stok Awal + Penerimaan - Pemberian = Stok Akhir (sesuai logika farmasi).

        return DB::table('databarang')
            ->leftJoin('kodesatuan', 'databarang.kode_sat', '=', 'kodesatuan.kode_sat')
            ->whereExists(function ($query) use ($startDate, $endDate) {
                $query->select(DB::raw(1))
                    ->from('riwayat_barang_medis')
                    ->whereColumn('riwayat_barang_medis.kode_brng', 'databarang.kode_brng')
                    ->where('riwayat_barang_medis.status', 'Simpan')
                    ->whereBetween('riwayat_barang_medis.tanggal', [$startDate, $endDate]);
            })
            ->select([
                'databarang.kode_brng',
                'databarang.nama_brng',
                'kodesatuan.satuan',
                'databarang.h_beli as harga_beli',
                DB::raw("(
                    SELECT r1.stok_awal 
                    FROM riwayat_barang_medis r1 
                    WHERE r1.kode_brng = databarang.kode_brng 
                      AND r1.tanggal BETWEEN '$startDate' AND '$endDate' 
                      AND r1.status = 'Simpan' 
                    ORDER BY r1.tanggal ASC, r1.jam ASC 
                    LIMIT 1
                ) as stok_awal"),
                DB::raw("(
                    SELECT r2.stok_akhir 
                    FROM riwayat_barang_medis r2 
                    WHERE r2.kode_brng = databarang.kode_brng 
                      AND r2.tanggal BETWEEN '$startDate' AND '$endDate' 
                      AND r2.status = 'Simpan' 
                    ORDER BY r2.tanggal DESC, r2.jam DESC 
                    LIMIT 1
                ) as stok_akhir"),
                DB::raw("(
                    SELECT COALESCE(SUM(GREATEST(r.stok_akhir - r.stok_awal, 0)), 0)
                    FROM riwayat_barang_medis r
                    WHERE r.kode_brng = databarang.kode_brng
                      AND r.tanggal BETWEEN '$startDate' AND '$endDate'
                      AND r.status = 'Simpan'
                ) as penerimaan"),
                DB::raw("(
                    SELECT COALESCE(SUM(GREATEST(r.stok_akhir - r.stok_awal, 0)), 0)
                    FROM riwayat_barang_medis r
                    WHERE r.kode_brng = databarang.kode_brng
                      AND r.tanggal BETWEEN '$startDate' AND '$endDate'
                      AND r.status = 'Simpan'
                      AND r.posisi IN ('Penerimaan', 'Pengadaan')
                ) as pengadaan"),
                DB::raw("(
                    SELECT COALESCE(SUM(GREATEST(r.stok_akhir - r.stok_awal, 0)), 0)
                    FROM riwayat_barang_medis r
                    WHERE r.kode_brng = databarang.kode_brng
                      AND r.tanggal BETWEEN '$startDate' AND '$endDate'
                      AND r.status = 'Simpan'
                      AND r.posisi = 'Retur Pasien'
                ) as retur_pasien"),
                DB::raw("(
                    SELECT COALESCE(SUM(GREATEST(r.stok_akhir - r.stok_awal, 0)), 0)
                    FROM riwayat_barang_medis r
                    WHERE r.kode_brng = databarang.kode_brng
                      AND r.tanggal BETWEEN '$startDate' AND '$endDate'
                      AND r.status = 'Simpan'
                      AND r.posisi = 'Mutasi'
                ) as mutasi_masuk"),
                DB::raw("(
                    SELECT COALESCE(SUM(GREATEST(r.stok_akhir - r.stok_awal, 0)), 0)
                    FROM riwayat_barang_medis r
                    WHERE r.kode_brng = databarang.kode_brng
                      AND r.tanggal BETWEEN '$startDate' AND '$endDate'
                      AND r.status = 'Simpan'
                      AND r.posisi = 'Opname'
                ) as opname_lebih"),
                DB::raw("(
                    SELECT COALESCE(SUM(GREATEST(r.stok_akhir - r.stok_awal, 0)), 0)
                    FROM riwayat_barang_medis r
                    WHERE r.kode_brng = databarang.kode_brng
                      AND r.tanggal BETWEEN '$startDate' AND '$endDate'
                      AND r.status = 'Simpan'
                      AND r.posisi NOT IN ('Penerimaan', 'Pengadaan', 'Retur Pasien', 'Mutasi', 'Opname')
                ) as lain_lain_masuk"),

                // Referensi Draft
                DB::raw("(
                    SELECT COALESCE(SUM(rd.jml), 0)
                    FROM resep_dokter rd
                    JOIN resep_obat ro ON rd.no_resep = ro.no_resep
                    WHERE rd.kode_brng = databarang.kode_brng
                      AND ro.tgl_perawatan BETWEEN '$startDate' AND '$endDate'
                ) as resep_dokter"),

                // Total Keluar
                DB::raw("(
                    SELECT COALESCE(SUM(GREATEST(r.stok_awal - r.stok_akhir, 0)), 0)
                    FROM riwayat_barang_medis r
                    WHERE r.kode_brng = databarang.kode_brng
                      AND r.tanggal BETWEEN '$startDate' AND '$endDate'
                      AND r.status = 'Simpan'
                ) as distribusi"),

                // Rincian Keluar (dari riwayat_barang_medis)
                DB::raw("(
                    SELECT COALESCE(SUM(GREATEST(r.stok_awal - r.stok_akhir, 0)), 0)
                    FROM riwayat_barang_medis r
                    WHERE r.kode_brng = databarang.kode_brng
                      AND r.tanggal BETWEEN '$startDate' AND '$endDate'
                      AND r.status = 'Simpan'
                      AND r.posisi = 'Pemberian Obat'
                ) as pemberian_obat"),
                DB::raw("(
                    SELECT COALESCE(SUM(GREATEST(r.stok_awal - r.stok_akhir, 0)), 0)
                    FROM riwayat_barang_medis r
                    WHERE r.kode_brng = databarang.kode_brng
                      AND r.tanggal BETWEEN '$startDate' AND '$endDate'
                      AND r.status = 'Simpan'
                      AND r.posisi = 'Resep Pulang'
                ) as resep_pulang"),
                DB::raw("(
                    SELECT COALESCE(SUM(GREATEST(r.stok_awal - r.stok_akhir, 0)), 0)
                    FROM riwayat_barang_medis r
                    WHERE r.kode_brng = databarang.kode_brng
                      AND r.tanggal BETWEEN '$startDate' AND '$endDate'
                      AND r.status = 'Simpan'
                      AND r.posisi = 'Penjualan'
                ) as detail_jual"),
                DB::raw("(
                    SELECT COALESCE(SUM(GREATEST(r.stok_awal - r.stok_akhir, 0)), 0)
                    FROM riwayat_barang_medis r
                    WHERE r.kode_brng = databarang.kode_brng
                      AND r.tanggal BETWEEN '$startDate' AND '$endDate'
                      AND r.status = 'Simpan'
                      AND r.posisi = 'Stok Keluar'
                ) as stok_keluar"),
                DB::raw("(
                    SELECT COALESCE(SUM(GREATEST(r.stok_awal - r.stok_akhir, 0)), 0)
                    FROM riwayat_barang_medis r
                    WHERE r.kode_brng = databarang.kode_brng
                      AND r.tanggal BETWEEN '$startDate' AND '$endDate'
                      AND r.status = 'Simpan'
                      AND r.posisi = 'Mutasi'
                ) as mutasi_keluar"),
                DB::raw("(
                    SELECT COALESCE(SUM(GREATEST(r.stok_awal - r.stok_akhir, 0)), 0)
                    FROM riwayat_barang_medis r
                    WHERE r.kode_brng = databarang.kode_brng
                      AND r.tanggal BETWEEN '$startDate' AND '$endDate'
                      AND r.status = 'Simpan'
                      AND r.posisi = 'Hibah'
                ) as hibah"),
                DB::raw("(
                    SELECT COALESCE(SUM(GREATEST(r.stok_awal - r.stok_akhir, 0)), 0)
                    FROM riwayat_barang_medis r
                    WHERE r.kode_brng = databarang.kode_brng
                      AND r.tanggal BETWEEN '$startDate' AND '$endDate'
                      AND r.status = 'Simpan'
                      AND r.posisi = 'Retur Beli'
                ) as retur_supplier"),
                DB::raw("(
                    SELECT COALESCE(SUM(GREATEST(r.stok_awal - r.stok_akhir, 0)), 0)
                    FROM riwayat_barang_medis r
                    WHERE r.kode_brng = databarang.kode_brng
                      AND r.tanggal BETWEEN '$startDate' AND '$endDate'
                      AND r.status = 'Simpan'
                      AND r.posisi = 'Opname'
                ) as opname_kurang"),
                DB::raw("(
                    SELECT COALESCE(SUM(GREATEST(r.stok_awal - r.stok_akhir, 0)), 0)
                    FROM riwayat_barang_medis r
                    WHERE r.kode_brng = databarang.kode_brng
                      AND r.tanggal BETWEEN '$startDate' AND '$endDate'
                      AND r.status = 'Simpan'
                      AND r.posisi = 'Pengambilan Medis'
                ) as pengambilan_medis"),
                DB::raw("(
                    SELECT COALESCE(SUM(GREATEST(r.stok_awal - r.stok_akhir, 0)), 0)
                    FROM riwayat_barang_medis r
                    WHERE r.kode_brng = databarang.kode_brng
                      AND r.tanggal BETWEEN '$startDate' AND '$endDate'
                      AND r.status = 'Simpan'
                      AND r.posisi NOT IN ('Pemberian Obat', 'Resep Pulang', 'Penjualan', 'Stok Keluar', 'Mutasi', 'Hibah', 'Retur Beli', 'Opname', 'Pengambilan Medis')
                ) as lain_lain_keluar")
            ])
            ->orderBy('databarang.nama_brng', 'asc');
    }

    public function getOpnameQuery($date, $kdBangsal = null)
    {
        $query = DB::table('opname')
            ->join('databarang', 'opname.kode_brng', '=', 'databarang.kode_brng')
            ->leftJoin('kodesatuan', 'databarang.kode_sat', '=', 'kodesatuan.kode_sat')
            ->join('bangsal', 'opname.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->where('opname.tanggal', $date)
            ->select([
                'databarang.kode_brng',
                'databarang.nama_brng',
                'kodesatuan.satuan',
                'opname.stok as stok_sistem',
                'opname.real as stok_fisik',
                'opname.selisih',
                'opname.h_beli',
                'opname.keterangan',
                'bangsal.nm_bangsal'
            ]);

        if ($kdBangsal) {
            $query->where('opname.kd_bangsal', $kdBangsal);
        }

        return $query->orderBy('databarang.nama_brng', 'asc');
    }

    public function getOpnameDepots()
    {
        return DB::table('opname')
            ->join('bangsal', 'opname.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->select('bangsal.kd_bangsal', 'bangsal.nm_bangsal')
            ->distinct()
            ->orderBy('bangsal.nm_bangsal', 'asc')
            ->get();
    }

    public function getTemplateBuSugatiQuery($startDate, $endDate)
    {
        return DB::table('databarang')
            ->leftJoin('kodesatuan', 'databarang.kode_sat', '=', 'kodesatuan.kode_sat')
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereExists(function ($q) use ($startDate, $endDate) {
                    $q->select(DB::raw(1))
                        ->from('riwayat_barang_medis')
                        ->whereColumn('riwayat_barang_medis.kode_brng', 'databarang.kode_brng')
                        ->where('riwayat_barang_medis.status', 'Simpan')
                        ->whereBetween('riwayat_barang_medis.tanggal', [$startDate, $endDate]);
                })
                ->orWhereExists(function ($q) use ($startDate, $endDate) {
                    $q->select(DB::raw(1))
                        ->from('detailpesan')
                        ->join('pemesanan', 'detailpesan.no_faktur', '=', 'pemesanan.no_faktur')
                        ->whereColumn('detailpesan.kode_brng', 'databarang.kode_brng')
                        ->whereBetween('pemesanan.tgl_pesan', [$startDate, $endDate]);
                })
                ->orWhereExists(function ($q) use ($startDate, $endDate) {
                    $q->select(DB::raw(1))
                        ->from('detail_pemberian_obat')
                        ->whereColumn('detail_pemberian_obat.kode_brng', 'databarang.kode_brng')
                        ->whereBetween('detail_pemberian_obat.tgl_perawatan', [$startDate, $endDate]);
                });
            })
            ->select([
                'databarang.kode_brng',
                'databarang.nama_brng',
                'kodesatuan.satuan',
                'databarang.h_beli as harga_beli',
                DB::raw("(
                    SELECT r1.stok_awal 
                    FROM riwayat_barang_medis r1 
                    WHERE r1.kode_brng = databarang.kode_brng 
                      AND r1.tanggal BETWEEN '$startDate' AND '$endDate' 
                      AND r1.status = 'Simpan' 
                    ORDER BY r1.tanggal ASC, r1.jam ASC 
                    LIMIT 1
                ) as stok_awal"),
                DB::raw("(
                    SELECT COALESCE(SUM(detailpesan.jumlah), 0)
                    FROM detailpesan
                    JOIN pemesanan ON detailpesan.no_faktur = pemesanan.no_faktur
                    WHERE detailpesan.kode_brng = databarang.kode_brng
                      AND pemesanan.tgl_pesan BETWEEN '$startDate' AND '$endDate'
                ) as penerimaan"),
                DB::raw("(
                    SELECT COALESCE(SUM(detail_pemberian_obat.jml), 0)
                    FROM detail_pemberian_obat
                    WHERE detail_pemberian_obat.kode_brng = databarang.kode_brng
                      AND detail_pemberian_obat.tgl_perawatan BETWEEN '$startDate' AND '$endDate'
                ) as pemberian")
            ])
            ->orderBy('databarang.nama_brng', 'asc');
    }
}
