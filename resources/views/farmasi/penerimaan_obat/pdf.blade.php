<!DOCTYPE html>
<html>

<head>
    <title>Rekap Penerimaan Obat dan BHP Farmasi</title>
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
        <h2 style="margin-bottom: 5px;">REKAP PENERIMAAN OBAT DAN BHP FARMASI</h2>
        <div style="font-size: 12px; font-weight: bold;">UNIT FARMASI</div>
        <p>Periode: {{ $tgl_mulai }} s/d {{ $tgl_selesai }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="30">No</th>
                <th>Nama Barang</th>
                <th width="80">Satuan</th>
                <th width="80" style="text-align: center;">Total Pesanan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $row->barang }}</td>
                    <td>{{ $row->satuan }}</td>
                    <td style="text-align: center;">{{ (float) $row->jumlah }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>

</html>