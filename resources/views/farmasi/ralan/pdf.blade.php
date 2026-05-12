<!DOCTYPE html>
<html>

<head>
    <title>Laporan Penarikan Data Rawat Jalan</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #007C3C;
            color: white;
            text-transform: uppercase;
            font-size: 9px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            color: #007C3C;
            font-size: 24px;
        }

        .header p {
            margin: 5px 0;
            color: #666;
        }

        /* Sub-tabel obat */
        .obat-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
            font-size: 9px;
        }

        .obat-table th {
            background-color: #1e40af;
            color: white;
            font-size: 8px;
            padding: 4px 6px;
        }

        .obat-table td {
            border: 1px solid #bfdbfe;
            padding: 3px 6px;
            background-color: #f0f9ff;
            font-size: 9px;
        }

        .obat-table tr:nth-child(even) td {
            background-color: #e0f2fe;
        }

        .no-data {
            color: #aaa;
            font-style: italic;
        }

        .jml-col {
            width: 50px;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>TARIKSIS</h1>
        <p>Laporan Penarikan Data Rawat Jalan</p>
        <p>Periode: {{ request('tgl_mulai') }} s/d {{ request('tgl_selesai') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No. Rawat</th>
                <th>Nama Pasien</th>
                <th>Usia</th>
                <th>JK</th>
                <th>Detail Obat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $row)
                @php
                    $rowObat = $obat[$row->no_rawat] ?? collect();
                    $jumlah = $rowObat->count();
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $row->no_rawat }}</td>
                    <td>{{ $row->nm_pasien }}</td>
                    <td>{{ $row->umur }}</td>
                    <td>{{ $row->jk }}</td>
                    <td>
                        @if($jumlah > 0)
                            <table class="obat-table">
                                <thead>
                                    <tr>
                                        <th>Nama Obat</th>
                                        <th class="jml-col">Jml</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rowObat as $item)
                                        <tr>
                                            <td>{{ $item->nama_brng }}</td>
                                            <td class="jml-col">{{ (float) $item->jml }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <span class="no-data">Tidak ada</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>