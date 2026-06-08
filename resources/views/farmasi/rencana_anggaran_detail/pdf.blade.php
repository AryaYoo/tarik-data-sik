<!DOCTYPE html>
<html>

<head>
    <title>Laporan Rencana Anggaran Detail</title>
    <style>
        body { font-family: sans-serif; font-size: 6px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 2px 3px; text-align: left; }
        th { background-color: #007C3C; color: white; text-align: center; text-transform: uppercase; font-size: 5.5px; }
        .header { text-align: center; margin-bottom: 15px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .footer { margin-top: 15px; text-align: right; font-size: 5.5px; color: #777; }
        .bg-green { background-color: #f0fdf4; }
        .bg-red { background-color: #fef2f2; }
        .bg-blue { background-color: #eff6ff; }
        .bg-orange { background-color: #fff7ed; }
        .text-green { color: #15803d; }
        .text-red { color: #dc2626; }
        .text-blue { color: #1d4ed8; }
        .text-orange { color: #c2410c; }
    </style>
</head>

<body>
    <div class="header">
        <h2 style="margin: 0; padding: 0; color: #007C3C; text-transform: uppercase; font-size: 12px;">Laporan Rencana Anggaran Detail</h2>
        <h4 style="margin: 2px 0 0 0; padding: 0; color: #666; font-weight: normal; font-size: 8px;">Rencana Pengadaan & Anggaran Farmasi Lengkap dengan Rincian Transaksi Masuk/Keluar</h4>
        <p style="margin: 4px 0 0 0; font-size: 7px;">Periode: {{ \Carbon\Carbon::parse($tgl_mulai)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($tgl_selesai)->format('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" width="28">Kode</th>
                <th rowspan="2">Nama Barang</th>
                <th rowspan="2" width="18">Sat.</th>
                <th rowspan="2" width="38" class="text-right">Harga</th>
                <th rowspan="2" width="20" class="text-right">Awal</th>
                <!-- Masuk -->
                <th @if($show_detail) colspan="6" @else colspan="1" @endif class="bg-green text-green">&#x2B06; MASUK</th>
                <!-- Netral -->
                <th rowspan="2" width="20" class="text-right">Resep Dr</th>
                <!-- Keluar -->
                <th @if($show_detail) colspan="11" @else colspan="1" @endif style="background-color:#dc2626; color:white;">&#x2B07; KELUAR</th>
                <!-- Perencanaan -->
                <th colspan="5" class="bg-blue text-blue">Rencana Anggaran</th>
            </tr>
            <tr>
                <th width="22" class="bg-green text-green">Total</th>
                @if($show_detail)
                <th width="15" class="bg-green text-green">Peng</th>
                <th width="15" class="bg-green text-green">Rtr.P</th>
                <th width="15" class="bg-green text-green">Mutasi</th>
                <th width="15" class="bg-green text-green">Op(+)</th>
                <th width="15" class="bg-green text-green">Lain</th>
                @endif
                <th width="22" style="background-color:#fecaca; color:#dc2626;">Total</th>
                @if($show_detail)
                <th width="15" style="background-color:#fecaca; color:#dc2626;">Beri</th>
                <th width="15" style="background-color:#fecaca; color:#dc2626;">R.Plg</th>
                <th width="15" style="background-color:#fecaca; color:#dc2626;">Jual</th>
                <th width="15" style="background-color:#fecaca; color:#dc2626;">S.Klr</th>
                <th width="15" style="background-color:#fecaca; color:#dc2626;">Mutasi</th>
                <th width="15" style="background-color:#fecaca; color:#dc2626;">Hbh</th>
                <th width="15" style="background-color:#fecaca; color:#dc2626;">Rtr.S</th>
                <th width="15" style="background-color:#fecaca; color:#dc2626;">Op(-)</th>
                <th width="15" style="background-color:#fecaca; color:#dc2626;">Peng</th>
                <th width="15" style="background-color:#fecaca; color:#dc2626;">Lain</th>
                @endif
                
                <th width="20" class="bg-blue text-blue">Akhir</th>
                <th width="20" class="bg-blue text-blue">Buffer</th>
                <th width="20" class="bg-blue text-blue">Pakai</th>
                <th width="20" class="bg-blue text-blue">Order</th>
                <th width="38" class="bg-blue text-blue">Anggaran</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $item)
                @php
                    $harga_beli = (float) $item->harga_beli;
                    $stok_awal = (float) $item->stok_awal;
                    $penerimaan = (float) $item->penerimaan;
                    $pemberian = (float) $item->pemberian;

                    $stok_akhir = $stok_awal + $penerimaan - $pemberian;
                    $buffer_stock = floor($pemberian * 0.15);
                    $rencana_pemakaian = $pemberian + $buffer_stock;
                    
                    $rencana_pengadaan = 0;
                    if ($rencana_pemakaian > $stok_akhir) {
                        $rencana_pengadaan = $rencana_pemakaian - $stok_akhir;
                    }

                    $rencana_anggaran = $rencana_pengadaan * $harga_beli;
                @endphp
                <tr>
                    <td style="font-weight:bold;">{{ $item->kode_brng }}</td>
                    <td style="text-transform:uppercase; font-weight:bold;">{{ $item->nama_brng }}</td>
                    <td class="text-center" style="text-transform:uppercase;">{{ $item->satuan }}</td>
                    <td class="text-right" style="color:#007C3C; font-weight:bold;">Rp {{ number_format($harga_beli, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($stok_awal, 0, ',', '.') }}</td>
                    <!-- Masuk -->
                    <td class="text-right text-green bg-green" style="font-weight:bold;">{{ number_format($penerimaan, 0, ',', '.') }}</td>
                    @if($show_detail)
                    <td class="text-right text-green bg-green">{{ number_format($item->pengadaan, 0, ',', '.') }}</td>
                    <td class="text-right text-green bg-green">{{ number_format($item->retur_pasien, 0, ',', '.') }}</td>
                    <td class="text-right text-green bg-green">{{ number_format($item->mutasi_masuk, 0, ',', '.') }}</td>
                    <td class="text-right text-green bg-green">{{ number_format($item->opname_lebih, 0, ',', '.') }}</td>
                    <td class="text-right text-green bg-green">{{ number_format($item->lain_lain_masuk, 0, ',', '.') }}</td>
                    @endif
                    <!-- Resep Dr -->
                    <td class="text-right">{{ number_format($item->resep_dokter, 0, ',', '.') }}</td>
                    <!-- Keluar -->
                    <td class="text-right text-red bg-red" style="font-weight:bold;">{{ number_format($pemberian, 0, ',', '.') }}</td>
                    @if($show_detail)
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
                    @endif
                    <!-- Perencanaan -->
                    <td class="text-right bg-blue" style="font-weight:bold;">{{ number_format($stok_akhir, 0, ',', '.') }}</td>
                    <td class="text-right bg-blue">{{ number_format($buffer_stock, 0, ',', '.') }}</td>
                    <td class="text-right bg-blue">{{ number_format($rencana_pemakaian, 0, ',', '.') }}</td>
                    <td class="text-right text-blue bg-blue" style="font-weight:bold;">{{ number_format($rencana_pengadaan, 0, ',', '.') }}</td>
                    <td class="text-right text-orange bg-orange" style="font-weight:bold;">Rp {{ number_format($rencana_anggaran, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $show_detail ? 28 : 13 }}" class="text-center" style="font-style: italic; color: #777; padding: 20px;">Tidak ada data pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak oleh TARIKSIS Portal pada: {{ date('d/m/Y H:i:s') }}</p>
    </div>
</body>

</html>
