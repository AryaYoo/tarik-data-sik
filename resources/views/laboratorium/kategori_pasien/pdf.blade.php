<!DOCTYPE html>
<html>

<head>
    <title>Kategori Pasien Laboratorium</title>
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
            background: #007C3C;
            color: #ffffff;
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
            color: #666;
        }

        .summary-box {
            display: inline-block;
            border: 1px solid #ccc;
            padding: 4px 10px;
            border-radius: 4px;
            margin: 2px;
            font-size: 9px;
        }

        /* Label kategori */
        .kat-neonatus { background: #EDE9FE; color: #6D28D9; font-weight: bold; padding: 2px 6px; border-radius: 4px; }
        .kat-bayi     { background: #DBEAFE; color: #1D4ED8; font-weight: bold; padding: 2px 6px; border-radius: 4px; }
        .kat-anak     { background: #FEF3C7; color: #B45309; font-weight: bold; padding: 2px 6px; border-radius: 4px; }
        .kat-dewasa   { background: #D1FAE5; color: #065F46; font-weight: bold; padding: 2px 6px; border-radius: 4px; }

        tr:nth-child(even) { background: #f9f9f9; }
    </style>
</head>

<body>
    <div class="header">
        <h2 style="margin-bottom: 4px;">KATEGORI PASIEN LABORATORIUM</h2>
        <div style="font-size: 11px; font-weight: bold;">UNIT LABORATORIUM — RSIA IBI SURABAYA</div>
        <p>Periode: {{ \Carbon\Carbon::parse($tgl_mulai)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($tgl_selesai)->format('d/m/Y') }}</p>
    </div>

    {{-- Summary --}}
    @if(!empty($summary))
    <div style="margin-bottom: 10px; text-align: center;">
        <span class="summary-box">Total: <strong>{{ $summary['total'] }}</strong></span>
        <span class="summary-box" style="background:#EDE9FE; color:#6D28D9;">Neonatus: <strong>{{ $summary['neonatus'] }}</strong></span>
        <span class="summary-box" style="background:#DBEAFE; color:#1D4ED8;">Bayi: <strong>{{ $summary['bayi'] }}</strong></span>
        <span class="summary-box" style="background:#FEF3C7; color:#B45309;">Anak: <strong>{{ $summary['anak'] }}</strong></span>
        <span class="summary-box" style="background:#D1FAE5; color:#065F46;">Dewasa: <strong>{{ $summary['dewasa'] }}</strong></span>
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th width="20" class="text-center">No</th>
                <th width="65">Tgl. Periksa</th>
                <th width="55">No. RM</th>
                <th>Nama Pasien</th>
                <th width="60">Tgl. Lahir</th>
                <th width="50" class="text-center">Umur</th>
                <th width="60" class="text-center">Kategori</th>
                <th>Jenis Pemeriksaan</th>
                <th width="70">Jenis Bayar</th>
                <th width="55" class="text-center">Kunjungan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
                @php
                    $umurTahun      = (int) $item->umur_tahun;
                    $umurBulanTotal = (int) $item->umur_bulan_total;
                    $bulanSisa      = $umurBulanTotal % 12;

                    if ($umurTahun >= 1) {
                        $umurTeks = $umurTahun . ' Th ' . $bulanSisa . ' Bln';
                    } elseif ($umurBulanTotal >= 1) {
                        $umurTeks = $umurBulanTotal . ' Bln';
                    } else {
                        $hari     = \Carbon\Carbon::parse($item->tgl_lahir)->diffInDays(\Carbon\Carbon::parse($item->tgl_sampel));
                        $umurTeks = $hari . ' Hr';
                    }

                    $katClass = match($item->kategori_usia) {
                        'Neonatus' => 'kat-neonatus',
                        'Bayi'     => 'kat-bayi',
                        'Anak'     => 'kat-anak',
                        default    => 'kat-dewasa',
                    };
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tgl_sampel)->format('d/m/Y') }}</td>
                    <td class="text-center">{{ $item->no_rkm_medis }}</td>
                    <td>{{ strtoupper($item->nm_pasien) }}</td>
                    <td>{{ $item->tgl_lahir ? \Carbon\Carbon::parse($item->tgl_lahir)->format('d/m/Y') : '-' }}</td>
                    <td class="text-center">{{ $umurTeks }}</td>
                    <td class="text-center"><span class="{{ $katClass }}">{{ $item->kategori_usia }}</span></td>
                    <td style="font-size:9px;">{{ $item->pemeriksaan ?: '-' }}</td>
                    <td>{{ $item->png_jawab }}</td>
                    <td class="text-center">{{ $item->status == 'ralan' ? 'Ralan' : 'Ranap' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }} | TARIKSIS — RSIA IBI Surabaya
    </div>
</body>

</html>
