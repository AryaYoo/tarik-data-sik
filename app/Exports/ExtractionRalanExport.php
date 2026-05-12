<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Support\Collection;

class ExtractionRalanExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $data;
    protected $obat;
    protected $rows;

    public function __construct($data, $obat)
    {
        $this->data = $data;
        $this->obat = $obat;
        $this->rows = $this->buildRows();
    }

    private function buildRows(): Collection
    {
        $rows = collect();
        $no = 0;

        foreach ($this->data as $row) {
            $no++;
            $rowObat = $this->obat[$row->no_rawat] ?? collect();

            if ($rowObat->isEmpty()) {
                $rows->push([
                    '_type' => 'pasien',
                    'no' => $no,
                    'no_rawat' => $row->no_rawat,
                    'nm_pasien' => $row->nm_pasien,
                    'umur' => $row->umur,
                    'jk' => $row->jk,
                    'nama_obat' => '',
                    'jumlah' => '',
                ]);
            } else {
                $first = $rowObat->first();
                $rows->push([
                    '_type' => 'pasien',
                    'no' => $no,
                    'no_rawat' => $row->no_rawat,
                    'nm_pasien' => $row->nm_pasien,
                    'umur' => $row->umur,
                    'jk' => $row->jk,
                    'nama_obat' => $first->nama_brng,
                    'jumlah' => (float) $first->jml,
                ]);

                foreach ($rowObat->skip(1) as $item) {
                    $rows->push([
                        '_type' => 'obat',
                        'no' => '',
                        'no_rawat' => '',
                        'nm_pasien' => '',
                        'umur' => '',
                        'jk' => '',
                        'nama_obat' => $item->nama_brng,
                        'jumlah' => (float) $item->jml,
                    ]);
                }
            }
        }

        return $rows;
    }

    public function collection()
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'No',
            'No. Rawat',
            'Nama Pasien',
            'Usia',
            'Jenis Kelamin',
            'Nama Obat',
            'Jumlah',
        ];
    }

    public function map($row): array
    {
        return [
            $row['no'],
            $row['no_rawat'],
            $row['nm_pasien'],
            $row['umur'],
            $row['jk'],
            $row['nama_obat'],
            $row['jumlah'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '007C3C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $excelRow = 2;
        foreach ($this->rows as $row) {
            if ($row['_type'] === 'obat') {
                $sheet->getStyle("A{$excelRow}:G{$excelRow}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EFF6FF']], // Blue tint
                    'font' => ['color' => ['rgb' => '1E40AF']],
                ]);
            }
            $excelRow++;
        }

        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [];
    }
}
