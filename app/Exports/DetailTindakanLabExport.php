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
use Carbon\Carbon;

class DetailTindakanLabExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $data;
    protected $tgl_mulai;
    protected $tgl_selesai;

    public function __construct($data, $tgl_mulai, $tgl_selesai)
    {
        $this->data = $data;
        $this->tgl_mulai = $tgl_mulai;
        $this->tgl_selesai = $tgl_selesai;
    }

    public function collection()
    {
        return $this->data;
    }

    public function title(): string
    {
        return 'Detail Tindakan Lab';
    }

    public function headings(): array
    {
        return [
            'No',
            'No. Rawat',
            'No. RM',
            'Nama Pasien',
            'JK',
            'Kode Periksa',
            'Nama Pemeriksaan',
            'Dokter PJ LAB',
            'Kode PJ',
            'NIP',
            'Dokter Perujuk',
            'Kode Perujuk',
            'Tanggal',
            'Jam',
            'Cara Bayar',
            'Status',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        $jkTeks = match(strtoupper((string) $row->jk)) {
            'L' => 'Laki-laki (L)',
            'P' => 'Perempuan (P)',
            default => '-',
        };

        return [
            $no,
            $row->no_rawat,
            $row->no_rkm_medis,
            strtoupper($row->nm_pasien),
            $jkTeks,
            $row->kd_jenis_prw,
            $row->nm_perawatan ?: '-',
            $row->nm_dokter_pj ?: '-',
            $row->kd_pj_lab ?: '-',
            $row->nip ?: '-',
            $row->nm_dokter_perujuk ?: '-',
            $row->kd_dokter_perujuk ?: '-',
            $row->tgl_periksa ? Carbon::parse($row->tgl_periksa)->format('d/m/Y') : '-',
            $row->jam ?: '-',
            $row->png_jawab,
            strtolower($row->status) == 'ralan' ? 'Rawat Jalan' : 'Rawat Inap',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $rowCount  = count($this->data);
        $totalRows = $rowCount + 1;

        // Header styling - Olive Green
        $sheet->getStyle('A1:P1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '007C3C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        if ($rowCount > 0) {
            $sheet->getStyle("A2:P{$totalRows}")->applyFromArray([
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => 'D1D5DB'],
                    ],
                ],
            ]);

            // Alignment formatting
            $sheet->getStyle("A2:A{$totalRows}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B2:C{$totalRows}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E2:F{$totalRows}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("I2:J{$totalRows}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("L2:N{$totalRows}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("P2:P{$totalRows}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        foreach (range('A', 'P') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [];
    }
}
