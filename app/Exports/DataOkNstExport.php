<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class DataOkNstExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $data;
    protected $no = 0;

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
            'No',
            'No. RM',
            'Nama Pasien',
            'No. Rawat',
            'Tipe NST',
            'Kode NST',
            'Nama Diagnosa / Prosedur NST',
            'Prioritas',
            'Tanggal Operasi',
            'Jam Mulai',
            'Jam Selesai',
            'Paket OK',
            'Status Operasi',
        ];
    }

    public function map($row): array
    {
        $this->no++;
        return [
            $this->no,
            $row->no_rkm_medis,
            $row->nm_pasien,
            $row->no_rawat,
            $row->tipe_nst ?? 'ICD-10 (Indikasi)',
            $row->kode_diagnosa,
            $row->nama_diagnosa,
            $row->prioritas == 1 ? 'Utama' : 'Sekunder (' . $row->prioritas . ')',
            $row->tgl_operasi,
            $row->jam_mulai,
            $row->jam_selesai,
            $row->nama_paket ?: '-',
            $row->status_operasi ?: '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:M1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '007C3C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [];
    }
}
