<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Carbon\Carbon;

class PelayananIbuHamilExport implements FromCollection, WithHeadings, WithMapping, WithStyles
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
            'NO',
            'NAMA PASIEN ( Ny. )',
            'NIK IBU HAMIL',
            'NAMA SUAMI ( Tn. )',
            'ALAMAT sesuai KTP dan Domisili',
            'UMUR IBU ( Th )',
            'HPHT',
            'USIA KEHAMILAN ( minggu )',
            'Kecil Masa Kehamilan (cm)',
            'Besar Masa Kehamilan',
            'DIAGNOSA ( G..P..A.. )',
            'Lingkar Lengan Atas (LiLA, ....cm)',
        ];
    }

    public function map($row): array
    {
        $this->no++;

        // 1. Nama Pasien
        $namaPasien = trim($row->nm_pasien ?? '');
        if ($namaPasien !== '' && !preg_match('/^(ny\.|ny\s)/i', $namaPasien)) {
            $namaPasien = 'NY. ' . strtoupper($namaPasien);
        } else {
            $namaPasien = strtoupper($namaPasien);
        }

        // 2. NIK
        $nik = $row->no_ktp ?: '-';

        // 3. Nama Suami
        $namaSuami = '-';
        if (!empty($row->keluarga) && strtoupper(trim($row->keluarga)) === 'SUAMI' && !empty($row->namakeluarga)) {
            $namaSuami = trim($row->namakeluarga);
        } else if (!empty($row->hubunganpj) && strtoupper(trim($row->hubunganpj)) === 'SUAMI' && !empty($row->p_jawab)) {
            $namaSuami = trim($row->p_jawab);
        }

        if ($namaSuami !== '-') {
            $namaSuami = strtoupper($namaSuami);
            if (!str_starts_with($namaSuami, 'TN.') && !str_starts_with($namaSuami, 'TN ')) {
                $namaSuami = 'TN. ' . $namaSuami;
            }
        }

        // 4. Alamat Lengkap
        $alamatParts = array_filter([
            $row->alamat,
            $row->nm_kel ? 'Kel. ' . $row->nm_kel : null,
            $row->nm_kec ? 'Kec. ' . $row->nm_kec : null,
            $row->nm_kab ? $row->nm_kab : null,
        ]);
        $alamatLengkap = !empty($alamatParts) ? implode(', ', $alamatParts) : ($row->alamat ?: '-');

        // 5. Umur Ibu
        $umur = '-';
        if (!empty($row->umurdaftar)) {
            $umur = $row->umurdaftar . ' TH';
        } elseif (!empty($row->tgl_lahir)) {
            $umur = Carbon::parse($row->tgl_lahir)->age . ' TH';
        }

        // 6. HPHT
        $hpht = '-';
        if (!empty($row->hpht) && $row->hpht !== '0000-00-00') {
            $hpht = Carbon::parse($row->hpht)->format('m/d/Y');
        }

        // 7. Usia Kehamilan
        $uk = '-';
        if (!empty($row->usia_kehamilan)) {
            $uk = trim($row->usia_kehamilan);
            if (!preg_match('/mgg|minggu/i', $uk)) {
                $uk .= ' MGG';
            }
        } elseif (!empty($row->hpht) && $row->hpht !== '0000-00-00') {
            $tglDaftar = Carbon::parse($row->tgl_registrasi);
            $tglHpht = Carbon::parse($row->hpht);
            $diffWeeks = floor($tglHpht->diffInDays($tglDaftar) / 7);
            if ($diffWeeks > 0 && $diffWeeks <= 45) {
                $uk = $diffWeeks . ' MGG';
            }
        }

        // 8. TFU (Kecil Masa Kehamilan & Besar Masa Kehamilan)
        $tfuRaw = $row->tfu_kebidanan ?: ($row->tfu_dokter ?: null);
        $kmk = '-';
        $bmk = '-';
        if ($tfuRaw) {
            $cleanTfu = preg_replace('/[^0-9.]/', '', $tfuRaw);
            if ($cleanTfu !== '') {
                $tfuValue = $cleanTfu . ' CM';
                preg_match('/(\d+)/', $uk, $matchesUk);
                $numericUk = !empty($matchesUk[1]) ? floatval($matchesUk[1]) : null;
                $numericTfu = floatval($cleanTfu);

                if ($numericUk && $numericTfu < ($numericUk - 3)) {
                    $kmk = $tfuValue;
                } else {
                    $bmk = $tfuValue;
                }
            } else {
                $bmk = $tfuRaw;
            }
        }

        // 9. Diagnosa (G..P..A..)
        $diagnosa = '-';
        $hasGpa = ($row->g !== null && $row->g !== '') || ($row->p !== null && $row->p !== '') || ($row->a !== null && $row->a !== '');
        if ($hasGpa) {
            $diagnosa = 'G' . ($row->g ?? '0') . 'P' . ($row->p ?? '0') . 'A' . ($row->a ?? '0');
            if ($uk !== '-') {
                $diagnosa .= ' UK ' . $uk;
            }
        } elseif (!empty($row->diagnosa_dokter)) {
            $diagnosa = $row->diagnosa_dokter;
        }

        // 10. LiLA
        $lila = '-';
        if (!empty($row->lila)) {
            $cleanLila = preg_replace('/[^0-9.]/', '', $row->lila);
            $lila = $cleanLila ? ($cleanLila . ' CM') : $row->lila;
        }

        return [
            $this->no,
            $namaPasien,
            $nik,
            $namaSuami,
            $alamatLengkap,
            $umur,
            $hpht,
            $uk,
            $kmk,
            $bmk,
            $diagnosa,
            $lila,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:L1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '007C3C']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ],
        ]);

        // Berikan warna kuning untuk header kolom 9 (Kecil Masa Kehamilan) dan 10 (Besar Masa Kehamilan) sesuai screenshot user
        $sheet->getStyle('I1:J1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '000000']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFF00']],
        ]);

        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [];
    }
}

