<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class FarmasiExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $data;
    protected $title;

    public function __construct($data, $title = 'DATA WAKTU TUNGGU FARMASI')
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
            'Nama Pasien',
            'No. RM',
            'Jenis Bayar',
            'Tgl Validasi',
            'Jam Validasi',
            'Tgl Penyerahan',
            'Jam Penyerahan',
            'Waktu Tunggu',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $row->nm_pasien,
            $row->no_rkm_medis,
            $row->png_jawab,
            $row->tgl_perawatan,
            $row->jam_validasi,
            $row->tgl_penyerahan,
            $row->jam_penyerahan,
            $this->formatDuration($row->total_waktu),
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

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '007C3C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Conditional coloring for wait time (Column I)
        $rowCount = count($this->data);
        foreach ($this->data as $index => $row) {
            $i = $index + 2; // Row offset
            $parts = explode(':', $row->total_waktu);
            $hours = (int)($parts[0] ?? 0);
            $minutes = (int)($parts[1] ?? 0);
            $totalMinutes = ($hours * 60) + $minutes;

            $color = '000000'; // Default black
            if ($hours > 0 || $totalMinutes >= 60) {
                $color = 'FF0000'; // Red
            } elseif ($totalMinutes >= 30) {
                $color = 'EAB308'; // Yellow/Orange (Tailwind yellow-500)
            } else {
                $color = '15803D'; // Green (Tailwind green-700)
            }

            $sheet->getStyle("I$i")->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color($color));
            $sheet->getStyle("I$i")->getFont()->setBold(true);
        }

        return [];
    }
}
