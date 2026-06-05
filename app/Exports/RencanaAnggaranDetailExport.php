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

    public function __construct($data, $tgl_mulai, $tgl_selesai)
    {
        $this->data = $data;
        $this->tgl_mulai = $tgl_mulai;
        $this->tgl_selesai = $tgl_selesai;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Kode Barang',
            'Nama Barang',
            'Satuan',
            'Harga Barang',
            'Stok Awal',
            'Total Masuk',
            'Penerimaan Supplier',
            'Retur Pasien',
            'Mutasi Masuk',
            'Opname Lebih',
            'Lain-lain (Masuk)',
            'Resep Dokter (Draft Ref)',
            'Total Keluar',
            'Pemberian Obat',
            'Resep Pulang',
            'Detail Jual',
            'Stok Keluar',
            'Mutasi Keluar',
            'Hibah',
            'Retur Supplier',
            'Opname Kurang',
            'Pengambilan Medis',
            'Lain-lain (Keluar)',
            'Stok Akhir',
            'Buffer Stock 15%',
            'Rencana Pemakaian',
            'Rencana Pengadaan',
            'Rencana Anggaran',
        ];
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

        return [
            $row->kode_brng,
            strtoupper($row->nama_brng),
            $row->satuan,
            $harga_beli,
            $stok_awal,
            $penerimaan,
            (float) $row->pengadaan,
            (float) $row->retur_pasien,
            (float) $row->mutasi_masuk,
            (float) $row->opname_lebih,
            (float) $row->lain_lain_masuk,
            (float) $row->resep_dokter,
            $pemberian,
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
            $stok_akhir,
            $buffer_stock,
            $rencana_pemakaian,
            $rencana_pengadaan,
            $rencana_anggaran,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style Header - Green theme
        $sheet->getStyle('A1:AB1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '007C3C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Auto-fit columns A to AB
        foreach (range('A', 'Z') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->getColumnDimension('AA')->setAutoSize(true);
        $sheet->getColumnDimension('AB')->setAutoSize(true);

        // Format Columns
        $rowCount = count($this->data);
        if ($rowCount > 0) {
            $lastRow = $rowCount + 1;
            
            // Format numbers (Currency columns: D, AB)
            $sheet->getStyle("D2:D$lastRow")->getNumberFormat()->setFormatCode('"Rp" #,##0');
            $sheet->getStyle("AB2:AB$lastRow")->getNumberFormat()->setFormatCode('"Rp" #,##0');

            // Alignments & standard number format for other numeric fields
            foreach (['E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA'] as $col) {
                $sheet->getStyle("{$col}2:{$col}{$lastRow}")->getNumberFormat()->setFormatCode('#,##0');
            }

            // Alignments
            $sheet->getStyle("A2:A$lastRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("B2:B$lastRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("C2:C$lastRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D2:AB$lastRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        return [];
    }
}
