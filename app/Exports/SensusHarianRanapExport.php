<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class SensusHarianRanapExport implements FromArray, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    protected $data;
    protected $bulan;

    public function __construct($data, $bulan)
    {
        $this->data  = $data;
        $this->bulan = $bulan;
    }

    public function title(): string
    {
        return 'Sensus Harian Ranap';
    }

    public function headings(): array
    {
        return [
            'Indikator',
            'Umum',
            'BPJS',
            'Total',
        ];
    }

    public function array(): array
    {
        $umum  = $this->data['umum'];
        $bpjs  = $this->data['bpjs'];
        $total = $this->data['total'];

        return [
            ['Pasien Awal Bulan', $umum['pasien_awal_bulan'], $bpjs['pasien_awal_bulan'], $total['pasien_awal_bulan']],
            ['Pasien Masuk', $umum['pasien_masuk'], $bpjs['pasien_masuk'], $total['pasien_masuk']],
            ['  - Masuk via IGD', $umum['pasien_masuk_igd'], $bpjs['pasien_masuk_igd'], $total['pasien_masuk_igd']],
            ['  - Masuk via Obgyn', $umum['pasien_masuk_obgyn'], $bpjs['pasien_masuk_obgyn'], $total['pasien_masuk_obgyn']],
            ['  - Masuk via Anak', $umum['pasien_masuk_anak'], $bpjs['pasien_masuk_anak'], $total['pasien_masuk_anak']],
            ['  - Masuk via Bedah', $umum['pasien_masuk_bedah'], $bpjs['pasien_masuk_bedah'], $total['pasien_masuk_bedah']],
            ['  - Masuk via Lainnya', $umum['pasien_masuk_lainnya'], $bpjs['pasien_masuk_lainnya'], $total['pasien_masuk_lainnya']],
            ['Pasien Pindahan', $umum['pasien_pindahan'], $bpjs['pasien_pindahan'], $total['pasien_pindahan']],
            ['Pasien Dipindahkan', $umum['pasien_dipindahkan'], $bpjs['pasien_dipindahkan'], $total['pasien_dipindahkan']],
            ['Pasien Keluar Hidup', $umum['pasien_keluar_hidup'], $bpjs['pasien_keluar_hidup'], $total['pasien_keluar_hidup']],
            ['Pasien Keluar Mati', $umum['pasien_keluar_mati'], $bpjs['pasien_keluar_mati'], $total['pasien_keluar_mati']],
            ['  Pria Mati ≥48 Jam', $umum['mati_48_pria'], $bpjs['mati_48_pria'], $total['mati_48_pria']],
            ['  Wanita Mati ≥48 Jam', $umum['mati_48_wanita'], $bpjs['mati_48_wanita'], $total['mati_48_wanita']],
            ['  Pria Mati <48 Jam', $umum['mati_kurang_48_pria'], $bpjs['mati_kurang_48_pria'], $total['mati_kurang_48_pria']],
            ['  Wanita Mati <48 Jam', $umum['mati_kurang_48_wanita'], $bpjs['mati_kurang_48_wanita'], $total['mati_kurang_48_wanita']],
            ['', '', '', ''],
            ['HARI PERAWATAN', '', '', ''],
            ['HP VVIP', $umum['hp_vvip'], $bpjs['hp_vvip'], $total['hp_vvip']],
            ['HP VIP', $umum['hp_vip'], $bpjs['hp_vip'], $total['hp_vip']],
            ['HP Kelas 1', $umum['hp_kelas_1'], $bpjs['hp_kelas_1'], $total['hp_kelas_1']],
            ['HP Kelas 2', $umum['hp_kelas_2'], $bpjs['hp_kelas_2'], $total['hp_kelas_2']],
            ['HP Kelas 3', $umum['hp_kelas_3'], $bpjs['hp_kelas_3'], $total['hp_kelas_3']],
            ['HP Khusus', $umum['hp_khusus'], $bpjs['hp_khusus'], $total['hp_khusus']],
            ['Total Hari Perawatan', $umum['total_hari_perawatan'], $bpjs['total_hari_perawatan'], $total['total_hari_perawatan']],
            ['', '', '', ''],
            ['Alokasi TT Awal Bulan', $this->data['alokasi_tt'], '', ''],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 15,
            'C' => 15,
            'D' => 15,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header style
        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '007C3C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // Total row style
        $totalRows = [2, 3, 9, 10, 11, 12, 25];
        foreach ($totalRows as $row) {
            $sheet->getStyle("A{$row}:D{$row}")->applyFromArray([
                'font' => ['bold' => true],
            ]);
        }

        // Section header style
        $sheet->getStyle('A18:D18')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
        ]);

        // All cells border
        $lastRow = 27;
        $sheet->getStyle("A1:D{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D1D5DB'],
                ],
            ],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // Numeric columns center
        $sheet->getStyle("B2:D{$lastRow}")->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        return [];
    }
}
