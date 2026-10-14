<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class SensusHarianRanap2Export implements FromArray, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    protected $data;
    protected $bulan;

    public function __construct($data, $bulan)
    {
        $this->data  = $data;
        $this->bulan = $bulan;
    }

    public function title(): string
    {
        return 'Sensus Harian Ranap 2';
    }

    public function headings(): array
    {
        return [
            'No',
            'No. Rawat',
            'No. RM',
            'Nama Pasien',
            'L/P',
            'Kamar / Bangsal',
            'Jenis Pelayanan',
            'Pasien Awal Bulan',
            'Pasien Masuk (Poli/Unit)',
            'Pasien Pindahan',
            'Pasien Dipindahkan',
            'Pasien Keluar Hidup',
            'Pria Mati >= 48 Jam',
            'Wanita Mati >= 48 Jam',
            'HP VVIP',
            'HP VIP',
            'HP Kelas 1',
            'HP Kelas 2',
            'HP Kelas 3',
            'HP Khusus',
            'Total HP',
            'Alokasi TT Bangsal',
        ];
    }

    public function array(): array
    {
        $rows = [];
        $no = 1;
        $patients = $this->data['patients'] ?? [];

        foreach ($patients as $p) {
            $rows[] = [
                $no++,
                $p['no_rawat'],
                $p['no_rkm_medis'],
                $p['nm_pasien'],
                $p['jk'],
                ($p['nm_bangsal'] ?? '-') . ' (' . ($p['kd_kamar'] ?? '-') . ')',
                $p['jenis_pelayanan'],
                $p['pasien_awal_bulan'],
                $p['pasien_masuk'],
                $p['pasien_pindahan'],
                $p['pasien_dipindahkan'],
                $p['pasien_keluar_hidup'],
                $p['mati_48_pria'],
                $p['mati_48_wanita'],
                $p['hp_vvip'],
                $p['hp_vip'],
                $p['hp_kelas_1'],
                $p['hp_kelas_2'],
                $p['hp_kelas_3'],
                $p['hp_khusus'],
                $p['total_hp'],
                $p['alokasi_tt'],
            ];
        }

        // Summary total row
        $summary = $this->data['summary'] ?? [];
        $rows[] = [
            'TOTAL',
            count($patients) . ' Pasien',
            '',
            '',
            '',
            '',
            'Umum: ' . ($summary['umum_count'] ?? 0) . ' | BPJS: ' . ($summary['bpjs_count'] ?? 0),
            $summary['pasien_awal_bulan'] ?? 0,
            $summary['pasien_masuk'] ?? 0,
            $summary['pasien_pindahan'] ?? 0,
            $summary['pasien_dipindahkan'] ?? 0,
            $summary['pasien_keluar_hidup'] ?? 0,
            $summary['mati_48_pria'] ?? 0,
            $summary['mati_48_wanita'] ?? 0,
            $summary['total_hp_vvip'] ?? 0,
            $summary['total_hp_vip'] ?? 0,
            $summary['total_hp_kelas_1'] ?? 0,
            $summary['total_hp_kelas_2'] ?? 0,
            $summary['total_hp_kelas_3'] ?? 0,
            $summary['total_hp_khusus'] ?? 0,
            $summary['total_hp_all'] ?? 0,
            'Total TT RS: ' . ($this->data['alokasi_tt_total'] ?? 0),
        ];

        return $rows;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,   // No
            'B' => 20,  // No Rawat
            'C' => 12,  // No RM
            'D' => 28,  // Nama
            'E' => 6,   // L/P
            'F' => 24,  // Kamar/Bangsal
            'G' => 16,  // Jenis Pelayanan
            'H' => 16,  // Pasien Awal Bulan
            'I' => 20,  // Pasien Masuk
            'J' => 15,  // Pindahan
            'K' => 16,  // Dipindahkan
            'L' => 16,  // Keluar Hidup
            'M' => 18,  // Pria Mati >=48
            'N' => 18,  // Wanita Mati >=48
            'O' => 10,  // HP VVIP
            'P' => 10,  // HP VIP
            'Q' => 12,  // HP K1
            'R' => 12,  // HP K2
            'S' => 12,  // HP K3
            'T' => 12,  // HP Khusus
            'U' => 12,  // Total HP
            'V' => 18,  // Alokasi TT
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $patientCount = count($this->data['patients'] ?? []);
        $totalRow = $patientCount + 2; // +1 heading, +1 1-indexed

        // Header style
        $sheet->getStyle('A1:V1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '007C3C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // All cells border
        $sheet->getStyle("A1:V{$totalRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D1D5DB'],
                ],
            ],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // Center alignments
        $sheet->getStyle("A2:A{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("C2:C{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("E2:E{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("G2:N{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("O2:V{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Total row style
        $sheet->getStyle("A{$totalRow}:V{$totalRow}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F2937']],
        ]);

        return [];
    }
}
