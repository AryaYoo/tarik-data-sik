<!DOCTYPE html>
<html>
<head>
    <title>Laporan Harga Barang Farmasi</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #007C3C; color: white; text-align: center; text-transform: uppercase; font-size: 9px; }
        .header { text-align: center; margin-bottom: 20px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .footer { margin-top: 20px; text-align: right; font-size: 8px; color: #777; }
        .status-active { color: green; font-weight: bold; }
        .status-inactive { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN HARGA BARANG FARMASI</h2>
        <p>Tanggal Cetak: {{ date('d/m/Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="30">No</th>
                <th width="80">Kode Barang</th>
                <th>Nama Barang</th>
                <th width="60">Satuan</th>
                @foreach($selectedColumns as $col)
                    @if(isset($columnMap[$col]))
                        <th>{{ $columnMap[$col] }}</th>
                    @endif
                @endforeach
                <th width="50">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->kode_brng }}</td>
                    <td style="text-transform: uppercase;">{{ $item->nama_brng }}</td>
                    <td class="text-center">{{ $item->satuan ?? '-' }}</td>
                    @foreach($selectedColumns as $col)
                        <td class="text-right">
                            Rp{{ number_format($item->$col ?? 0, 0, ',', '.') }}
                        </td>
                    @endforeach
                    <td class="text-center">
                        @if($item->status == '1')
                            <span class="status-active">Aktif</span>
                        @else
                            <span class="status-inactive">Non-Aktif</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($selectedColumns) + 5 }}" class="text-center">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak dari Sistem TARIKSIS &copy; {{ date('Y') }}</p>
    </div>
</body>
</html>
