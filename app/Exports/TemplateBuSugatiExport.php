<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TemplateBuSugatiExport implements FromCollection, WithHeadings, WithMapping, WithStyles
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

    public function headings(): array
    {
        return [
            'Kode Barang',
            'Nama Barang',
            'Harga Barang',
            'Stok Awal',
            'Penerimaan',
            'Pemberian',
            'Stok Akhir',
            'Buffer Stock 15%',
            'Rencana Pemakaian',
            'Rencana Pengadaan',
            'Rencana Anggaran',
        ];
    }

    public function map($row): array
    {
        $harga_beli = (float) $row->harga_beli;
        $stok_awal = (float) $row->stok_awal;
        $penerimaan = (float) $row->penerimaan;
        $pemberian = (float) $row->pemberian;

        $stok_akhir = $stok_awal + $penerimaan - $pemberian;
        $buffer_stock = floor($pemberian * 0.15);
        $rencana_pemakaian = $pemberian + $buffer_stock;
        
        $rencana_pengadaan = 0.0;
        if ($rencana_pemakaian > $stok_akhir) {
            $rencana_pengadaan = $rencana_pemakaian - $stok_akhir;
        }

        $rencana_anggaran = $rencana_pengadaan * $harga_beli;

        return [
            $row->kode_brng,
            strtoupper($row->nama_brng),
            $harga_beli,
            $stok_awal,
            $penerimaan,
            $pemberian,
            $stok_akhir,
            $buffer_stock,
            $rencana_pemakaian,
            $rencana_pengadaan,
            $rencana_anggaran,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style Header
        $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '007C3C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Auto-fit columns
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Format Columns
        $rowCount = count($this->data);
        if ($rowCount > 0) {
            $lastRow = $rowCount + 1;
            
            // Format numbers & currency
            $sheet->getStyle("C2:C$lastRow")->getNumberFormat()->setFormatCode('"Rp" #,##0');
            $sheet->getStyle("D2:D$lastRow")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("E2:E$lastRow")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("F2:F$lastRow")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("G2:G$lastRow")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("H2:H$lastRow")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("I2:I$lastRow")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("J2:J$lastRow")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("K2:K$lastRow")->getNumberFormat()->setFormatCode('"Rp" #,##0');

            // Alignments
            $sheet->getStyle("A2:A$lastRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("B2:B$lastRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("C2:K$lastRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        return [];
    }
}
