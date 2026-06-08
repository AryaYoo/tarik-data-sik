<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class RencanaAnggaranDetailExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $data;
    protected $tgl_mulai;
    protected $tgl_selesai;
    protected $show_detail;

    public function __construct($data, $tgl_mulai, $tgl_selesai, $show_detail = true)
    {
        $this->data = $data;
        $this->tgl_mulai = $tgl_mulai;
        $this->tgl_selesai = $tgl_selesai;
        $this->show_detail = $show_detail;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        $headers = [
            'Kode Barang',
            'Nama Barang',
            'Satuan',
            'Harga Barang',
            'Stok Awal',
            'Total Masuk',
        ];

        if ($this->show_detail) {
            $headers = array_merge($headers, [
                'Penerimaan Supplier',
                'Retur Pasien',
                'Mutasi Masuk',
                'Opname Lebih',
                'Lain-lain (Masuk)',
                'Resep Dokter (Draft Ref)'
            ]);
        }

        $headers[] = 'Total Keluar';

        if ($this->show_detail) {
            $headers = array_merge($headers, [
                'Pemberian Obat',
                'Resep Pulang',
                'Detail Jual',
                'Stok Keluar',
                'Mutasi Keluar',
                'Hibah',
                'Retur Supplier',
                'Opname Kurang',
                'Pengambilan Medis',
                'Lain-lain (Keluar)'
            ]);
        }

        $headers = array_merge($headers, [
            'Stok Akhir',
            'Buffer Stock 15%',
            'Rencana Pemakaian',
            'Rencana Pengadaan',
            'Rencana Anggaran',
        ]);

        return $headers;
    }

    public function map($row): array
    {
        $harga_beli = (float) $row->harga_beli;
        $stok_awal = (float) $row->stok_awal;
        $penerimaan = (float) $row->penerimaan;
        $pemberian = (float) $row->pemberian;

        $stok_akhir = $stok_awal + $penerimaan - $pemberian;
        $buffer_stock = floor($pemberian * 0.15);
        $rencana_pemakaian = $pemberian + $buffer_stock;
        
        $rencana_pengadaan = 0.0;
        if ($rencana_pemakaian > $stok_akhir) {
            $rencana_pengadaan = $rencana_pemakaian - $stok_akhir;
        }

        $rencana_anggaran = $rencana_pengadaan * $harga_beli;

        $mapped = [
            $row->kode_brng,
            strtoupper($row->nama_brng),
            $row->satuan,
            $harga_beli,
            $stok_awal,
            $penerimaan,
        ];

        if ($this->show_detail) {
            $mapped = array_merge($mapped, [
                (float) $row->pengadaan,
                (float) $row->retur_pasien,
                (float) $row->mutasi_masuk,
                (float) $row->opname_lebih,
                (float) $row->lain_lain_masuk,
                (float) $row->resep_dokter,
            ]);
        }

        $mapped[] = $pemberian;

        if ($this->show_detail) {
            $mapped = array_merge($mapped, [
                (float) $row->pemberian_obat,
                (float) $row->resep_pulang,
                (float) $row->detail_jual,
                (float) $row->stok_keluar,
                (float) $row->mutasi_keluar,
                (float) $row->hibah,
                (float) $row->retur_supplier,
                (float) $row->opname_kurang,
                (float) $row->pengambilan_medis,
                (float) $row->lain_lain_keluar,
            ]);
        }

        $mapped = array_merge($mapped, [
            $stok_akhir,
            $buffer_stock,
            $rencana_pemakaian,
            $rencana_pengadaan,
            $rencana_anggaran,
        ]);

        return $mapped;
    }

    public function styles(Worksheet $sheet)
    {
        $lastColIndex = count($this->headings());
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastColIndex);

        // Style Header - Green theme
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '007C3C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Auto-fit columns
        for ($i = 1; $i <= $lastColIndex; $i++) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        // Format Columns
        $rowCount = count($this->data);
        if ($rowCount > 0) {
            $lastRow = $rowCount + 1;
            
            // Alignments
            $sheet->getStyle("A2:A$lastRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("B2:B$lastRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("C2:C$lastRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D2:{$lastCol}{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            
            // Format numbers dynamically
            for ($i = 4; $i <= $lastColIndex; $i++) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
                
                // If it's the Harga Barang (col 4) or Rencana Anggaran (last col), apply Currency format
                if ($i === 4 || $i === $lastColIndex) {
                    $sheet->getStyle("{$colLetter}2:{$colLetter}{$lastRow}")->getNumberFormat()->setFormatCode('"Rp" #,##0');
                } else {
                    $sheet->getStyle("{$colLetter}2:{$colLetter}{$lastRow}")->getNumberFormat()->setFormatCode('#,##0');
                }
            }
        }

        return [];
    }
}
