<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class WaktuTungguGabunganExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $data;
    protected $summary;
    protected $tgl_mulai;
    protected $tgl_selesai;

    public function __construct($data, $summary, $tgl_mulai, $tgl_selesai)
    {
        $this->data        = $data;
        $this->summary     = $summary;
        $this->tgl_mulai   = $tgl_mulai;
        $this->tgl_selesai = $tgl_selesai;
    }

    public function collection()
    {
        return $this->data;
    }

    public function title(): string
    {
        return 'Lama Pelayanan Lab PK';
    }

    public function headings(): array
    {
        return [
            'No.',
            'No.RM',
            'Nama Pasien',
            'Dokter Perujuk',
            'No.Order',
            'Kode Periksa',
            'Pemeriksaan',
            'Jam Permintaan',
            'Jam Sampel',
            'Jam Hasil',
            'Permintaan-Sampel(m)',
            'Sampel-Hasil(m)',
            'Permintaan-Hasil(m)',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        $tPermintaan = !empty($row->jam_permintaan_full) ? strtotime($row->jam_permintaan_full) : null;
        $tSampel     = !empty($row->jam_sampel_full) ? strtotime($row->jam_sampel_full) : null;
        $tHasil      = !empty($row->jam_hasil_full) ? strtotime($row->jam_hasil_full) : null;

        $psMin = ($tSampel && $tPermintaan) ? number_format(($tSampel - $tPermintaan) / 60, 2, '.', '') : '0.00';
        $shMin = ($tHasil && $tSampel) ? number_format(($tHasil - $tSampel) / 60, 2, '.', '') : '0.00';
        $phMin = ($tHasil && $tPermintaan) ? number_format(($tHasil - $tPermintaan) / 60, 2, '.', '') : '0.00';

        return [
            $no,
            $row->no_rkm_medis,
            strtoupper($row->nm_pasien),
            $row->nm_dokter_perujuk ?: '-',
            $row->noorder,
            $row->kd_jenis_prw ?: '-',
            $row->pemeriksaan ?: '-',
            $row->jam_permintaan_full ?: '-',
            $row->jam_sampel_full ?: '-',
            $row->jam_hasil_full ?: '-',
            $psMin,
            $shMin,
            $phMin,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $rowCount  = count($this->data);
        $dataEndRow = $rowCount + 1;

        // Header styling - Olive Green
        $sheet->getStyle('A1:M1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '007C3C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        if ($rowCount > 0) {
            $sheet->getStyle("A2:M{$dataEndRow}")->applyFromArray([
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => 'E5E7EB'],
                    ],
                ],
            ]);

            // Alignments
            $sheet->getStyle("A2:A{$dataEndRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B2:B{$dataEndRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E2:F{$dataEndRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("H2:J{$dataEndRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("K2:M{$dataEndRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        // Write summary rows at bottom matching Khanza format
        if (!empty($this->summary)) {
            $currRow = $dataEndRow + 1;

            // Rata-rata row
            $sheet->setCellValue("A{$currRow}", "Rata-rata (Menit)");
            $sheet->setCellValue("J{$currRow}", ":");
            $sheet->setCellValue("K{$currRow}", number_format($this->summary['ps_avg'] ?? 0, 2, '.', ''));
            $sheet->setCellValue("L{$currRow}", number_format($this->summary['sh_avg'] ?? 0, 2, '.', ''));
            $sheet->setCellValue("M{$currRow}", number_format($this->summary['ph_avg'] ?? 0, 2, '.', ''));
            $sheet->getStyle("A{$currRow}:M{$currRow}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => '1F2937']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']],
            ]);
            $currRow++;

            // Ranges
            foreach ($this->summary['ranges'] ?? [] as $r) {
                $sheet->setCellValue("A{$currRow}", $r['label']);
                $sheet->setCellValue("J{$currRow}", ":");
                $sheet->setCellValue("K{$currRow}", $r['ps']);
                $sheet->setCellValue("L{$currRow}", $r['sh']);
                $sheet->setCellValue("M{$currRow}", $r['ph']);
                $sheet->getStyle("A{$currRow}:M{$currRow}")->applyFromArray([
                    'font' => ['size' => 9],
                ]);
                $sheet->getStyle("K{$currRow}:M{$currRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $currRow++;
            }

            // Summary table border
            $sumStartRow = $dataEndRow + 1;
            $sumEndRow   = $currRow - 1;
            $sheet->getStyle("A{$sumStartRow}:M{$sumEndRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => 'D1D5DB'],
                    ],
                ],
            ]);
        }

        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [];
    }
}
