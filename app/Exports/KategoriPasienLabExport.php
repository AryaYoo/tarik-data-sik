<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Carbon\Carbon;

class KategoriPasienLabExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $data;
    protected $tgl_mulai;
    protected $tgl_selesai;

    public function __construct($data, $tgl_mulai, $tgl_selesai)
    {
        $this->data        = $data;
        $this->tgl_mulai   = $tgl_mulai;
        $this->tgl_selesai = $tgl_selesai;
    }

    public function collection()
    {
        return $this->data;
    }

    public function title(): string
    {
        return 'Kategori Pasien Lab';
    }

    public function headings(): array
    {
        return [
            'No',
            'Tgl. Pemeriksaan',
            'No. RM',
            'Nama Pasien',
            'Tgl. Lahir',
            'Umur',
            'Kategori Usia',
            'Jenis Pemeriksaan',
            'Jenis Bayar',
            'Status Kunjungan',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        $umurTahun    = (int) $row->umur_tahun;
        $umurBulanTotal = (int) $row->umur_bulan_total;
        $bulanSisa    = $umurBulanTotal % 12;

        if ($umurTahun >= 1) {
            $umurTeks = $umurTahun . ' Th ' . $bulanSisa . ' Bln';
        } elseif ($umurBulanTotal >= 1) {
            $umurTeks = $umurBulanTotal . ' Bulan';
        } else {
            $hari     = Carbon::parse($row->tgl_lahir)->diffInDays(Carbon::parse($row->tgl_sampel));
            $umurTeks = $hari . ' Hari';
        }

        return [
            $no,
            Carbon::parse($row->tgl_sampel)->format('d/m/Y'),
            $row->no_rkm_medis,
            strtoupper($row->nm_pasien),
            $row->tgl_lahir ? Carbon::parse($row->tgl_lahir)->format('d/m/Y') : '-',
            $umurTeks,
            $row->kategori_usia,
            $row->pemeriksaan ?: '-',
            $row->png_jawab,
            $row->status == 'ralan' ? 'Rawat Jalan' : 'Rawat Inap',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header styling - Olive Green
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '007C3C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // Auto-size columns
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Color-code kategori usia
        $rowCount = count($this->data);
        for ($i = 2; $i <= ($rowCount + 1); $i++) {
            $kategori = $sheet->getCell("G$i")->getValue();
            $color = match($kategori) {
                'Neonatus' => ['bg' => 'EDE9FE', 'font' => '6D28D9'],
                'Bayi'     => ['bg' => 'DBEAFE', 'font' => '1D4ED8'],
                'Anak'     => ['bg' => 'FEF3C7', 'font' => 'B45309'],
                default    => ['bg' => 'D1FAE5', 'font' => '065F46'],
            };

            $sheet->getStyle("G$i")->applyFromArray([
                'font' => ['color' => ['rgb' => $color['font']], 'bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $color['bg']]],
            ]);
        }

        return [];
    }
}
