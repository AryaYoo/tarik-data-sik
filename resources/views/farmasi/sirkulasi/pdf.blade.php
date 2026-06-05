<!DOCTYPE html>
<html>

<head>
    <title>Laporan Perputaran Obat (Sirkulasi)</title>
    <style>
        body { font-family: sans-serif; font-size: 7px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 3px 4px; text-align: left; }
        th { background-color: #007C3C; color: white; text-align: center; text-transform: uppercase; font-size: 6.5px; }
        .header { text-align: center; margin-bottom: 15px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .footer { margin-top: 15px; text-align: right; font-size: 6px; color: #777; }
        .bg-green { background-color: #f0fdf4; }
        .bg-red { background-color: #fef2f2; }
        .text-green { color: #15803d; }
        .text-red { color: #dc2626; }
        .badge-balance { background: #f0fdf4; color: #15803d; padding: 1px 4px; border-radius: 3px; font-weight: bold; font-size: 6px; }
        .badge-selisih { background: #fef2f2; color: #dc2626; padding: 1px 4px; border-radius: 3px; font-weight: bold; font-size: 6px; }
    </style>
</head>

<body>
    <div class="header">
        <h2 style="margin: 0; padding: 0; color: #007C3C; text-transform: uppercase; font-size: 14px;">Laporan Perputaran Obat (Sirkulasi)</h2>
        <h4 style="margin: 4px 0 0 0; padding: 0; color: #666; font-weight: normal; font-size: 9px;">Rincian Lengkap Semua Sumber Transaksi Masuk & Keluar per Barang</h4>
        <p style="margin: 6px 0 0 0; font-size: 8px;">Periode: {{ \Carbon\Carbon::parse($tgl_mulai)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($tgl_selesai)->format('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" width="35">Kode Barang</th>
                <th rowspan="2">Nama Barang</th>
                <th rowspan="2" width="20">Sat.</th>
                <th rowspan="2" width="40" class="text-right">Harga</th>
                <th rowspan="2" width="25" class="text-right">Awal</th>
                <!-- Masuk -->
                <th colspan="6" class="bg-green text-green">&#x2B06; MASUK</th>
                <!-- Netral -->
                <th rowspan="2" width="25" class="text-right">Resep Dr</th>
                <!-- Keluar -->
                <th colspan="11" style="background-color:#dc2626; color:white;">&#x2B07; KELUAR</th>
                <!-- Akhir -->
                <th rowspan="2" width="25" class="text-right">Akhir</th>
                <th rowspan="2" width="30" class="text-center">Ket.</th>
            </tr>
            <tr>
                <th width="30" class="bg-green text-green">Total</th>
                <th width="20" class="bg-green text-green">Peng.</th>
                <th width="20" class="bg-green text-green">Rtr.P</th>
                <th width="20" class="bg-green text-green">Mutasi</th>
                <th width="20" class="bg-green text-green">Op(+)</th>
                <th width="20" class="bg-green text-green">Lain</th>
                <th width="30" style="background-color:#fecaca; color:#dc2626;">Total</th>
                <th width="20" style="background-color:#fecaca; color:#dc2626;">Beri</th>
                <th width="20" style="background-color:#fecaca; color:#dc2626;">R.Plg</th>
                <th width="20" style="background-color:#fecaca; color:#dc2626;">Jual</th>
                <th width="20" style="background-color:#fecaca; color:#dc2626;">S.Klr</th>
                <th width="20" style="background-color:#fecaca; color:#dc2626;">Mutasi</th>
                <th width="20" style="background-color:#fecaca; color:#dc2626;">Hbh</th>
                <th width="20" style="background-color:#fecaca; color:#dc2626;">Rtr.S</th>
                <th width="20" style="background-color:#fecaca; color:#dc2626;">Op(-)</th>
                <th width="20" style="background-color:#fecaca; color:#dc2626;">Peng.</th>
                <th width="20" style="background-color:#fecaca; color:#dc2626;">Lain</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $item)
                @php
                    $expected = $item->stok_awal + $item->penerimaan - $item->distribusi;
                    $isBalance = (int)$item->stok_akhir === (int)$expected;
                @endphp
                <tr>
                    <td style="font-weight:bold;">{{ $item->kode_brng }}</td>
                    <td style="text-transform:uppercase; font-weight:bold;">{{ $item->nama_brng }}</td>
                    <td class="text-center" style="text-transform:uppercase;">{{ $item->satuan }}</td>
                    <td class="text-right" style="color:#007C3C; font-weight:bold;">{{ number_format($item->harga_beli, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->stok_awal, 0, ',', '.') }}</td>
                    <!-- Masuk -->
                    <td class="text-right text-green bg-green" style="font-weight:bold;">{{ number_format($item->penerimaan, 0, ',', '.') }}</td>
                    <td class="text-right text-green bg-green">{{ number_format($item->pengadaan, 0, ',', '.') }}</td>
                    <td class="text-right text-green bg-green">{{ number_format($item->retur_pasien, 0, ',', '.') }}</td>
                    <td class="text-right text-green bg-green">{{ number_format($item->mutasi_masuk, 0, ',', '.') }}</td>
                    <td class="text-right text-green bg-green">{{ number_format($item->opname_lebih, 0, ',', '.') }}</td>
                    <td class="text-right text-green bg-green">{{ number_format($item->lain_lain_masuk, 0, ',', '.') }}</td>
                    <!-- Resep Dr -->
                    <td class="text-right">{{ number_format($item->resep_dokter, 0, ',', '.') }}</td>
                    <!-- Keluar -->
                    <td class="text-right text-red bg-red" style="font-weight:bold;">{{ number_format($item->distribusi, 0, ',', '.') }}</td>
                    <td class="text-right text-red bg-red">{{ number_format($item->pemberian_obat, 0, ',', '.') }}</td>
                    <td class="text-right text-red bg-red">{{ number_format($item->resep_pulang, 0, ',', '.') }}</td>
                    <td class="text-right text-red bg-red">{{ number_format($item->detail_jual, 0, ',', '.') }}</td>
                    <td class="text-right text-red bg-red">{{ number_format($item->stok_keluar, 0, ',', '.') }}</td>
                    <td class="text-right text-red bg-red">{{ number_format($item->mutasi_keluar, 0, ',', '.') }}</td>
                    <td class="text-right text-red bg-red">{{ number_format($item->hibah, 0, ',', '.') }}</td>
                    <td class="text-right text-red bg-red">{{ number_format($item->retur_supplier, 0, ',', '.') }}</td>
                    <td class="text-right text-red bg-red">{{ number_format($item->opname_kurang, 0, ',', '.') }}</td>
                    <td class="text-right text-red bg-red">{{ number_format($item->pengambilan_medis, 0, ',', '.') }}</td>
                    <td class="text-right text-red bg-red">{{ number_format($item->lain_lain_keluar, 0, ',', '.') }}</td>
                    <!-- Akhir -->
                    <td class="text-right" style="font-weight:bold;">{{ number_format($item->stok_akhir, 0, ',', '.') }}</td>
                    <td class="text-center">
                        @if($isBalance)
                            <span class="badge-balance">&#10003; Blnc</span>
                        @else
                            <span class="badge-selisih">S:{{ number_format(abs($item->stok_akhir - $expected), 0, ',', '.') }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="25" class="text-center" style="font-style:italic; color:#777; padding:15px;">Tidak ada data sirkulasi pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak oleh TARIKSIS Portal pada: {{ date('d/m/Y H:i:s') }} | Format: A4 Landscape</p>
    </div>
</body>

</html>