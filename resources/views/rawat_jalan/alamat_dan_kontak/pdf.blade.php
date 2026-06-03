<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laporan Alamat & Kontak Rawat Jalan</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #007C3C;
            padding-bottom: 10px;
        }
        .title {
            font-size: 16px;
            font-weight: bold;
            color: #007C3C;
            margin: 0;
            text-transform: uppercase;
        }
        .subtitle {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #007C3C;
            color: white;
            font-weight: bold;
            text-align: left;
            padding: 8px;
            border: 1px solid #ddd;
            font-size: 10px;
        }
        td {
            padding: 8px;
            border: 1px solid #ddd;
            font-size: 10px;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            font-size: 9px;
            color: #999;
            text-align: right;
            border-top: 1px solid #eee;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">Laporan Alamat & Kontak Pasien Rawat Jalan</h1>
        <p class="subtitle">
            Periode: {{ request('tgl_mulai') }} s/d {{ request('tgl_selesai') }}
            @if(request('kd_poli'))
                <br>Poliklinik: {{ request('kd_poli') }}
            @endif
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No.</th>
                <th style="width: 15%;">No. RM</th>
                <th style="width: 25%;">Nama Pasien</th>
                <th style="width: 15%;">Poliklinik</th>
                <th style="width: 25%;">Alamat</th>
                <th style="width: 15%;">Nomor HP</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $row)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $row->no_rkm_medis }}</td>
                    <td>{{ $row->nm_pasien }}</td>
                    <td>{{ $row->nm_poli }}</td>
                    <td>{{ $row->alamat }}</td>
                    <td>{{ $row->no_tlp }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">Tidak ada data pada periode ini</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }} oleh {{ auth()->user()->username ?? 'Sistem' }}
    </div>
</body>
</html>
