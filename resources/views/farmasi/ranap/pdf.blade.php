<!DOCTYPE html>
<html>

<head>
    <title>Laporan Penarikan Data</title>
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

        /* Sub-tabel instruksi */
        .instruksi-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
            font-size: 9px;
        }

        .instruksi-table th {
            background-color: #92400e;
            color: white;
            font-size: 8px;
            padding: 4px 6px;
        }

        .instruksi-table td {
            border: 1px solid #fde68a;
            padding: 3px 6px;
            background-color: #fffbeb;
            font-size: 9px;
        }

        .instruksi-table tr:nth-child(even) td {
            background-color: #fef3c7;
        }

        .no-instruksi {
            color: #aaa;
            font-style: italic;
        }

        .jam-col {
            width: 70px;
            white-space: nowrap;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>TARIKSIS</h1>
        <p>Laporan Penarikan Data Rawat Inap</p>
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
                <th>Terapi</th>
                <th>Diagnosa Utama</th>
                <th>Lama</th>
                <th>Instruksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $row)
                @php
                    $rowInstruksi = $instruksi[$row->no_rawat] ?? collect();
                    $jumlah = $rowInstruksi->count();
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $row->no_rawat }}</td>
                    <td>{{ $row->nm_pasien }}</td>
                    <td>{{ $row->umur }}</td>
                    <td>{{ $row->jk }}</td>
                    <td>{{ $row->prosedur_utama }}</td>
                    <td>{{ $row->diagnosa_utama }}</td>
                    <td>{{ $row->lama }} Hari</td>
                    <td>
                        @if($jumlah > 0)
                            <table class="instruksi-table">
                                <thead>
                                    <tr>
                                        <th class="jam-col">Jam</th>
                                        <th>Instruksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rowInstruksi as $ins)
                                        <tr>
                                            <td class="jam-col">{{ \Carbon\Carbon::parse($ins->jam_rawat)->format('H:i') }}</td>
                                            <td>{{ $ins->instruksi }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <span class="no-instruksi">Tidak ada</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>