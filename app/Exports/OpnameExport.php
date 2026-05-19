<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class OpnameExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Nama Obat',
            'Satuan',
            'Stok Sistem',
            'Stok Fisik',
            'Selisih',
            'Keterangan',
        ];
    }

    public function map($row): array
    {
        $selisih = $row->selisih;
        if ($selisih < 0) {
            $keterangan = 'KURANG';
        } elseif ($selisih > 0) {
            $keterangan = 'LEBIH';
        } else {
            $keterangan = 'SESUAI';
        }

        return [
            strtoupper($row->nama_brng),
            strtoupper($row->satuan),
            $row->stok_sistem,
            $row->stok_fisik,
            $selisih,
            $keterangan,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style Header
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '007C3C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Auto-fit columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Format numbers and align columns
        $rowCount = count($this->data);
        if ($rowCount > 0) {
            $lastRow = $rowCount + 1;
            
            // Format numbers for Stok Sistem, Stok Fisik, and Selisih
            $sheet->getStyle("C2:C$lastRow")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("D2:D$lastRow")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("E2:E$lastRow")->getNumberFormat()->setFormatCode('+#,##0;-#,##0;0');

            // Alignments
            $sheet->getStyle("A2:B$lastRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("C2:E$lastRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("F2:F$lastRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Dynamic Font Colors for Keterangan (Column F) & Selisih (Column E)
            foreach ($this->data as $index => $row) {
                $rowNum = $index + 2;
                $selisih = $row->selisih;
                
                if ($selisih < 0) {
                    // Red for negative (Kurang)
                    $color = 'DC2626'; // Tailwind red-600
                } elseif ($selisih > 0) {
                    // Green for positive (Lebih)
                    $color = '16A34A'; // Tailwind green-600
                } else {
                    // Gray for zero (Sesuai)
                    $color = '4B5563'; // Tailwind gray-600
                }

                $sheet->getStyle("E$rowNum")->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color($color))->setBold(true);
                $sheet->getStyle("F$rowNum")->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color($color))->setBold(true);
            }
        }

        return [];
    }
}
