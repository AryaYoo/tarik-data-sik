<!DOCTYPE html>
<html>

<head>
    <title>Rekap Pemberian Obat dan BHP</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 5px;
            text-align: left;
        }

        th {
            background: #f2f2f2;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2 style="margin-bottom: 5px;">REKAP PEMBERIAN OBAT DAN BHP</h2>
        <div style="font-size: 12px; font-weight: bold;">UNIT FARMASI</div>
        <p>Periode: {{ $tgl_mulai }} s/d {{ $tgl_selesai }}</p>
    </div>

    @php
        $groupedData = $data->groupBy('barang');
        $no = 1;
    @endphp

    <table>
        <thead>
            <tr>
                <th width="30">No</th>
                <th>Item Obat</th>
                <th width="60">Satuan</th>
                <th>Dokter yang Meresepkan</th>
                <th width="80" style="text-align: center;">Jumlah Obat yang Diresepkan</th>
                <th width="80" style="text-align: center;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($groupedData as $barang => $items)
                @php $rowCount = $items->count(); @endphp
                @foreach($items as $index => $row)
                    <tr>
                        @if($index === 0)
                            <td rowspan="{{ $rowCount }}" style="text-align: center; vertical-align: middle;">{{ $no++ }}</td>
                            <td rowspan="{{ $rowCount }}" style="vertical-align: middle;">{{ $row->barang }}</td>
                            <td rowspan="{{ $rowCount }}" style="text-align: center; vertical-align: middle;">{{ $row->satuan }}
                            </td>
                        @endif
                        <td>{{ $row->dokter }}</td>
                        <td style="text-align: center;">{{ (float) $row->jumlah }}</td>
                        @if($index === 0)
                            <td rowspan="{{ $rowCount }}" style="text-align: center; vertical-align: middle; font-weight: bold;">
                                {{ (float) $items->sum('jumlah') }}
                            </td>
                        @endif
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>

</html>