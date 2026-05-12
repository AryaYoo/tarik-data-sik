<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class PenerimaanObatExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithEvents
{
    protected $data;
    protected $drugTotals;

    public function __construct($data)
    {
        $this->data = $data;
        // Pre-calculate totals for each drug
        $this->drugTotals = $data->groupBy('barang')->map(function ($group) {
            return $group->sum('jumlah');
        });
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'No',
            'Item Obat',
            'Satuan',
            'Dokter yang Meresepkan',
            'Jumlah Obat yang Diresepkan',
            'Total',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        static $lastDrug = null;

        if ($lastDrug !== $row->barang) {
            $no++;
            $lastDrug = $row->barang;
            $displayNo = $no;
            $displayBarang = $row->barang;
            $displaySatuan = $row->satuan;
            $displayTotal = $this->drugTotals[$row->barang];
        } else {
            // These will be merged later, but we provide values just in case
            $displayNo = '';
            $displayBarang = '';
            $displaySatuan = '';
            $displayTotal = '';
        }

        return [
            $displayNo,
            $displayBarang,
            $displaySatuan,
            $row->dokter,
            $row->jumlah,
            $displayTotal,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $data = $this->data;
                $currentRow = 2; // Data starts at row 2
    
                $grouped = $data->groupBy('barang');
                $no = 1;

                foreach ($grouped as $barang => $rows) {
                    $rowCount = $rows->count();
                    if ($rowCount > 1) {
                        $endRow = $currentRow + $rowCount - 1;
                        // Merge No, Item Obat, Satuan, and Total
                        $sheet->mergeCells("A{$currentRow}:A{$endRow}");
                        $sheet->mergeCells("B{$currentRow}:B{$endRow}");
                        $sheet->mergeCells("C{$currentRow}:C{$endRow}");
                        $sheet->mergeCells("F{$currentRow}:F{$endRow}");

                        // Center the merged cells vertically
                        $sheet->getStyle("A{$currentRow}:C{$endRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                        $sheet->getStyle("F{$currentRow}:F{$endRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                    }
                    $currentRow += $rowCount;
                    $no++;
                }
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '007C3C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [];
    }
}
