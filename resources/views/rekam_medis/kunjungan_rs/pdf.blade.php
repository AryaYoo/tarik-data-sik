<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Kunjungan Pasien RS</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #007C3C;
            padding-bottom: 10px;
        }
        .title {
            font-size: 14px;
            font-weight: bold;
            color: #007C3C;
            margin: 0;
            text-transform: uppercase;
        }
        .subtitle {
            font-size: 10px;
            color: #666;
            margin-top: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th {
            background-color: #007C3C;
            color: white;
            font-weight: bold;
            text-align: left;
            padding: 6px 5px;
            border: 1px solid #ddd;
            font-size: 9px;
        }
        td {
            padding: 5px;
            border: 1px solid #ddd;
            font-size: 8.5px;
        }
        tr:nth-child(even) {
            background-color: #fcfcfc;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: 2px 4px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }
        .footer {
            margin-top: 20px;
            font-size: 8px;
            color: #999;
            text-align: right;
            border-top: 1px solid #eee;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">Laporan Data Kunjungan Pasien RS</h1>
        <div class="subtitle">
            Periode: {{ date('d/m/Y', strtotime($tgl_mulai)) }} s/d {{ date('d/m/Y', strtotime($tgl_selesai)) }}
            @if(!empty($status_lanjut))
                | Jenis Rawat: {{ $status_lanjut == 'Ralan' ? 'Rawat Jalan' : 'Rawat Inap' }}
            @endif
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 3%;" class="text-center">No</th>
                <th style="width: 12%;">No. Rawat</th>
                <th style="width: 7%;">Tgl Reg</th>
                <th style="width: 6%;">Jam</th>
                <th style="width: 7%;">No. RM</th>
                <th style="width: 15%;">Pasien</th>
                <th style="width: 3%;" class="text-center">JK</th>
                <th style="width: 12%;">Poliklinik</th>
                <th style="width: 15%;">Dokter</th>
                <th style="width: 12%;">Penjamin</th>
                <th style="width: 8%;" class="text-center">Jns Kunjungan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><strong>{{ $row->no_rawat }}</strong></td>
                    <td>{{ date('d/m/Y', strtotime($row->tgl_registrasi)) }}</td>
                    <td>{{ $row->jam_reg }}</td>
                    <td>{{ $row->no_rkm_medis }}</td>
                    <td><strong>{{ $row->nm_pasien }}</strong></td>
                    <td class="text-center">{{ $row->jk }}</td>
                    <td>{{ $row->nm_poli ?: '-' }}</td>
                    <td>{{ $row->nm_dokter ?: '-' }}</td>
                    <td>{{ $row->png_jawab ?: '-' }}</td>
                    <td class="text-center">{{ $row->jns_kunjungan ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center" style="padding: 20px;">Tidak ada data kunjungan pada periode yang dipilih.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ date('d/m/Y H:i:s') }} | Unit Rekam Medis - TARIKSIS
    </div>
</body>
</html>
