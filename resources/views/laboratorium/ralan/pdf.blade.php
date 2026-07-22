<!DOCTYPE html>
<html>

<head>
    <title>Waktu Tunggu Hasil Lab - Rawat Jalan</title>
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
            font-weight: bold;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .text-center {
            text-align: center;
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
        <h2 style="margin-bottom: 5px;">WAKTU TUNGGU HASIL LABORATORIUM - RAWAT JALAN</h2>
        <div style="font-size: 12px; font-weight: bold;">UNIT LABORATORIUM</div>
        <p>Periode: {{ \Carbon\Carbon::parse($tgl_mulai)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($tgl_selesai)->format('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="20">No</th>
                <th width="70">Tgl Periksa</th>
                <th>Nama Pasien</th>
                <th width="70">Jenis Bayar</th>
                <th>Pemeriksaan</th>
                <th width="60">No. RM</th>
                <th width="60">Jam Masuk</th>
                <th width="60">Jam Hasil</th>
                <th width="60" class="text-center">Total Waktu</th>
                <th width="70" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tgl_sampel)->format('d/m/Y') }}</td>
                    <td>{{ $item->nm_pasien }}</td>
                    <td>{{ $item->png_jawab }}</td>
                    <td>{{ $item->pemeriksaan }}</td>
                    <td class="text-center">{{ $item->no_rkm_medis }}</td>
                    <td class="text-center">{{ $item->jam_sampel }}</td>
                    <td class="text-center">{{ $item->jam_hasil }}</td>
                    <td class="text-center">
                        @php
                            $parts = explode(':', $item->total_waktu);
                            $hours = (int)($parts[0] ?? 0);
                            $minutes = (int)($parts[1] ?? 0);
                            $seconds = (int)($parts[2] ?? 0);
                            $totalSeconds = ($hours * 3600) + ($minutes * 60) + $seconds;
                            $isTepat = $totalSeconds < 3600;
                        @endphp
                        {{ $hours }} jam {{ $minutes }} menit {{ $seconds }} detik
                    </td>
                    <td class="text-center">
                        @if($isTepat)
                            Tepat Waktu
                        @else
                            <span style="color: red; font-weight: bold;">Tidak Sesuai</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>

</html>
