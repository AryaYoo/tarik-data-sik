<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kesehatan Ibu (Maternal)</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 8mm 10mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 7.5px;
            color: #222;
            line-height: 1.15;
        }
        .header {
            text-align: center;
            margin-bottom: 8px;
            border-bottom: 2px solid #007C3C;
            padding-bottom: 5px;
        }
        .title {
            font-size: 13px;
            font-weight: bold;
            color: #007C3C;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .subtitle {
            font-size: 9px;
            color: #555;
            margin-top: 2px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th {
            background-color: #007C3C;
            color: white;
            font-weight: bold;
            text-align: center;
            padding: 4px 3px;
            border: 1px solid #777;
            font-size: 7.5px;
            vertical-align: middle;
        }
        td {
            padding: 2.8px 4px;
            border: 1px solid #ccc;
            font-size: 7.5px;
            vertical-align: middle;
        }
        tr.header-row {
            background-color: #e5e7eb;
            font-weight: bold;
        }
        tr.highlight-row {
            background-color: #fef08a;
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
        .text-bold {
            font-weight: bold;
        }
        .pl-sub {
            padding-left: 14px;
        }
        .footer {
            margin-top: 8px;
            font-size: 7px;
            color: #777;
            text-align: right;
            border-top: 1px solid #ddd;
            padding-top: 3px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">Laporan Kesehatan Ibu (Maternal)</h1>
        <p class="subtitle">
            Periode: {{ $tglMulai }} s/d {{ $tglSelesai }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">NO</th>
                <th style="width: 55%; text-align: left; padding-left: 6px;">KEGIATAN</th>
                <th style="width: 13%;">Jumlah Dalam Wilayah Surabaya</th>
                <th style="width: 13%;">Jumlah Luar Wilayah Surabaya</th>
                <th style="width: 10%;">Jumlah Total</th>
                <th style="width: 4%;">Ket</th>
            </tr>
        </thead>
        <tbody>
            <tr class="header-row">
                <td class="text-center">A.</td>
                <td colspan="5" style="text-align: left; padding-left: 6px;">MATERNAL</td>
            </tr>

            @php
                $reportRows = [
                    ['no' => '1', 'key' => 'bumil_baru_k1', 'label' => 'Jml.Bumil Baru (K1 Akses)', 'type' => 'normal'],
                    ['no' => '2', 'key' => 'bumil_baru_k1_murni', 'label' => 'Jml.Bumil Baru < 12 mg. (K1 murni)', 'type' => 'normal'],
                    ['no' => '3', 'key' => 'bumil_lama', 'label' => 'Jml.Bumil Lama', 'type' => 'normal'],
                    ['no' => '4', 'key' => 'total_kunjungan', 'label' => 'Total Kunjungan ibu hamil', 'type' => 'normal'],
                    ['no' => '5', 'key' => 'bumil_poedji_rochjati', 'label' => 'Jumlah Bumil di skor Poedji Rochjati', 'type' => 'normal'],
                    ['no' => '6', 'key' => 'bumil_buku_kia', 'label' => 'Jml.Bumil mendapatkan buku KIA', 'type' => 'normal'],
                    ['no' => '7', 'key' => 'bumil_k4', 'label' => 'Jml.Bumil dg. K4', 'type' => 'normal'],
                    ['no' => '8', 'key' => 'bumil_fe1', 'label' => 'Jml. Bumil yang mendapatkan Fe I', 'type' => 'normal'],
                    ['no' => '9', 'key' => 'bumil_fe3', 'label' => 'Jml. Bumil yang mendapatkan Fe III', 'type' => 'normal'],
                    ['no' => '10', 'key' => 'bumil_periksa_hb', 'label' => 'Jml. Bumil yang diperiksa Hb', 'type' => 'normal'],
                    ['no' => '11', 'key' => 'bumil_hb_8_11', 'label' => 'Jml.Bumil dg.Hb 8 - 11 mg/dl', 'type' => 'normal'],
                    ['no' => '12', 'key' => 'bumil_hb_lt8', 'label' => 'Jml.Bumil dg.Hb < 8 mg/dl', 'type' => 'normal'],
                    ['no' => '13', 'key' => 'bumil_lila', 'label' => 'Jml. Bumil yang diukur Lingkar Lengan Atas (LiLA)', 'type' => 'normal'],
                    ['no' => '14', 'key' => 'bumil_periksa_protein_urine', 'label' => 'Jml. Bumil yang diperiksa Protein Urine', 'type' => 'normal'],
                    ['no' => '15', 'key' => 'bumil_protein_urine_positif', 'label' => 'Jml. Bumil dengan hasil Protein Urine Positif', 'type' => 'normal'],
                    ['no' => '16', 'key' => 'bumil_preeklampsia', 'label' => 'Jumlah Bumil yang dideteksi Pre Eklampsia', 'type' => 'normal'],
                    ['no' => '17', 'key' => 'bumil_preeklampsia_positif', 'label' => 'Jumlah Bumil yang dideteksi Pre Eklampsia dengan hasil positif', 'type' => 'normal'],
                    ['no' => '18', 'key' => 'bumil_preeklampsia_acetosal', 'label' => 'Jumlah Bumil positif Pre Eklampsia yang mendapat terapi Acetosal', 'type' => 'normal'],
                    ['no' => '19', 'key' => 'bumil_usg_dopler', 'label' => 'Jumlah Bumil di USG Dopler', 'type' => 'normal'],
                    ['no' => '20', 'key' => 'bumil_periksa_gula_darah', 'label' => 'Jml. Bumil yang diperiksa Gula Darah', 'type' => 'normal'],
                    ['no' => '21', 'key' => 'bumil_gula_darah_tinggi', 'label' => 'Jml. Bumil dengan hasil Gula Darah > 140 g/dL', 'type' => 'normal'],
                    ['no' => '22', 'key' => 'bumil_periksa_hepb', 'label' => 'Jml. Bumil yang diperiksa Hepatitis B', 'type' => 'normal'],
                    ['no' => '23', 'key' => 'bumil_hepb_positif', 'label' => 'Jml. Bumil dengan Hepatitis B', 'type' => 'normal'],
                    ['no' => '24', 'key' => 'bumil_risti', 'label' => 'Jml.Bumil dg. Risti', 'type' => 'normal'],
                    ['no' => '25', 'key' => 'bumil_risti_ditangani', 'label' => 'Jml.Bumil Risti ditangani', 'type' => 'normal'],
                    ['no' => '26', 'key' => 'bumil_risti_dirujuk', 'label' => 'Jml.Bumil risti dirujuk', 'type' => 'normal'],
                    ['no' => '27', 'key' => 'bumil_kmk', 'label' => 'Jml. Ibu Hamil Kecil Masa Kehamilan', 'type' => 'highlight'],
                    ['no' => '28', 'key' => 'bumil_bmk', 'label' => 'Jml. Ibu Hamil Besar Masa Kehamilan', 'type' => 'highlight'],
                    ['no' => '29', 'key' => '__header_kasus', 'label' => 'Kasus maternal ditemukan', 'type' => 'header'],
                    ['no' => '', 'key' => 'kasus_hyperemisis', 'label' => 'a. Hyperemisis', 'type' => 'sub'],
                    ['no' => '', 'key' => 'kasus_keguguran', 'label' => 'b. Keguguran', 'type' => 'sub'],
                    ['no' => '', 'key' => 'kasus_eklampsia', 'label' => 'c. Eklamsi/ pre eklamsi', 'type' => 'sub'],
                    ['no' => '', 'key' => 'kasus_perdarahan_kehamilan', 'label' => 'd. Perdarahan kehamilan', 'type' => 'sub'],
                    ['no' => '', 'key' => 'kasus_perdarahan_persalinan', 'label' => 'e. Perdarahan persalinan', 'type' => 'sub'],
                    ['no' => '', 'key' => 'kasus_perdarahan_nifas', 'label' => 'f. Perdarahan nifas ( HPP )', 'type' => 'sub'],
                    ['no' => '', 'key' => 'kasus_partus_lama', 'label' => 'g. Partus lama', 'type' => 'sub'],
                    ['no' => '', 'key' => 'kasus_infeksi', 'label' => 'h. Infeksi', 'type' => 'sub'],
                    ['no' => '', 'key' => 'kasus_tb', 'label' => 'i. TB', 'type' => 'sub'],
                    ['no' => '', 'key' => 'kasus_malaria', 'label' => 'j. Malaria', 'type' => 'sub'],
                    ['no' => '', 'key' => 'kasus_jantung', 'label' => 'k. Jantung', 'type' => 'sub'],
                    ['no' => '', 'key' => 'kasus_kek', 'label' => 'l. Kurang energi kronis (KEK)', 'type' => 'sub'],
                    ['no' => '', 'key' => 'kasus_hiv', 'label' => 'm. Bumil dg HIV (+)', 'type' => 'sub'],
                    ['no' => '', 'key' => 'kasus_lain', 'label' => 'n. Lain-lain', 'type' => 'sub'],
                    ['no' => '30', 'key' => 'bumil_ditawarkan_hiv_sifilis', 'label' => 'Jumlah Ibu Hamil yang ditawarkan tes HIV/ AIDS dan Sifilis', 'type' => 'normal'],
                    ['no' => '31', 'key' => 'bumil_dirujuk_vct', 'label' => 'Jumlah Ibu Hamil yang dirujuk ke Klinik VCT', 'type' => 'normal'],
                    ['no' => '32', 'key' => 'bumil_tes_hiv', 'label' => 'Jumlah Ibu Hamil yang melakukan tes HIV/ AIDS', 'type' => 'normal'],
                    ['no' => '33', 'key' => 'bumil_periksa_sifilis', 'label' => 'Jumlah Ibu Hamil yang diperiksa Sifilis', 'type' => 'normal'],
                    ['no' => '34', 'key' => 'bumil_sifilis', 'label' => 'Jumlah Ibu Hamil dengan Sifilis', 'type' => 'normal'],
                    ['no' => '35', 'key' => 'persalinan_normal', 'label' => 'Persalinan normal', 'type' => 'normal'],
                    ['no' => '36', 'key' => 'persalinan_imd', 'label' => 'Jumlah persalinan dengan IMD', 'type' => 'normal'],
                    ['no' => '37', 'key' => 'persalinan_imd_berhasil', 'label' => 'Jumlah persalinan dengan IMD yang berhasil', 'type' => 'normal'],
                    ['no' => '38', 'key' => 'persalinan_dirujuk_rs', 'label' => 'Persalinan dirujuk ke RS', 'type' => 'normal'],
                    ['no' => '39', 'key' => 'persalinan_operasi', 'label' => 'Persalinan dengan operasi', 'type' => 'normal'],
                    ['no' => '40', 'key' => '__header_tindakan', 'label' => 'Persalinan dengan tindakan', 'type' => 'header'],
                    ['no' => '', 'key' => 'tindakan_drip', 'label' => 'a. Drip', 'type' => 'sub'],
                    ['no' => '', 'key' => 'tindakan_vakum', 'label' => 'b. Vakum', 'type' => 'sub'],
                    ['no' => '', 'key' => 'tindakan_curatage', 'label' => 'c. Curatage', 'type' => 'sub'],
                    ['no' => '', 'key' => 'tindakan_placenta_manual', 'label' => 'd. Placenta manual', 'type' => 'sub'],
                    ['no' => '41', 'key' => 'nifas_baru', 'label' => 'Jumlah Ibu Nifas Baru', 'type' => 'normal'],
                    ['no' => '42', 'key' => 'total_kunjungan_nifas', 'label' => 'Total Kunjungan Ibu Nifas', 'type' => 'normal'],
                    ['no' => '43', 'key' => 'nifas_dirujuk', 'label' => 'Jumlah Ibu Nifas dirujuk', 'type' => 'normal'],
                    ['no' => '44', 'key' => 'nifas_hpp_ditangani', 'label' => 'Jumlah Ibu Nifas dengan HPP yang ditangani', 'type' => 'normal'],
                    ['no' => '46', 'key' => 'nifas_vit_a', 'label' => 'Jml. Ibu nifas dapat Vit. A', 'type' => 'normal'],
                    ['no' => '47', 'key' => 'ibu_meneteki_baru', 'label' => 'Jml. Ibu meneteki baru', 'type' => 'normal'],
                    ['no' => '48', 'key' => 'total_kunj_ibu_meneteki', 'label' => 'Total kunj. Ibu meneteki', 'type' => 'normal'],
                    ['no' => '49', 'key' => 'kematian_maternal', 'label' => 'Jml. Kematian Ibu maternal', 'type' => 'normal'],
                    ['no' => '50', 'key' => '__header_kematian', 'label' => 'Penyebab kematian maternal', 'type' => 'header'],
                    ['no' => '', 'key' => 'kematian_perdarahan', 'label' => 'a. Perdarahan', 'type' => 'sub'],
                    ['no' => '', 'key' => 'kematian_infeksi_jalan_lahir', 'label' => 'b. Infeksi jalan lahir', 'type' => 'sub'],
                    ['no' => '', 'key' => 'kematian_eklamsi', 'label' => 'c. Keracunan/ eklamsi', 'type' => 'sub'],
                    ['no' => '', 'key' => 'kematian_lain', 'label' => 'd. Lain-lain', 'type' => 'sub'],
                    ['no' => '51', 'key' => '__header_penolong', 'label' => 'Penolong Persalinan Terakhir dari kematian maternal', 'type' => 'header'],
                    ['no' => '', 'key' => 'penolong_dokter_spog', 'label' => 'a. Dokter/ SpOG', 'type' => 'sub'],
                    ['no' => '', 'key' => 'penolong_bidan', 'label' => 'b. Bidan', 'type' => 'sub'],
                ];
            @endphp

            @foreach($reportRows as $row)
                @php
                    $isHeader = ($row['type'] === 'header');
                    $isHighlight = ($row['type'] === 'highlight');
                    $isSub = ($row['type'] === 'sub');

                    $trClass = '';
                    if ($isHeader) {
                        $trClass = 'header-row';
                    } elseif ($isHighlight) {
                        $trClass = 'highlight-row';
                    }

                    $key = $row['key'];
                    $hasData = isset($data[$key]);
                    $dalam = $hasData && $data[$key]['dalam'] > 0 ? $data[$key]['dalam'] : '';
                    $luar = $hasData && $data[$key]['luar'] > 0 ? $data[$key]['luar'] : '';
                    $total = $hasData ? $data[$key]['total'] : 0;
                @endphp

                <tr class="{{ $trClass }}">
                    <td class="text-center">{{ $row['no'] }}</td>
                    <td class="{{ $isSub ? 'pl-sub' : '' }}" style="text-align: left;">{{ $row['label'] }}</td>
                    @if($isHeader)
                        <td colspan="4" style="background-color: #d1d5db;"></td>
                    @else
                        <td class="text-center text-bold">{{ $dalam }}</td>
                        <td class="text-center text-bold">{{ $luar }}</td>
                        <td class="text-center text-bold">{{ $total }}</td>
                        <td class="text-center"></td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak otomatis dari Sistem TARIKSIS pada {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>

