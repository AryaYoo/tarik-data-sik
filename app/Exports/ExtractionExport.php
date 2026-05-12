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

class ExtractionExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $data;
    protected $instruksi;
    protected $rows; // baris yang sudah di-flatten
    protected $instruksiRowNumbers = []; // nomor baris Excel yang merupakan baris instruksi

    public function __construct($data, $instruksi)
    {
        $this->data = $data;
        $this->instruksi = $instruksi;
        $this->rows = $this->buildRows();
    }

    /**
     * Flatten data pasien + instruksi menjadi baris-baris Excel.
     */
    private function buildRows(): Collection
    {
        $rows = collect();
        $no = 0;

        foreach ($this->data as $row) {
            $no++;
            $rowInstruksi = $this->instruksi[$row->no_rawat] ?? collect();

            if ($rowInstruksi->isEmpty()) {
                // Tidak ada instruksi: satu baris biasa
                $rows->push([
                    '_type' => 'pasien',
                    'no' => $no,
                    'no_rawat' => $row->no_rawat,
                    'nm_pasien' => $row->nm_pasien,
                    'umur' => $row->umur,
                    'jk' => $row->jk,
                    'prosedur' => $row->prosedur_utama,
                    'diagnosa' => $row->diagnosa_utama,
                    'lama' => $row->lama . ' Hari',
                    'jam_rawat' => '',
                    'instruksi' => '',
                ]);
            } else {
                // Baris pertama: data pasien + instruksi pertama
                $first = $rowInstruksi->first();
                $rows->push([
                    '_type' => 'pasien',
                    'no' => $no,
                    'no_rawat' => $row->no_rawat,
                    'nm_pasien' => $row->nm_pasien,
                    'umur' => $row->umur,
                    'jk' => $row->jk,
                    'prosedur' => $row->prosedur_utama,
                    'diagnosa' => $row->diagnosa_utama,
                    'lama' => $row->lama . ' Hari',
                    'jam_rawat' => \Carbon\Carbon::parse($first->jam_rawat)->format('H:i'),
                    'instruksi' => $first->instruksi,
                ]);

                // Baris berikutnya: hanya jam + instruksi
                foreach ($rowInstruksi->skip(1) as $ins) {
                    $rows->push([
                        '_type' => 'instruksi',
                        'no' => '',
                        'no_rawat' => '',
                        'nm_pasien' => '',
                        'umur' => '',
                        'jk' => '',
                        'prosedur' => '',
                        'diagnosa' => '',
                        'lama' => '',
                        'jam_rawat' => \Carbon\Carbon::parse($ins->jam_rawat)->format('H:i'),
                        'instruksi' => $ins->instruksi,
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
            'Terapi yang Diberikan',
            'Diagnosa Utama',
            'Lama Perawatan',
            'Jam Rawat',
            'Instruksi',
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
            $row['prosedur'],
            $row['diagnosa'],
            $row['lama'],
            $row['jam_rawat'],
            $row['instruksi'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header row styling
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '007C3C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Warnai baris instruksi (baris lanjutan) dengan warna kuning muda
        $excelRow = 2; // mulai dari baris ke-2 (setelah header)
        foreach ($this->rows as $row) {
            if ($row['_type'] === 'instruksi') {
                $sheet->getStyle("A{$excelRow}:J{$excelRow}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFBEB']],
                    'font' => ['color' => ['rgb' => '92400E']],
                ]);
            }
            $excelRow++;
        }

        // Auto-size kolom
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [];
    }
}
