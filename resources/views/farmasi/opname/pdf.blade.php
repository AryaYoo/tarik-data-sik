<!DOCTYPE html>
<html>
<head>
    <title>Laporan Stock Opname Farmasi</title>
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
        <h2 style="margin: 0; padding: 0; color: #007C3C; text-transform: uppercase; font-size: 16px;">Laporan Stock Opname Farmasi</h2>
        <h4 style="margin: 5px 0 0 0; padding: 0; color: #666; font-weight: normal; font-size: 11px;">Pencocokan Stok Fisik vs Catatan Sistem</h4>
        <p style="margin: 10px 0 0 0; font-size: 9px;">
            Tanggal: {{ \Carbon\Carbon::parse($tanggal)->format('d/m/Y') }} 
            &nbsp;|&nbsp; Depo/Unit: {{ $nm_bangsal }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="30" class="text-center">No</th>
                <th>Nama Obat</th>
                <th width="80">Satuan</th>
                <th width="80" class="text-right">Stok Sistem</th>
                <th width="80" class="text-right">Stok Fisik</th>
                <th width="80" class="text-right">Selisih</th>
                <th width="80" class="text-center">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
                <tr>
                    <td class="text-center" style="color: #777;">
                        {{ $index + 1 }}
                    </td>
                    <td style="text-transform: uppercase; font-weight: bold;">
                        {{ $item->nama_brng }}
                        <div style="font-size: 7px; color: #777; font-weight: normal; margin-top: 2px;">
                            Kode: {{ $item->kode_brng }} &bull; Unit: {{ $item->nm_bangsal }}
                        </div>
                    </td>
                    <td style="text-transform: uppercase;">
                        {{ $item->satuan }}
                    </td>
                    <td class="text-right">
                        {{ number_format($item->stok_sistem, 0, ',', '.') }}
                    </td>
                    <td class="text-right">
                        {{ number_format($item->stok_fisik, 0, ',', '.') }}
                    </td>
                    @php
                        $selisih = $item->selisih;
                        
                        if ($selisih < 0) {
                            $color = '#DC2626'; // red-600
                            $sign = '';
                            $keterangan = 'KURANG';
                        } elseif ($selisih > 0) {
                            $color = '#16A34A'; // green-600
                            $sign = '+';
                            $keterangan = 'LEBIH';
                        } else {
                            $color = '#4B5563'; // gray-600
                            $sign = '';
                            $keterangan = 'SESUAI';
                        }
                    @endphp
                    <td class="text-right" style="color: {{ $color }}; font-weight: bold;">
                        {{ $sign }}{{ number_format($selisih, 0, ',', '.') }}
                    </td>
                    <td class="text-center" style="color: {{ $color }}; font-weight: bold;">
                        {{ $keterangan }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="font-style: italic; color: #777; padding: 20px;">Tidak ada data stock opname untuk kriteria ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak oleh TARIKSIS Portal pada: {{ date('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
