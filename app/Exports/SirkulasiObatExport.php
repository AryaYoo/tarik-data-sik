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
            'Nama Obat',
            'Stok Awal',
            'Stok Akhir',
            'Selisih',
            'Jumlah Pengadaan',
            'Harga Beli',
        ];
    }

    public function map($row): array
    {
        $namaObat = strtoupper($row->nama_brng) . ' (' . strtoupper($row->satuan) . ')';
        $selisih = $row->stok_akhir - $row->stok_awal;

        return [
            $namaObat,
            $row->stok_awal,
            $row->stok_akhir,
            $selisih,
            $row->jumlah_pengadaan,
            $row->harga_beli,
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

        // Format Qty and Price Columns
        $rowCount = count($this->data);
        if ($rowCount > 0) {
            $lastRow = $rowCount + 1;
            
            // Format numbers for Stok, Selisih, Pengadaan and Price columns
            $sheet->getStyle("B2:B$lastRow")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("C2:C$lastRow")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("D2:D$lastRow")->getNumberFormat()->setFormatCode('+#,##0;-#,##0;0');
            $sheet->getStyle("E2:E$lastRow")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("F2:F$lastRow")->getNumberFormat()->setFormatCode('"Rp" #,##0');

            // Align numeric columns
            $sheet->getStyle("B2:F$lastRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("A2:A$lastRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        }

        return [];
    }
}
