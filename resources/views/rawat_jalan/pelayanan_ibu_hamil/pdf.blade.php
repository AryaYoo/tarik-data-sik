<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pelayanan Ibu Hamil (ANC)</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8.5px;
            color: #333;
            line-height: 1.2;
        }
        .header {
            text-align: center;
            margin-bottom: 12px;
            border-bottom: 2px solid #007C3C;
            padding-bottom: 8px;
        }
        .title {
            font-size: 14px;
            font-weight: bold;
            color: #007C3C;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .subtitle {
            font-size: 10px;
            color: #555;
            margin-top: 3px;
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
            padding: 5px 4px;
            border: 1px solid #999;
            font-size: 8px;
            vertical-align: middle;
        }
        th.kmk-bmk {
            background-color: #fef08a;
            color: #854d0e;
        }
        td {
            padding: 4px;
            border: 1px solid #ccc;
            font-size: 8px;
            vertical-align: middle;
        }
        tr:nth-child(even) {
            background-color: #fbfbfb;
        }
        .text-center {
            text-align: center;
        }
        .text-bold {
            font-weight: bold;
        }
        .footer {
            margin-top: 15px;
            font-size: 8px;
            color: #777;
            text-align: right;
            border-top: 1px solid #eee;
            padding-top: 4px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">Laporan Pelayanan Ibu Hamil (ANC) Rawat Jalan</h1>
        <p class="subtitle">
            Periode: {{ request('tgl_mulai') }} s/d {{ request('tgl_selesai') }}
            @if(request('kd_poli'))
                | Poliklinik: {{ request('kd_poli') }}
            @endif
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 3%;">NO</th>
                <th style="width: 12%;">NAMA PASIEN ( Ny. )</th>
                <th style="width: 10%;">NIK IBU HAMIL</th>
                <th style="width: 10%;">NAMA SUAMI ( Tn. )</th>
                <th style="width: 15%;">ALAMAT SESUAI KTP DAN DOMISILI</th>
                <th style="width: 5%;">UMUR IBU ( Th )</th>
                <th style="width: 6%;">HPHT</th>
                <th style="width: 7%;">USIA KEHAMILAN</th>
                <th class="kmk-bmk" style="width: 6%;">Kecil Masa Kehamilan (cm)</th>
                <th class="kmk-bmk" style="width: 6%;">Besar Masa Kehamilan</th>
                <th style="width: 13%;">DIAGNOSA ( G..P..A.. )</th>
                <th style="width: 7%;">LiLA (cm)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $row)
                @php
                    // 1. Nama Pasien
                    $namaPasien = trim($row->nm_pasien ?? '');
                    if ($namaPasien !== '' && !preg_match('/^(ny\.|ny\s)/i', $namaPasien)) {
                        $namaPasien = 'NY. ' . strtoupper($namaPasien);
                    } else {
                        $namaPasien = strtoupper($namaPasien);
                    }

                    // 2. Nama Suami (Hanya ambil jika hubungan keluarga atau penanggung jawab benar-benar SUAMI)
                    $namaSuami = '-';
                    if (!empty($row->keluarga) && strtoupper(trim($row->keluarga)) === 'SUAMI' && !empty($row->namakeluarga)) {
                        $namaSuami = trim($row->namakeluarga);
                    } elseif (!empty($row->hubunganpj) && strtoupper(trim($row->hubunganpj)) === 'SUAMI' && !empty($row->p_jawab)) {
                        $namaSuami = trim($row->p_jawab);
                    }
                    if ($namaSuami !== '-' && !preg_match('/^(tn\.|tn\s)/i', $namaSuami)) {
                        $namaSuami = 'TN. ' . strtoupper($namaSuami);
                    } elseif ($namaSuami !== '-') {
                        $namaSuami = strtoupper($namaSuami);
                    }

                    // 3. Alamat Lengkap
                    $alamatParts = array_filter([
                        $row->alamat,
                        $row->nm_kel ? 'Kel. ' . $row->nm_kel : null,
                        $row->nm_kec ? 'Kec. ' . $row->nm_kec : null,
                        $row->nm_kab ? $row->nm_kab : null,
                    ]);
                    $alamatLengkap = !empty($alamatParts) ? implode(', ', $alamatParts) : ($row->alamat ?: '-');

                    // 4. Umur
                    $umur = '-';
                    if (!empty($row->umurdaftar)) {
                        $umur = $row->umurdaftar . ' TH';
                    } elseif (!empty($row->tgl_lahir)) {
                        $umur = \Carbon\Carbon::parse($row->tgl_lahir)->age . ' TH';
                    }

                    // 5. HPHT
                    $hpht = '-';
                    if (!empty($row->hpht) && $row->hpht !== '0000-00-00') {
                        $hpht = \Carbon\Carbon::parse($row->hpht)->format('m/d/Y');
                    }

                    // 6. Usia Kehamilan
                    $uk = '-';
                    if (!empty($row->usia_kehamilan)) {
                        $uk = trim($row->usia_kehamilan);
                        if (!preg_match('/mgg|minggu/i', $uk)) {
                            $uk .= ' MGG';
                        }
                    } elseif (!empty($row->hpht) && $row->hpht !== '0000-00-00') {
                        $tglDaftar = \Carbon\Carbon::parse($row->tgl_registrasi);
                        $tglHpht = \Carbon\Carbon::parse($row->hpht);
                        $diffWeeks = floor($tglHpht->diffInDays($tglDaftar) / 7);
                        if ($diffWeeks > 0 && $diffWeeks <= 45) {
                            $uk = $diffWeeks . ' MGG';
                        }
                    }

                    // 7. TFU (Kecil / Besar Masa Kehamilan)
                    $tfuRaw = $row->tfu_kebidanan ?: ($row->tfu_dokter ?: null);
                    $kmk = '-';
                    $bmk = '-';
                    if ($tfuRaw) {
                        $cleanTfu = preg_replace('/[^0-9.]/', '', $tfuRaw);
                        if ($cleanTfu !== '') {
                            $tfuValue = $cleanTfu . ' CM';
                            preg_match('/(\d+)/', $uk, $matchesUk);
                            $numericUk = !empty($matchesUk[1]) ? floatval($matchesUk[1]) : null;
                            $numericTfu = floatval($cleanTfu);

                            if ($numericUk && $numericTfu < ($numericUk - 3)) {
                                $kmk = $tfuValue;
                            } else {
                                $bmk = $tfuValue;
                            }
                        } else {
                            $bmk = $tfuRaw;
                        }
                    }

                    // 8. Diagnosa GPA
                    $diagnosa = '-';
                    $hasGpa = ($row->g !== null && $row->g !== '') || ($row->p !== null && $row->p !== '') || ($row->a !== null && $row->a !== '');
                    if ($hasGpa) {
                        $diagnosa = 'G' . ($row->g ?? '0') . 'P' . ($row->p ?? '0') . 'A' . ($row->a ?? '0');
                        if ($uk !== '-') {
                            $diagnosa .= ' UK ' . $uk;
                        }
                    } elseif (!empty($row->diagnosa_dokter)) {
                        $diagnosa = $row->diagnosa_dokter;
                    }

                    // 9. LiLA
                    $lila = '-';
                    if (!empty($row->lila)) {
                        $cleanLila = preg_replace('/[^0-9.]/', '', $row->lila);
                        $lila = $cleanLila ? ($cleanLila . ' CM') : $row->lila;
                    }
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-bold">{{ $namaPasien }}</td>
                    <td>{{ $row->no_ktp ?: '-' }}</td>
                    <td>{{ $namaSuami }}</td>
                    <td>{{ $alamatLengkap }}</td>
                    <td class="text-center">{{ $umur }}</td>
                    <td class="text-center">{{ $hpht }}</td>
                    <td class="text-center">{{ $uk }}</td>
                    <td class="text-center">{{ $kmk }}</td>
                    <td class="text-center">{{ $bmk }}</td>
                    <td>{{ $diagnosa }}</td>
                    <td class="text-center">{{ $lila }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="text-center" style="padding: 15px;">Tidak ada data pelayanan ibu hamil pada periode ini</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak otomatis dari Sistem TARIKSIS pada {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>

