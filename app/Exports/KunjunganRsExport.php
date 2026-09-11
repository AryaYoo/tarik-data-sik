<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class KunjunganRsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
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
            'No. Rawat',
            'Tgl Reg',
            'Jam',
            'No. RM',
            'Pasien',
            'JK',
            'Poliklinik',
            'Dokter',
            'Penjamin',
            'Jns Kunjungan',
            'Status Rawat',
        ];
    }

    public function map($row): array
    {
        $this->no++;
        return [
            $this->no,
            $row->no_rawat,
            $row->tgl_registrasi,
            $row->jam_reg,
            $row->no_rkm_medis,
            $row->nm_pasien,
            $row->jk,
            $row->nm_poli ?: '-',
            $row->nm_dokter ?: '-',
            $row->png_jawab ?: '-',
            $row->jns_kunjungan ?: '-',
            $row->status_lanjut ?: '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:L1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '007C3C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [];
    }
}
