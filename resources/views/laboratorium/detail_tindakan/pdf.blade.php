<!DOCTYPE html>
<html>

<head>
    <title>Detail Tindakan Laboratorium</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 9px;
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
            padding: 5px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #007C3C;
            color: #ffffff;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
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
            color: #666;
        }

        .code-badge {
            font-family: monospace;
            font-weight: bold;
            color: #007C3C;
        }

        .sub-text {
            font-size: 8px;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2 style="margin-bottom: 4px; font-size: 14px;">DETAIL TINDAKAN LABORATORIUM</h2>
        <div style="font-size: 10px; font-weight: bold;">UNIT LABORATORIUM — RSIA IBI SURABAYA</div>
        <p style="margin-top: 4px;">Periode: {{ \Carbon\Carbon::parse($tgl_mulai)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($tgl_selesai)->format('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="20" class="text-center">No</th>
                <th width="110">No. Rawat / RM / Nama</th>
                <th width="65" class="text-center">Kode Periksa</th>
                <th>Pemeriksaan</th>
                <th width="130">Dokter PJ LAB / Kode / NIP</th>
                <th width="120">Dokter Perujuk / Kode</th>
                <th width="60" class="text-center">Tanggal & Jam</th>
                <th width="70">Cara Bayar</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
                <tr style="{{ $index % 2 == 1 ? 'background-color: #f9fbf9;' : '' }}">
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong style="color: #007C3C;">{{ $item->no_rawat }}</strong><br>
                        RM: <strong>{{ $item->no_rkm_medis }}</strong><br>
                        <strong>{{ strtoupper($item->nm_pasien) }}</strong>
                        <span class="sub-text">({{ strtoupper((string) $item->jk) == 'L' ? 'Laki-laki' : (strtoupper((string) $item->jk) == 'P' ? 'Perempuan' : '-') }})</span>
                    </td>
                    <td class="text-center">
                        <span class="code-badge">{{ $item->kd_jenis_prw }}</span>
                    </td>
                    <td>
                        <strong>{{ $item->nm_perawatan ?: '-' }}</strong>
                    </td>
                    <td>
                        <strong>{{ $item->nm_dokter_pj ?: '-' }}</strong><br>
                        <span class="sub-text">Kode PJ: {{ $item->kd_pj_lab ?: '-' }}</span><br>
                        <span class="sub-text">NIP: {{ $item->nip ?: '-' }}</span>
                    </td>
                    <td>
                        <strong>{{ $item->nm_dokter_perujuk ?: '-' }}</strong><br>
                        <span class="sub-text">Kode Perujuk: {{ $item->kd_dokter_perujuk ?: '-' }}</span>
                    </td>
                    <td class="text-center">
                        {{ \Carbon\Carbon::parse($item->tgl_periksa)->format('d/m/Y') }}<br>
                        <span class="sub-text">{{ $item->jam }}</span>
                    </td>
                    <td>
                        {{ $item->png_jawab }}<br>
                        <span class="sub-text">({{ strtolower($item->status) == 'ralan' ? 'Rawat Jalan' : 'Rawat Inap' }})</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data tindakan laboratorium yang ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }} | TARIKSIS — RSIA IBI Surabaya
    </div>
</body>

</html>
