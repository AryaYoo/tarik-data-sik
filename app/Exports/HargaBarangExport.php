<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class HargaBarangExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $data;
    protected $selectedColumns;
    protected $columnMap;

    public function __construct($data, $selectedColumns)
    {
        $this->data = $data;
        $this->selectedColumns = $selectedColumns;
        $this->columnMap = [
            'dasar' => 'Harga Dasar',
            'h_beli' => 'Harga Beli',
            'ralan' => 'Harga Ralan',
            'kelas1' => 'Kelas 1',
            'kelas2' => 'Kelas 2',
            'kelas3' => 'Kelas 3',
            'utama' => 'Utama',
            'vip' => 'VIP',
            'vvip' => 'VVIP',
            'beliluar' => 'Beli Luar',
            'jualbebas' => 'Jual Bebas',
            'karyawan' => 'Karyawan',
        ];
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        $headings = ['No', 'Kode Barang', 'Nama Barang', 'Satuan'];
        foreach ($this->selectedColumns as $col) {
            if (isset($this->columnMap[$col])) {
                $headings[] = $this->columnMap[$col];
            }
        }
        return $headings;
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        $mapped = [
            $no,
            $row->kode_brng,
            $row->nama_brng,
            $row->satuan ?? '-',
        ];

        foreach ($this->selectedColumns as $col) {
            if (isset($this->columnMap[$col])) {
                $mapped[] = $row->$col ?? 0;
            }
        }

        return $mapped;
    }

    public function styles(Worksheet $sheet)
    {
        $highestColumn = $sheet->getHighestColumn();
        $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '007C3C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        foreach (range('A', $highestColumn) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [];
    }
}
