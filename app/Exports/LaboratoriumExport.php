<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class LaboratoriumExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $data;
    protected $title;

    public function __construct($data, $title = 'DATA LABORATORIUM')
    {
        $this->data = $data;
        $this->title = $title;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal Sampel',
            'Nama Pasien',
            'Jenis Bayar',
            'Pemeriksaan',
            'No. RM',
            'Jam Sampel',
            'Tanggal Hasil',
            'Jam Hasil',
            'Total Waktu Tunggu',
            'Status',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $row->tgl_sampel,
            $row->nm_pasien,
            $row->png_jawab,
            $row->pemeriksaan,
            $row->no_rkm_medis,
            $row->jam_sampel,
            $row->tgl_hasil,
            $row->jam_hasil,
            $this->formatDuration($row->total_waktu),
            $this->calculateStatus($row->total_waktu),
        ];
    }

    private function formatDuration($timeString)
    {
        if (!$timeString) return '-';
        
        $parts = explode(':', $timeString);
        $hours = (int)($parts[0] ?? 0);
        $minutes = (int)($parts[1] ?? 0);
        $seconds = (int)($parts[2] ?? 0);
        
        return "$hours jam $minutes menit $seconds detik";
    }

    private function calculateStatus($timeString)
    {
        if (!$timeString) return '-';
        
        $parts = explode(':', $timeString);
        $hours = (int)($parts[0] ?? 0);
        $minutes = (int)($parts[1] ?? 0);
        $seconds = (int)($parts[2] ?? 0);
        
        $totalSeconds = ($hours * 3600) + ($minutes * 60) + $seconds;
        return $totalSeconds < 3600 ? 'Tepat Waktu' : 'Tidak Sesuai';
    }

    public function styles(Worksheet $sheet)
    {
        // Header styling
        $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '007C3C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Auto-size columns
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Color "Tidak Sesuai" status in red
        $rowCount = count($this->data);
        for ($i = 2; $i <= ($rowCount + 1); $i++) {
            $status = $sheet->getCell("K$i")->getValue();
            if ($status === 'Tidak Sesuai') {
                $sheet->getStyle("K$i")->applyFromArray([
                    'font' => ['color' => ['rgb' => 'FF0000'], 'bold' => true],
                ]);
            }
        }

        return [];
    }
}
