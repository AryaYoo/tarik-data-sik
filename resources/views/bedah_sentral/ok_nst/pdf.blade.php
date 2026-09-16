<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data OK NST - Bedah Sentral</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 9px; color: #333; background: #fff; }
        .header { text-align: center; border-bottom: 2px solid #007C3C; padding-bottom: 8px; margin-bottom: 10px; }
        .header h1 { font-size: 15px; font-weight: bold; color: #007C3C; text-transform: uppercase; letter-spacing: 0.5px; }
        .header p { font-size: 9px; color: #666; margin-top: 2px; }
        .meta { display: flex; justify-content: space-between; font-size: 8.5px; color: #555; margin-bottom: 8px; }
        .meta span { background: #f5f5f5; padding: 2px 6px; border-radius: 3px; }
        table { width: 100%; border-collapse: collapse; font-size: 8.5px; }
        thead tr { background: #007C3C; color: white; }
        thead th { padding: 6px 5px; text-align: left; font-weight: bold; text-transform: uppercase; letter-spacing: 0.4px; }
        tbody tr:nth-child(even) { background: #f9fafb; }
        tbody tr:nth-child(odd) { background: #fff; }
        tbody td { padding: 5px 5px; border-bottom: 1px solid #eee; vertical-align: top; }
        .badge-icd10 { background: #fef3c7; color: #92400e; padding: 1px 4px; border-radius: 3px; font-weight: bold; font-family: monospace; }
        .badge-icd9 { background: #dbeafe; color: #1e40af; padding: 1px 4px; border-radius: 3px; font-weight: bold; font-family: monospace; }
        .badge-selesai { background: #d1fae5; color: #065f46; padding: 1px 5px; border-radius: 3px; font-weight: bold; }
        .badge-proses { background: #dbeafe; color: #1e40af; padding: 1px 5px; border-radius: 3px; font-weight: bold; }
        .badge-menunggu { background: #fef9c3; color: #713f12; padding: 1px 5px; border-radius: 3px; font-weight: bold; }
        .footer { margin-top: 12px; text-align: right; font-size: 7.5px; color: #aaa; border-top: 1px solid #eee; padding-top: 4px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>&#x2665; Data OK NST &mdash; Bedah Sentral</h1>
        <p>Periode: {{ $tgl_mulai }} s/d {{ $tgl_selesai }}
            @if(!empty($jenis_nst) && $jenis_nst !== 'all') | Filter NST: {{ $jenis_nst }}@endif
            @if(!empty($status_operasi)) | Status: {{ $status_operasi }}@endif
        </p>
    </div>

    <div class="meta">
        <span>Total Data: {{ count($data) }} baris</span>
        <span>Dicetak: {{ now()->format('d/m/Y H:i:s') }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:3%">No</th>
                <th style="width:8%">No. RM</th>
                <th style="width:16%">Nama Pasien</th>
                <th style="width:12%">No. Rawat</th>
                <th style="width:8%">Tipe</th>
                <th style="width:18%">Diagnosa / Prosedur NST</th>
                <th style="width:8%">Tgl Operasi</th>
                <th style="width:5%">Jam</th>
                <th style="width:14%">Paket OK</th>
                <th style="width:8%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $i => $row)
            <tr>
                <td style="text-align:center;color:#999;font-weight:bold">{{ $i + 1 }}</td>
                <td style="font-family:monospace;font-weight:bold;color:#555">{{ $row->no_rkm_medis }}</td>
                <td style="font-weight:bold;text-transform:uppercase">{{ $row->nm_pasien }}</td>
                <td style="font-family:monospace;font-size:8px">{{ $row->no_rawat }}</td>
                <td>
                    @if(($row->tipe_nst ?? '') === 'ICD-9 CM (Prosedur)')
                        <span class="badge-icd9">ICD-9</span>
                    @else
                        <span class="badge-icd10">ICD-10</span>
                    @endif
                </td>
                <td>
                    <span style="font-family:monospace;font-weight:bold">{{ $row->kode_diagnosa }}</span> &mdash;
                    <span>{{ $row->nama_diagnosa }}</span>
                    @if(isset($row->prioritas) && $row->prioritas == 1)
                        <div style="color:#007C3C;font-size:7px;font-weight:bold;margin-top:1px">DIAGNOSA UTAMA</div>
                    @endif
                </td>
                <td>{{ \Carbon\Carbon::parse($row->tgl_operasi)->format('d/m/Y') }}</td>
                <td style="font-family:monospace;font-size:7.5px">
                    {{ substr($row->jam_mulai, 0, 5) }}<br>{{ substr($row->jam_selesai, 0, 5) }}
                </td>
                <td style="font-size:8px">{{ $row->nama_paket ?: '-' }}</td>
                <td style="text-align:center">
                    @if($row->status_operasi == 'Selesai')
                        <span class="badge-selesai">Selesai</span>
                    @elseif($row->status_operasi == 'Proses Operasi')
                        <span class="badge-proses">Proses</span>
                    @elseif($row->status_operasi == 'Menunggu')
                        <span class="badge-menunggu">Menunggu</span>
                    @else
                        -
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" style="text-align:center;padding:20px;color:#aaa;font-style:italic">
                    Tidak ada data OK NST pada periode ini
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        TARIKSIS &copy; 2026 &mdash; RSIA IBI Surabaya (Bedah Sentral - Pemantauan NST)
    </div>
</body>
</html>
