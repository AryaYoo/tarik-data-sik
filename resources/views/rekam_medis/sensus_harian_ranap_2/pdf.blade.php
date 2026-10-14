<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sensus Harian Rawat Inap 2 - {{ $bulan }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8px;
            color: #333;
            margin: 0;
            padding: 5px;
        }
        .header {
            text-align: center;
            margin-bottom: 12px;
            border-bottom: 2px solid #007C3C;
            padding-bottom: 8px;
        }
        .title {
            font-size: 13px;
            font-weight: bold;
            color: #007C3C;
            margin: 0;
            text-transform: uppercase;
        }
        .subtitle {
            font-size: 9px;
            color: #666;
            margin-top: 3px;
        }
        .summary-box {
            margin-bottom: 12px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 6px 10px;
            font-size: 8px;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary-table td {
            border: none;
            padding: 2px 4px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        table.data-table th {
            background-color: #007C3C;
            color: white;
            font-weight: bold;
            text-align: center;
            padding: 4px 3px;
            border: 1px solid #ccc;
            font-size: 7.5px;
        }
        table.data-table td {
            padding: 3.5px 3px;
            border: 1px solid #ddd;
            font-size: 7.5px;
        }
        table.data-table tr:nth-child(even) {
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
        .footer-total td {
            background-color: #1f2937 !important;
            color: white !important;
            font-weight: bold;
        }
        .footer-info {
            margin-top: 15px;
            font-size: 7.5px;
            color: #888;
            text-align: right;
            border-top: 1px solid #eee;
            padding-top: 4px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">Sensus Harian Rawat Inap 2 (Rincian per Pasien)</h1>
        <div class="subtitle">
            Periode: {{ date('F Y', strtotime($bulan . '-01')) }}
            @if(!empty($data['kd_bangsal']))
                | Filter Bangsal: {{ $data['kd_bangsal'] }}
            @endif
            @if(!empty($data['jenis_pelayanan']))
                | Pelayanan: {{ $data['jenis_pelayanan'] }}
            @endif
        </div>
    </div>

    <!-- Summary Box -->
    <div class="summary-box">
        <table class="summary-table">
            <tr>
                <td><strong>Alokasi TT Awal:</strong> {{ $data['alokasi_tt_total'] ?? 0 }} TT</td>
                <td><strong>Total Pasien:</strong> {{ count($data['patients'] ?? []) }}</td>
                <td><strong>Umum:</strong> {{ $data['summary']['umum_count'] ?? 0 }}</td>
                <td><strong>BPJS:</strong> {{ $data['summary']['bpjs_count'] ?? 0 }}</td>
                <td><strong>Awal Bulan:</strong> {{ $data['summary']['pasien_awal_bulan'] ?? 0 }}</td>
                <td><strong>Pasien Masuk:</strong> {{ $data['summary']['pasien_masuk'] ?? 0 }} (IGD: {{ $data['summary']['masuk_igd'] ?? 0 }}, Obg: {{ $data['summary']['masuk_obgyn'] ?? 0 }}, Anak: {{ $data['summary']['masuk_anak'] ?? 0 }}, Bedah: {{ $data['summary']['masuk_bedah'] ?? 0 }})</td>
            </tr>
            <tr>
                <td><strong>Pindahan:</strong> {{ $data['summary']['pasien_pindahan'] ?? 0 }}</td>
                <td><strong>Dipindahkan:</strong> {{ $data['summary']['pasien_dipindahkan'] ?? 0 }}</td>
                <td><strong>Keluar Hidup:</strong> {{ $data['summary']['pasien_keluar_hidup'] ?? 0 }}</td>
                <td><strong>Mati ≥48j (L):</strong> {{ $data['summary']['mati_48_pria'] ?? 0 }}</td>
                <td><strong>Mati ≥48j (P):</strong> {{ $data['summary']['mati_48_wanita'] ?? 0 }}</td>
                <td><strong>Total Hari Rawat:</strong> {{ $data['summary']['total_hp_all'] ?? 0 }} Hari (VVIP: {{ $data['summary']['total_hp_vvip'] ?? 0 }}, VIP: {{ $data['summary']['total_hp_vip'] ?? 0 }}, K1: {{ $data['summary']['total_hp_kelas_1'] ?? 0 }}, K2: {{ $data['summary']['total_hp_kelas_2'] ?? 0 }}, K3: {{ $data['summary']['total_hp_kelas_3'] ?? 0 }}, Khusus: {{ $data['summary']['total_hp_khusus'] ?? 0 }})</td>
            </tr>
        </table>
    </div>

    <!-- Patient Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 2%;">No</th>
                <th style="width: 10%;">No. Rawat / RM</th>
                <th style="width: 12%;">Nama Pasien</th>
                <th style="width: 2%;">L/P</th>
                <th style="width: 9%;">Ruang / Kamar</th>
                <th style="width: 5%;">Jenis</th>
                <th style="width: 4%;">Awal Bln</th>
                <th style="width: 5%;">Masuk</th>
                <th style="width: 4%;">Pindah</th>
                <th style="width: 4%;">Dipindah</th>
                <th style="width: 4%;">Keluar Hidup</th>
                <th style="width: 4%;">Mati ≥48 L</th>
                <th style="width: 4%;">Mati ≥48 P</th>
                <th style="width: 3%;">VVIP</th>
                <th style="width: 3%;">VIP</th>
                <th style="width: 3%;">K1</th>
                <th style="width: 3%;">K2</th>
                <th style="width: 3%;">K3</th>
                <th style="width: 3%;">Khusus</th>
                <th style="width: 4%;">Total HP</th>
                <th style="width: 5%;">Alokasi TT</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data['patients'] ?? [] as $idx => $p)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>
                        <strong>{{ $p['no_rawat'] }}</strong><br>
                        <span style="color:#777;">RM: {{ $p['no_rkm_medis'] }}</span>
                    </td>
                    <td>
                        <strong>{{ $p['nm_pasien'] }}</strong><br>
                        <span style="color:#777; font-size:7px;">{{ $p['png_jawab'] }}</span>
                    </td>
                    <td class="text-center bold">{{ $p['jk'] }}</td>
                    <td>{{ $p['nm_bangsal'] ?? '-' }} ({{ $p['kd_kamar'] ?? '-' }})</td>
                    <td class="text-center bold">{{ $p['jenis_pelayanan'] }}</td>
                    <td class="text-center">{{ $p['pasien_awal_bulan'] }}</td>
                    <td class="text-center">{{ $p['pasien_masuk'] }}</td>
                    <td class="text-center">{{ $p['pasien_pindahan'] }}</td>
                    <td class="text-center">{{ $p['pasien_dipindahkan'] }}</td>
                    <td class="text-center">{{ $p['pasien_keluar_hidup'] }}</td>
                    <td class="text-center">{{ $p['mati_48_pria'] }}</td>
                    <td class="text-center">{{ $p['mati_48_wanita'] }}</td>
                    <td class="text-center">{{ $p['hp_vvip'] }}</td>
                    <td class="text-center">{{ $p['hp_vip'] }}</td>
                    <td class="text-center">{{ $p['hp_kelas_1'] }}</td>
                    <td class="text-center">{{ $p['hp_kelas_2'] }}</td>
                    <td class="text-center">{{ $p['hp_kelas_3'] }}</td>
                    <td class="text-center">{{ $p['hp_khusus'] }}</td>
                    <td class="text-center bold" style="background-color:#e0f2fe;">{{ $p['total_hp'] }}</td>
                    <td class="text-center">{{ $p['alokasi_tt'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="21" class="text-center" style="padding: 15px;">Tidak ada data pasien yang sesuai filter.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="footer-total">
                <td class="text-center">TOTAL</td>
                <td colspan="4">{{ count($data['patients'] ?? []) }} Pasien Rawat Inap</td>
                <td class="text-center">U:{{ $data['summary']['umum_count'] ?? 0 }} | B:{{ $data['summary']['bpjs_count'] ?? 0 }}</td>
                <td class="text-center">{{ $data['summary']['pasien_awal_bulan'] ?? 0 }}</td>
                <td class="text-center">{{ $data['summary']['pasien_masuk'] ?? 0 }}</td>
                <td class="text-center">{{ $data['summary']['pasien_pindahan'] ?? 0 }}</td>
                <td class="text-center">{{ $data['summary']['pasien_dipindahkan'] ?? 0 }}</td>
                <td class="text-center">{{ $data['summary']['pasien_keluar_hidup'] ?? 0 }}</td>
                <td class="text-center">{{ $data['summary']['mati_48_pria'] ?? 0 }}</td>
                <td class="text-center">{{ $data['summary']['mati_48_wanita'] ?? 0 }}</td>
                <td class="text-center">{{ $data['summary']['total_hp_vvip'] ?? 0 }}</td>
                <td class="text-center">{{ $data['summary']['total_hp_vip'] ?? 0 }}</td>
                <td class="text-center">{{ $data['summary']['total_hp_kelas_1'] ?? 0 }}</td>
                <td class="text-center">{{ $data['summary']['total_hp_kelas_2'] ?? 0 }}</td>
                <td class="text-center">{{ $data['summary']['total_hp_kelas_3'] ?? 0 }}</td>
                <td class="text-center">{{ $data['summary']['total_hp_khusus'] ?? 0 }}</td>
                <td class="text-center" style="background-color: #047857 !important;">{{ $data['summary']['total_hp_all'] ?? 0 }}</td>
                <td class="text-center">TT RS: {{ $data['alokasi_tt_total'] ?? 0 }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer-info">
        Dicetak pada: {{ now()->translatedFormat('d F Y H:i:s') }} | Sistem Penarikan Data SIMRS (TARIKSIS)
    </div>
</body>
</html>
