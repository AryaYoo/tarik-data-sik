<!DOCTYPE html>
<html>

<head>
    <title>Laporan Template Bu Sugati</title>
    <style>
        @page {
            size: landscape;
            margin: 20px;
        }

        body {
            font-family: sans-serif;
            font-size: 8px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 4px;
            text-align: left;
        }

        th {
            background-color: #007C3C;
            color: white;
            text-align: center;
            text-transform: uppercase;
            font-size: 7.5px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 7px;
            color: #777;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2 style="margin: 0; padding: 0; color: #007C3C; text-transform: uppercase; font-size: 14px;">Laporan Rencana Anggaran</h2>
        <p style="margin: 5px 0 0 0; font-size: 9px;">Periode: {{ \Carbon\Carbon::parse($tgl_mulai)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($tgl_selesai)->format('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="70">Kode Barang</th>
                <th>Nama Barang</th>
                <th width="50" class="text-center">Satuan</th>
                <th width="65" class="text-right">Harga Barang</th>
                <th width="50" class="text-right">Stok Awal</th>
                <th width="55" class="text-right">Penerimaan</th>
                <th width="55" class="text-right">Pemberian</th>
                <th width="55" class="text-right" style="display: none;">Resep Dokter</th>
                <th width="55" class="text-right" style="display: none;">Detail Jual</th>
                <th width="50" class="text-right">Stok Akhir</th>
                <th width="65" class="text-right">Buffer Stock 15%</th>
                <th width="70" class="text-right">Rencana Pemakaian</th>
                <th width="70" class="text-right">Rencana Pengadaan</th>
                <th width="75" class="text-right">Rencana Anggaran</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
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
                    <td style="font-weight: bold;">{{ $item->kode_brng }}</td>
                    <td style="text-transform: uppercase;">{{ $item->nama_brng }}</td>
                    <td class="text-center" style="text-transform: uppercase;">{{ $item->satuan }}</td>
                    <td class="text-right">Rp {{ number_format($harga_beli, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($stok_awal, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($penerimaan, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($pemberian, 0, ',', '.') }}</td>
                    <td class="text-right" style="color:#7c3aed; display: none;">{{ number_format($item->resep_dokter, 0, ',', '.') }}</td>
                    <td class="text-right" style="color:#c2410c; display: none;">{{ number_format($item->detail_jual, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($stok_akhir, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($buffer_stock, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($rencana_pemakaian, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($rencana_pengadaan, 0, ',', '.') }}</td>
                    <td class="text-right" style="font-weight: bold; color: #007C3C;">Rp {{ number_format($rencana_anggaran, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="text-center" style="font-style: italic; color: #777; padding: 20px;">Tidak ada data pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak oleh TARIKSIS Portal pada: {{ date('d/m/Y H:i:s') }}</p>
    </div>
</body>

</html>
