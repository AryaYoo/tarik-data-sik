<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SirkulasiObatExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $data;

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
            'Resep Dokter',
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
        ];
    }

    public function map($row): array
    {
        return [
            $row->kode_brng,
            strtoupper($row->nama_brng),
            $row->satuan,
            $row->harga_beli,
            $row->stok_awal,
            $row->penerimaan,
            $row->pengadaan,
            $row->retur_pasien,
            $row->mutasi_masuk,
            $row->opname_lebih,
            $row->lain_lain_masuk,
            $row->resep_dokter,
            $row->distribusi,
            $row->pemberian_obat,
            $row->resep_pulang,
            $row->detail_jual,
            $row->stok_keluar,
            $row->mutasi_keluar,
            $row->hibah,
            $row->retur_supplier,
            $row->opname_kurang,
            $row->pengambilan_medis,
            $row->lain_lain_keluar,
            $row->stok_akhir,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:X1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '007C3C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        foreach (range('A', 'X') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $rowCount = count($this->data);
        if ($rowCount > 0) {
            $lastRow = $rowCount + 1;
            $sheet->getStyle("D2:D$lastRow")->getNumberFormat()->setFormatCode('"Rp" #,##0');
            $sheet->getStyle("E2:X$lastRow")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("A2:A$lastRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("B2:B$lastRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("C2:C$lastRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D2:X$lastRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        return [];
    }
}
