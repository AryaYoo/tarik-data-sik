<!DOCTYPE html>
<html>
<head>
    <title>Laporan Waktu Tunggu Farmasi</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #007C3C; color: white; text-align: center; }
        .header { text-align: center; margin-bottom: 30px; }
        .text-center { text-align: center; }
        .footer { margin-top: 30px; text-align: right; }
        .summary { margin-top: 20px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN WAKTU TUNGGU FARMASI - RAWAT JALAN</h2>
        <p>Periode: {{ \Carbon\Carbon::parse($tgl_mulai)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($tgl_selesai)->format('d/m/Y') }}</p>
    </div>

    @if($avgSeconds)
        <div class="summary">
            @php
                $h = floor($avgSeconds / 3600);
                $m = floor(($avgSeconds % 3600) / 60);
                $s = $avgSeconds % 60;
            @endphp
            Rata-rata Waktu Tunggu: {{ $h > 0 ? $h.' jam ' : '' }}{{ $m }} menit {{ $s }} detik
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th width="20">No</th>
                <th>Nama Pasien</th>
                <th width="60">No. RM</th>
                <th width="70">Jenis Bayar</th>
                <th width="60">Validasi</th>
                <th width="60">Penyerahan</th>
                <th width="100" class="text-center">Waktu Tunggu</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->nm_pasien }}</td>
                    <td class="text-center">{{ $item->no_rkm_medis }}</td>
                    <td>{{ $item->png_jawab }}</td>
                    <td class="text-center">{{ $item->jam_validasi }}</td>
                    <td class="text-center">{{ $item->jam_penyerahan }}</td>
                    <td class="text-center">
                        @php
                            $parts = explode(':', $item->total_waktu);
                            $hours = (int)$parts[0];
                            $minutes = (int)$parts[1];
                            $seconds = (int)$parts[2];
                            $totalMinutes = ($hours * 60) + $minutes;

                            $color = 'black';
                            if ($hours > 0 || $totalMinutes >= 60) {
                                $color = 'red';
                            } elseif ($totalMinutes >= 30) {
                                $color = '#EAB308'; // Yellow/Orange
                            } else {
                                $color = 'green';
                            }
                        @endphp
                        <span style="color: {{ $color }}; font-weight: bold;">
                            {{ $hours }}j {{ $minutes }}m {{ $seconds }}d
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ date('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
