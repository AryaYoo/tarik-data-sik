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
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

class KategoriPasienLabExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $data;
    protected $tgl_mulai;
    protected $tgl_selesai;
    protected $mergeRanges = [];
    protected $totalPatients = 0;

    public function __construct($data, $tgl_mulai, $tgl_selesai)
    {
        $this->tgl_mulai   = $tgl_mulai;
        $this->tgl_selesai = $tgl_selesai;

        // Kelompokkan data per pasien berdasarkan No. RM atau Nama agar data pasien yang sama berada di baris berurutan
        $groupMap    = [];
        $nextGroupId = 1;

        $grouped = $data->groupBy(function ($item) use (&$groupMap, &$nextGroupId) {
            $rm   = trim((string) ($item->no_rkm_medis ?? ''));
            $nama = strtoupper(trim((string) ($item->nm_pasien ?? '')));

            $groupId = null;
            if ($rm !== '' && $rm !== '-' && isset($groupMap['rm:' . $rm])) {
                $groupId = $groupMap['rm:' . $rm];
            } elseif ($nama !== '' && isset($groupMap['nama:' . $nama])) {
                $groupId = $groupMap['nama:' . $nama];
            }

            if ($groupId === null) {
                $groupId = 'g_' . $nextGroupId++;
            }

            if ($rm !== '' && $rm !== '-') {
                $groupMap['rm:' . $rm] = $groupId;
            }
            if ($nama !== '') {
                $groupMap['nama:' . $nama] = $groupId;
            }

            return $groupId;
        });

        $flatList   = collect();
        $patientNo  = 1;
        $currentRow = 2; // Baris 1 adalah headings

        foreach ($grouped as $groupId => $items) {
            $count    = $items->count();
            $startRow = $currentRow;
            $endRow   = $currentRow + $count - 1;

            if ($count > 1) {
                $this->mergeRanges[] = [
                    'start' => $startRow,
                    'end'   => $endRow,
                ];
            }

            foreach ($items as $item) {
                $item->patient_no = $patientNo;
                $flatList->push($item);
                $currentRow++;
            }

            $patientNo++;
        }

        $this->totalPatients = $patientNo - 1;
        $this->data = $flatList;
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
            'No. RM',
            'Nama Pasien',
            'Tgl. Lahir',
            'Tgl. Pemeriksaan',
            'Umur',
            'Kategori Usia',
            'Jenis Pemeriksaan',
            'Jenis Bayar',
            'Status Kunjungan',
        ];
    }

    public function map($row): array
    {
        $umurTahun      = (int) $row->umur_tahun;
        $umurBulanTotal = (int) $row->umur_bulan_total;
        $bulanSisa      = $umurBulanTotal % 12;

        if ($umurTahun >= 1) {
            $umurTeks = $umurTahun . ' Th ' . $bulanSisa . ' Bln';
        } elseif ($umurBulanTotal >= 1) {
            $umurTeks = $umurBulanTotal . ' Bulan';
        } else {
            $hari     = Carbon::parse($row->tgl_lahir)->diffInDays(Carbon::parse($row->tgl_sampel));
            $umurTeks = $hari . ' Hari';
        }

        return [
            $row->patient_no,
            $row->no_rkm_medis,
            strtoupper($row->nm_pasien),
            $row->tgl_lahir ? Carbon::parse($row->tgl_lahir)->format('d/m/Y') : '-',
            Carbon::parse($row->tgl_sampel)->format('d/m/Y'),
            $umurTeks,
            $row->kategori_usia,
            $row->pemeriksaan ?: '-',
            $row->png_jawab,
            $row->status == 'ralan' ? 'Rawat Jalan' : 'Rawat Inap',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $rowCount  = count($this->data);
        $totalRows = $rowCount + 1;

        // Header styling - Olive Green
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '007C3C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        if ($rowCount > 0) {
            // Set vertical alignment and borders for all data cells
            $sheet->getStyle("A2:J{$totalRows}")->applyFromArray([
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => 'D1D5DB'],
                    ],
                ],
            ]);

            // Center-align specific columns
            $sheet->getStyle("A2:A{$totalRows}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B2:B{$totalRows}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D2:D{$totalRows}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E2:E{$totalRows}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("F2:F{$totalRows}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("G2:G{$totalRows}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("J2:J{$totalRows}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Merge cells for patients with multiple examinations (No, No. RM, Nama, Tgl. Lahir)
            foreach ($this->mergeRanges as $range) {
                $start = $range['start'];
                $end   = $range['end'];

                // Merge No (Col A)
                $sheet->mergeCells("A{$start}:A{$end}");
                // Merge No. RM (Col B)
                $sheet->mergeCells("B{$start}:B{$end}");
                // Merge Nama Pasien (Col C)
                $sheet->mergeCells("C{$start}:C{$end}");
                // Merge Tgl. Lahir (Col D)
                $sheet->mergeCells("D{$start}:D{$end}");
            }

            // Color-code kategori usia
            for ($i = 2; $i <= $totalRows; $i++) {
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

            // Baris total pasien unik di bagian bawah tabel
            $footerRow = $totalRows + 1;
            $sheet->mergeCells("A{$footerRow}:D{$footerRow}");
            $sheet->setCellValue("A{$footerRow}", "TOTAL: {$this->totalPatients} PASIEN (Dihitung Berdasarkan No. RM / Nama)");
            $sheet->getStyle("A{$footerRow}:J{$footerRow}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => '004D25'], 'size' => 10],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8F5E9']],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => 'A5D6A7'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);
        }

        // Auto-size columns
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [];
    }
}
