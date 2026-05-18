<!DOCTYPE html>
<html>
<head>
    <title>Laporan Perputaran Obat (Sirkulasi)</title>
    <style>
        body { font-family: sans-serif; font-size: 9px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 5px; text-align: left; }
        th { background-color: #007C3C; color: white; text-align: center; text-transform: uppercase; font-size: 8px; }
        .header { text-align: center; margin-bottom: 30px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .footer { margin-top: 30px; text-align: right; font-size: 7px; color: #777; }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin: 0; padding: 0; color: #007C3C; text-transform: uppercase; font-size: 16px;">Laporan Perputaran Obat (Sirkulasi)</h2>
        <h4 style="margin: 5px 0 0 0; padding: 0; color: #666; font-weight: normal; font-size: 11px;">Stok Awal, Stok Akhir, Selisih, Pengadaan & Harga Beli</h4>
        <p style="margin: 10px 0 0 0; font-size: 9px;">Periode: {{ \Carbon\Carbon::parse($tgl_mulai)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($tgl_selesai)->format('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nama Obat</th>
                <th width="90" class="text-right">Stok Awal</th>
                <th width="90" class="text-right">Stok Akhir</th>
                <th width="90" class="text-right">Selisih</th>
                <th width="100" class="text-right">Jumlah Pengadaan</th>
                <th width="80" class="text-right">Harga Beli</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
                <tr>
                    <td style="text-transform: uppercase; font-weight: bold;">
                        {{ $item->nama_brng }}
                        <div style="font-size: 7px; color: #777; font-weight: normal; margin-top: 2px;">{{ $item->satuan }}</div>
                    </td>
                    <td class="text-right">
                        {{ number_format($item->stok_awal, 0, ',', '.') }}
                    </td>
                    <td class="text-right">
                        {{ number_format($item->stok_akhir, 0, ',', '.') }}
                    </td>
                    @php
                        $selisih = $item->stok_akhir - $item->stok_awal;
                        
                        $selisihColor = '#333';
                        $selisihSign = '';
                        if ($selisih > 0) {
                            $selisihColor = '#16A34A'; // green-600
                            $selisihSign = '+';
                        } elseif ($selisih < 0) {
                            $selisihColor = '#DC2626'; // red-600
                        }
                    @endphp
                    <td class="text-right" style="color: {{ $selisihColor }}; font-weight: bold;">
                        {{ $selisihSign }}{{ number_format($selisih, 0, ',', '.') }}
                    </td>
                    <td class="text-right">
                        {{ number_format($item->jumlah_pengadaan, 0, ',', '.') }}
                    </td>
                    <td class="text-right" style="font-weight: bold; color: #007C3C;">
                        Rp {{ number_format($item->harga_beli, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="font-style: italic; color: #777; padding: 20px;">Tidak ada data sirkulasi pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak oleh TARIKSIS Portal pada: {{ date('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
