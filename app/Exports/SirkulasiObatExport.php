<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SirkulasiObatExport implements FromCollection, WithHeadings, WithMapping, WithStyles
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
            'Kode Barang',
            'Nama Barang',
            'Satuan',
            'Harga Barang',
            'Stok Awal',
            'Penerimaan',
            'Pemberian',
            'Stok Akhir',
        ];
    }

    public function map($row): array
    {
        return [
            $row->kode_brng,
            strtoupper($row->nama_brng),
            $row->satuan,
            $row->harga_beli,
            $row->stok_awal,
            $row->penerimaan,
            $row->distribusi,
            $row->stok_akhir,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style Header
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '007C3C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Auto-fit columns
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Format Qty and Price Columns
        $rowCount = count($this->data);
        if ($rowCount > 0) {
            $lastRow = $rowCount + 1;
            
            // Format numbers
            $sheet->getStyle("D2:D$lastRow")->getNumberFormat()->setFormatCode('"Rp" #,##0');
            $sheet->getStyle("E2:E$lastRow")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("F2:F$lastRow")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("G2:G$lastRow")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("H2:H$lastRow")->getNumberFormat()->setFormatCode('#,##0');

            // Alignments
            $sheet->getStyle("A2:A$lastRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("B2:B$lastRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("C2:C$lastRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D2:D$lastRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("E2:H$lastRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        return [];
    }
}
