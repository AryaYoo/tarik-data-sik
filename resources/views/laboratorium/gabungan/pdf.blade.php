<!DOCTYPE html>
<html>

<head>
    <title>Data Lama Pelayanan Laboratorium Patologi Klinis</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 8.5px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 4px 5px;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background: #007C3C;
            color: #ffffff;
            font-weight: bold;
            font-size: 8.5px;
            text-transform: uppercase;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8px;
            color: #666;
        }

        .summary-table {
            margin-top: 15px;
            width: 100%;
            border-collapse: collapse;
        }

        .summary-table td {
            padding: 3px 6px;
        }

        .bg-gray {
            background-color: #f2f2f2;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2 style="margin-bottom: 4px; font-size: 13px; text-transform: uppercase;">DATA LAMA PELAYANAN LABORATORIUM PATOLOGI KLINIS</h2>
        <div style="font-size: 10px; font-weight: bold;">UNIT LABORATORIUM — RSIA IBI SURABAYA</div>
        <p style="margin-top: 4px;">Periode: {{ \Carbon\Carbon::parse($tgl_mulai)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($tgl_selesai)->format('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="18" class="text-center">No.</th>
                <th width="40" class="text-center">No.RM</th>
                <th>Nama Pasien</th>
                <th>Dokter Perujuk</th>
                <th width="65" class="text-center">No.Order</th>
                <th width="55" class="text-center">Kode Periksa</th>
                <th>Pemeriksaan</th>
                <th width="80" class="text-center">Jam Permintaan</th>
                <th width="80" class="text-center">Jam Sampel</th>
                <th width="80" class="text-center">Jam Hasil</th>
                <th width="55" class="text-right">Permintaan-Sampel(m)</th>
                <th width="55" class="text-right">Sampel-Hasil(m)</th>
                <th width="55" class="text-right">Permintaan-Hasil(m)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
                @php
                    $tPermintaan = !empty($item->jam_permintaan_full) ? strtotime($item->jam_permintaan_full) : null;
                    $tSampel     = !empty($item->jam_sampel_full) ? strtotime($item->jam_sampel_full) : null;
                    $tHasil      = !empty($item->jam_hasil_full) ? strtotime($item->jam_hasil_full) : null;

                    $psMin = ($tSampel && $tPermintaan) ? number_format(($tSampel - $tPermintaan) / 60, 2) : '0.00';
                    $shMin = ($tHasil && $tSampel) ? number_format(($tHasil - $tSampel) / 60, 2) : '0.00';
                    $phMin = ($tHasil && $tPermintaan) ? number_format(($tHasil - $tPermintaan) / 60, 2) : '0.00';
                @endphp
                <tr style="{{ $index % 2 == 1 ? 'background-color: #f9fbf9;' : '' }}">
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $item->no_rkm_medis }}</td>
                    <td><strong>{{ strtoupper($item->nm_pasien) }}</strong></td>
                    <td>{{ $item->nm_dokter_perujuk ?: '-' }}</td>
                    <td class="text-center" style="font-family: monospace;">{{ $item->noorder }}</td>
                    <td class="text-center" style="font-family: monospace;">{{ $item->kd_jenis_prw ?: '-' }}</td>
                    <td>{{ $item->pemeriksaan ?: '-' }}</td>
                    <td class="text-center">{{ $item->jam_permintaan_full ?: '-' }}</td>
                    <td class="text-center">{{ $item->jam_sampel_full ?: '-' }}</td>
                    <td class="text-center">{{ $item->jam_hasil_full ?: '-' }}</td>
                    <td class="text-right">{{ $psMin }}</td>
                    <td class="text-right">{{ $shMin }}</td>
                    <td class="text-right">{{ $phMin }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="13" class="text-center">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Summary Table (Rata-rata & Interval) --}}
    @if(!empty($summary))
    <table class="summary-table">
        <tr class="bg-gray">
            <td width="200"><strong>Rata-rata (Menit)</strong></td>
            <td width="10" class="text-center">:</td>
            <td class="text-right" width="100"><strong>{{ number_format($summary['ps_avg'] ?? 0, 2) }}</strong></td>
            <td class="text-right" width="100"><strong>{{ number_format($summary['sh_avg'] ?? 0, 2) }}</strong></td>
            <td class="text-right" width="100"><strong>{{ number_format($summary['ph_avg'] ?? 0, 2) }}</strong></td>
            <td></td>
        </tr>
        @foreach($summary['ranges'] ?? [] as $r)
        <tr>
            <td>{{ $r['label'] }}</td>
            <td class="text-center">:</td>
            <td class="text-right">{{ $r['ps'] }}</td>
            <td class="text-right">{{ $r['sh'] }}</td>
            <td class="text-right">{{ $r['ph'] }}</td>
            <td></td>
        </tr>
        @endforeach
    </table>
    @endif

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }} | TARIKSIS — RSIA IBI Surabaya
    </div>
</body>

</html>
