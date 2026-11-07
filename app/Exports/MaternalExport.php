<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class MaternalExport implements FromArray, WithStyles, WithTitle
{
    protected $data;
    protected $tglMulai;
    protected $tglSelesai;

    public function __construct($data, $tglMulai, $tglSelesai)
    {
        $this->data = $data;
        $this->tglMulai = $tglMulai;
        $this->tglSelesai = $tglSelesai;
    }

    public function title(): string
    {
        return 'Laporan Maternal';
    }

    public function array(): array
    {
        $d = $this->data;
        $rows = [];

        // Header
        $rows[] = ['NO', 'KEGIATAN', 'Jumlah Dalam Wilayah Surabaya', 'Jumlah Luar Wilayah Surabaya', 'Jumlah Total', 'Ket'];

        // Section header
        $rows[] = ['A.', 'MATERNAL', '', '', '', ''];

        $items = $this->getReportItems();
        foreach ($items as $item) {
            $key = $item['key'];
            $dalam = isset($d[$key]) ? $d[$key]['dalam'] : 0;
            $luar = isset($d[$key]) ? $d[$key]['luar'] : 0;
            $total = isset($d[$key]) ? $d[$key]['total'] : 0;

            $rows[] = [
                $item['no'],
                $item['label'],
                $dalam ?: '',
                $luar ?: '',
                $total ?: 0,
                ''
            ];
        }

        return $rows;
    }

    private function getReportItems()
    {
        return [
            ['no' => '1', 'key' => 'bumil_baru_k1', 'label' => 'Jml.Bumil Baru (K1 Akses)'],
            ['no' => '2', 'key' => 'bumil_baru_k1_murni', 'label' => 'Jml.Bumil Baru < 12 mg. (K1 murni)'],
            ['no' => '3', 'key' => 'bumil_lama', 'label' => 'Jml.Bumil Lama'],
            ['no' => '4', 'key' => 'total_kunjungan', 'label' => 'Total Kunjungan ibu hamil'],
            ['no' => '5', 'key' => 'bumil_poedji_rochjati', 'label' => 'Jumlah Bumil di skor Poedji Rochjati'],
            ['no' => '6', 'key' => 'bumil_buku_kia', 'label' => 'Jml.Bumil mendapatkan buku KIA'],
            ['no' => '7', 'key' => 'bumil_k4', 'label' => 'Jml.Bumil dg. K4'],
            ['no' => '8', 'key' => 'bumil_fe1', 'label' => 'Jml. Bumil yang mendapatkan Fe I'],
            ['no' => '9', 'key' => 'bumil_fe3', 'label' => 'Jml. Bumil yang mendapatkan Fe III'],
            ['no' => '10', 'key' => 'bumil_periksa_hb', 'label' => 'Jml. Bumil yang diperiksa Hb'],
            ['no' => '11', 'key' => 'bumil_hb_8_11', 'label' => 'Jml.Bumil dg.Hb 8 - 11 mg/dl'],
            ['no' => '12', 'key' => 'bumil_hb_lt8', 'label' => 'Jml.Bumil dg.Hb < 8 mg/dl'],
            ['no' => '13', 'key' => 'bumil_lila', 'label' => 'Jml. Bumil yang diukur Lingkar Lengan Atas (LiLA)'],
            ['no' => '14', 'key' => 'bumil_periksa_protein_urine', 'label' => 'Jml. Bumil yang diperiksa Protein Urine'],
            ['no' => '15', 'key' => 'bumil_protein_urine_positif', 'label' => 'Jml. Bumil dengan hasil Protein Urine Positif'],
            ['no' => '16', 'key' => 'bumil_preeklampsia', 'label' => 'Jumlah Bumil yang dideteksi Pre Eklampsia'],
            ['no' => '17', 'key' => 'bumil_preeklampsia_positif', 'label' => 'Jumlah Bumil yang dideteksi Pre Eklampsia dengan hasil positif'],
            ['no' => '18', 'key' => 'bumil_preeklampsia_acetosal', 'label' => 'Jumlah Bumil positif Pre Eklampsia yang mendapat terapi Acetosal'],
            ['no' => '19', 'key' => 'bumil_usg_dopler', 'label' => 'Jumlah Bumil di USG Dopler'],
            ['no' => '20', 'key' => 'bumil_periksa_gula_darah', 'label' => 'Jml. Bumil yang diperiksa Gula Darah'],
            ['no' => '21', 'key' => 'bumil_gula_darah_tinggi', 'label' => 'Jml. Bumil dengan hasil Gula Darah > 140 g/dL'],
            ['no' => '22', 'key' => 'bumil_periksa_hepb', 'label' => 'Jml. Bumil yang diperiksa Hepatitis B'],
            ['no' => '23', 'key' => 'bumil_hepb_positif', 'label' => 'Jml. Bumil dengan Hepatitis B'],
            ['no' => '24', 'key' => 'bumil_risti', 'label' => 'Jml.Bumil dg. Risti'],
            ['no' => '25', 'key' => 'bumil_risti_ditangani', 'label' => 'Jml.Bumil Risti ditangani'],
            ['no' => '26', 'key' => 'bumil_risti_dirujuk', 'label' => 'Jml.Bumil risti dirujuk'],
            ['no' => '27', 'key' => 'bumil_kmk', 'label' => 'Jml. Ibu Hamil Kecil Masa Kehamilan'],
            ['no' => '28', 'key' => 'bumil_bmk', 'label' => 'Jml. Ibu Hamil Besar Masa Kehamilan'],
            ['no' => '29', 'key' => '__header_kasus', 'label' => 'Kasus maternal ditemukan'],
            ['no' => '', 'key' => 'kasus_hyperemisis', 'label' => 'a. Hyperemisis'],
            ['no' => '', 'key' => 'kasus_keguguran', 'label' => 'b. Keguguran'],
            ['no' => '', 'key' => 'kasus_eklampsia', 'label' => 'c. Eklamsi/ pre eklamsi'],
            ['no' => '', 'key' => 'kasus_perdarahan_kehamilan', 'label' => 'd. Perdarahan kehamilan'],
            ['no' => '', 'key' => 'kasus_perdarahan_persalinan', 'label' => 'e. Perdarahan persalinan'],
            ['no' => '', 'key' => 'kasus_perdarahan_nifas', 'label' => 'f. Perdarahan nifas ( HPP )'],
            ['no' => '', 'key' => 'kasus_partus_lama', 'label' => 'g. Partus lama'],
            ['no' => '', 'key' => 'kasus_infeksi', 'label' => 'h. Infeksi'],
            ['no' => '', 'key' => 'kasus_tb', 'label' => 'i. TB'],
            ['no' => '', 'key' => 'kasus_malaria', 'label' => 'j. Malaria'],
            ['no' => '', 'key' => 'kasus_jantung', 'label' => 'k. Jantung'],
            ['no' => '', 'key' => 'kasus_kek', 'label' => 'l. Kurang energi kronis (KEK)'],
            ['no' => '', 'key' => 'kasus_hiv', 'label' => 'm. Bumil dg HIV (+)'],
            ['no' => '', 'key' => 'kasus_lain', 'label' => 'n. Lain-Lain'],
            ['no' => '30', 'key' => 'bumil_ditawarkan_hiv_sifilis', 'label' => 'Jumlah Ibu Hamil yang ditawarkan tes HIV/ AIDS dan Sifilis'],
            ['no' => '31', 'key' => 'bumil_dirujuk_vct', 'label' => 'Jumlah Ibu Hamil yang dirujuk ke Klinik VCT'],
            ['no' => '32', 'key' => 'bumil_tes_hiv', 'label' => 'Jumlah Ibu Hamil yang melakukan tes HIV/ AIDS'],
            ['no' => '33', 'key' => 'bumil_periksa_sifilis', 'label' => 'Jumlah Ibu Hamil yang diperiksa Sifilis'],
            ['no' => '34', 'key' => 'bumil_sifilis', 'label' => 'Jumlah Ibu Hamil dengan Sifilis'],
            ['no' => '35', 'key' => 'persalinan_normal', 'label' => 'Persalinan normal'],
            ['no' => '36', 'key' => 'persalinan_imd', 'label' => 'Jumlah persalinan dengan IMD'],
            ['no' => '37', 'key' => 'persalinan_imd_berhasil', 'label' => 'Jumlah persalinan dengan IMD yang berhasil'],
            ['no' => '38', 'key' => 'persalinan_dirujuk_rs', 'label' => 'Persalinan dirujuk ke RS'],
            ['no' => '39', 'key' => 'persalinan_operasi', 'label' => 'Persalinan dengan operasi'],
            ['no' => '40', 'key' => '__header_tindakan', 'label' => 'Persalinan dengan tindakan'],
            ['no' => '', 'key' => 'tindakan_drip', 'label' => 'a. Drip'],
            ['no' => '', 'key' => 'tindakan_vakum', 'label' => 'b. Vakum'],
            ['no' => '', 'key' => 'tindakan_curatage', 'label' => 'c. Curatage'],
            ['no' => '', 'key' => 'tindakan_placenta_manual', 'label' => 'd. Placenta manual'],
            ['no' => '41', 'key' => 'nifas_baru', 'label' => 'Jumlah Ibu Nifas Baru'],
            ['no' => '42', 'key' => 'total_kunjungan_nifas', 'label' => 'Total Kunjungan Ibu Nifas'],
            ['no' => '43', 'key' => 'nifas_dirujuk', 'label' => 'Jumlah Ibu Nifas dirujuk'],
            ['no' => '44', 'key' => 'nifas_hpp_ditangani', 'label' => 'Jumlah Ibu Nifas dengan HPP yang ditangani'],
            ['no' => '46', 'key' => 'nifas_vit_a', 'label' => 'Jml. Ibu nifas dapat Vit. A'],
            ['no' => '47', 'key' => 'ibu_meneteki_baru', 'label' => 'Jml. Ibu meneteki baru'],
            ['no' => '48', 'key' => 'total_kunj_ibu_meneteki', 'label' => 'Total kunj. Ibu meneteki'],
            ['no' => '49', 'key' => 'kematian_maternal', 'label' => 'Jml. Kematian Ibu maternal'],
            ['no' => '50', 'key' => '__header_kematian', 'label' => 'Penyebab kematian maternal'],
            ['no' => '', 'key' => 'kematian_perdarahan', 'label' => 'a. Perdarahan'],
            ['no' => '', 'key' => 'kematian_infeksi_jalan_lahir', 'label' => 'b. Infeksi jalan lahir'],
            ['no' => '', 'key' => 'kematian_eklamsi', 'label' => 'c. Keracunan/ eklamsi'],
            ['no' => '', 'key' => 'kematian_lain', 'label' => 'd. Lain-lain'],
            ['no' => '51', 'key' => '__header_penolong', 'label' => 'Penolong Persalinan Terakhir dari kematian maternal'],
            ['no' => '', 'key' => 'penolong_dokter_spog', 'label' => 'a. Dokter/ SpOG'],
            ['no' => '', 'key' => 'penolong_bidan', 'label' => 'b. Bidan'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = count($this->array());

        // Header row style
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '007C3C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ]);

        // Section header (row 2)
        $sheet->getStyle('A2:F2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 10],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D9D9D9']],
        ]);

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(50);
        $sheet->getColumnDimension('C')->setWidth(18);
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(10);

        // Borders
        $sheet->getStyle("A1:F{$lastRow}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
        ]);

        // Highlight rows 27 & 28 (KMK/BMK) - yellow background
        $items = $this->getReportItems();
        $rowNum = 3; // start after header + section header
        foreach ($items as $item) {
            if ($item['key'] === 'bumil_kmk' || $item['key'] === 'bumil_bmk') {
                $sheet->getStyle("A{$rowNum}:F{$rowNum}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFF00']],
                ]);
            }
            // Grey background for section headers
            if (str_starts_with($item['key'], '__header')) {
                $sheet->getStyle("A{$rowNum}:F{$rowNum}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D9D9D9']],
                    'font' => ['bold' => true],
                ]);
            }
            $rowNum++;
        }

        // Center align number columns
        $sheet->getStyle("A1:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("C1:F{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [];
    }
}

