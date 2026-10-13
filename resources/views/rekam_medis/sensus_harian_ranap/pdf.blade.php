<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sensus Harian Rawat Inap - {{ $bulan }}</title>
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
            text-align: center;
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
        .text-right {
            text-align: right;
        }
        .text-left {
            text-align: left;
        }
        .bold {
            font-weight: bold;
        }
        .section-header td {
            background-color: #e8f5e9;
            font-weight: bold;
            color: #007C3C;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 0.5px;
        }
        .sub-row td:first-child {
            padding-left: 20px;
        }
        .total-row td {
            background-color: #f0f0f0;
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
        <h1 class="title">Sensus Harian Rawat Inap</h1>
        <div class="subtitle">
            Periode: {{ date('F Y', strtotime($bulan . '-01')) }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 40%; text-align: left;">Indikator</th>
                <th style="width: 20%;">Umum</th>
                <th style="width: 20%;">BPJS</th>
                <th style="width: 20%;">Total</th>
            </tr>
        </thead>
        <tbody>
            <!-- Section: Pergerakan Pasien -->
            <tr class="section-header">
                <td colspan="4">Pergerakan Pasien</td>
            </tr>

            <tr>
                <td class="text-left bold">Pasien Awal Bulan</td>
                <td class="text-center">{{ $data['umum']['pasien_awal_bulan'] }}</td>
                <td class="text-center">{{ $data['bpjs']['pasien_awal_bulan'] }}</td>
                <td class="text-center bold">{{ $data['total']['pasien_awal_bulan'] }}</td>
            </tr>
            <tr>
                <td class="text-left bold">Pasien Masuk</td>
                <td class="text-center">{{ $data['umum']['pasien_masuk'] }}</td>
                <td class="text-center">{{ $data['bpjs']['pasien_masuk'] }}</td>
                <td class="text-center bold">{{ $data['total']['pasien_masuk'] }}</td>
            </tr>

            @php
                $subMasuk = [
                    ['label' => '- Via IGD', 'key' => 'pasien_masuk_igd'],
                    ['label' => '- Via Obgyn', 'key' => 'pasien_masuk_obgyn'],
                    ['label' => '- Via Anak', 'key' => 'pasien_masuk_anak'],
                    ['label' => '- Via Bedah', 'key' => 'pasien_masuk_bedah'],
                    ['label' => '- Via Lainnya', 'key' => 'pasien_masuk_lainnya'],
                ];
            @endphp

            @foreach($subMasuk as $sm)
                <tr class="sub-row">
                    <td class="text-left" style="padding-left: 20px; color: #666;">{{ $sm['label'] }}</td>
                    <td class="text-center" style="color: #666;">{{ $data['umum'][$sm['key']] }}</td>
                    <td class="text-center" style="color: #666;">{{ $data['bpjs'][$sm['key']] }}</td>
                    <td class="text-center" style="color: #666;">{{ $data['total'][$sm['key']] }}</td>
                </tr>
            @endforeach

            <tr>
                <td class="text-left bold">Pasien Pindahan</td>
                <td class="text-center">{{ $data['umum']['pasien_pindahan'] }}</td>
                <td class="text-center">{{ $data['bpjs']['pasien_pindahan'] }}</td>
                <td class="text-center bold">{{ $data['total']['pasien_pindahan'] }}</td>
            </tr>
            <tr>
                <td class="text-left bold">Pasien Dipindahkan</td>
                <td class="text-center">{{ $data['umum']['pasien_dipindahkan'] }}</td>
                <td class="text-center">{{ $data['bpjs']['pasien_dipindahkan'] }}</td>
                <td class="text-center bold">{{ $data['total']['pasien_dipindahkan'] }}</td>
            </tr>

            <!-- Section: Pasien Keluar -->
            <tr class="section-header">
                <td colspan="4">Pasien Keluar</td>
            </tr>

            <tr>
                <td class="text-left bold">Pasien Keluar Hidup</td>
                <td class="text-center">{{ $data['umum']['pasien_keluar_hidup'] }}</td>
                <td class="text-center">{{ $data['bpjs']['pasien_keluar_hidup'] }}</td>
                <td class="text-center bold">{{ $data['total']['pasien_keluar_hidup'] }}</td>
            </tr>
            <tr>
                <td class="text-left bold">Pasien Keluar Mati</td>
                <td class="text-center">{{ $data['umum']['pasien_keluar_mati'] }}</td>
                <td class="text-center">{{ $data['bpjs']['pasien_keluar_mati'] }}</td>
                <td class="text-center bold">{{ $data['total']['pasien_keluar_mati'] }}</td>
            </tr>

            @php
                $matiRows = [
                    ['label' => '- Pria Mati ≥48 Jam', 'key' => 'mati_48_pria'],
                    ['label' => '- Wanita Mati ≥48 Jam', 'key' => 'mati_48_wanita'],
                    ['label' => '- Pria Mati <48 Jam', 'key' => 'mati_kurang_48_pria'],
                    ['label' => '- Wanita Mati <48 Jam', 'key' => 'mati_kurang_48_wanita'],
                ];
            @endphp

            @foreach($matiRows as $mr)
                <tr class="sub-row">
                    <td class="text-left" style="padding-left: 20px; color: #666;">{{ $mr['label'] }}</td>
                    <td class="text-center" style="color: #666;">{{ $data['umum'][$mr['key']] }}</td>
                    <td class="text-center" style="color: #666;">{{ $data['bpjs'][$mr['key']] }}</td>
                    <td class="text-center" style="color: #666;">{{ $data['total'][$mr['key']] }}</td>
                </tr>
            @endforeach

            <!-- Section: Hari Perawatan -->
            <tr class="section-header">
                <td colspan="4">Hari Perawatan (per Kelas Kamar)</td>
            </tr>

            @php
                $hpRows = [
                    ['label' => 'HP VVIP', 'key' => 'hp_vvip'],
                    ['label' => 'HP VIP', 'key' => 'hp_vip'],
                    ['label' => 'HP Kelas 1', 'key' => 'hp_kelas_1'],
                    ['label' => 'HP Kelas 2', 'key' => 'hp_kelas_2'],
                    ['label' => 'HP Kelas 3', 'key' => 'hp_kelas_3'],
                    ['label' => 'HP Khusus (Kelas Utama)', 'key' => 'hp_khusus'],
                ];
            @endphp

            @foreach($hpRows as $hp)
                <tr>
                    <td class="text-left">{{ $hp['label'] }}</td>
                    <td class="text-center">{{ $data['umum'][$hp['key']] }}</td>
                    <td class="text-center">{{ $data['bpjs'][$hp['key']] }}</td>
                    <td class="text-center">{{ $data['total'][$hp['key']] }}</td>
                </tr>
            @endforeach

            <tr class="total-row">
                <td class="text-left">TOTAL HARI PERAWATAN</td>
                <td class="text-center">{{ $data['umum']['total_hari_perawatan'] }}</td>
                <td class="text-center">{{ $data['bpjs']['total_hari_perawatan'] }}</td>
                <td class="text-center">{{ $data['total']['total_hari_perawatan'] }}</td>
            </tr>

            <!-- Section: Alokasi TT -->
            <tr class="section-header">
                <td colspan="4">Kapasitas Tempat Tidur</td>
            </tr>

            <tr>
                <td class="text-left bold">Jumlah Alokasi TT Awal Bulan</td>
                <td colspan="3" class="text-center bold" style="font-size: 11px;">{{ $data['alokasi_tt'] }}</td>
            </tr>

            @if(isset($data['alokasi_tt_detail']))
                @foreach($data['alokasi_tt_detail'] as $kelas => $detail)
                    <tr class="sub-row">
                        <td class="text-left" style="padding-left: 20px; color: #666;">{{ $kelas }}</td>
                        <td colspan="3" class="text-center" style="color: #666;">{{ $detail->jumlah }} TT</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ date('d/m/Y H:i:s') }} | Unit Rekam Medis - TARIKSIS
    </div>
</body>
</html>
