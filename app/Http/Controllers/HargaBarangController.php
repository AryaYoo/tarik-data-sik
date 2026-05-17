<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\FarmasiRepository;
use App\Models\ExtractionLog;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\HargaBarangExport;
use Barryvdh\DomPDF\Facade\Pdf;

class HargaBarangController extends Controller
{
    protected $farmasiRepository;

    public function __construct(FarmasiRepository $farmasiRepository)
    {
        $this->farmasiRepository = $farmasiRepository;
    }

    public function index(Request $request)
    {
        $search = $request->get('q');
        $selectedColumns = $request->get('columns', []);

        $columnMap = [
            'dasar' => 'Harga Dasar',
            'h_beli' => 'Harga Beli',
            'ralan' => 'Harga Ralan',
            'kelas1' => 'Kelas 1',
            'kelas2' => 'Kelas 2',
            'kelas3' => 'Kelas 3',
            'utama' => 'Utama',
            'vip' => 'VIP',
            'vvip' => 'VVIP',
            'beliluar' => 'Beli Luar',
            'jualbebas' => 'Jual Bebas',
            'karyawan' => 'Karyawan',
        ];

        $data = null;
        if ($request->has('tarik') && !empty($selectedColumns)) {
            // Log the extraction (SOP 5 Audit Trail)
            ExtractionLog::create([
                'username' => Auth::user()->username ?? Auth::user()->name,
                'filter_date' => now()->format('Y-m-d'),
                'extraction_type' => 'Item Price Data',
            ]);

            $data = $this->farmasiRepository->getHargaBarangQuery($search)
                ->paginate(15)
                ->appends($request->all());
        }

        return view('farmasi.harga.index', compact('data', 'search', 'selectedColumns', 'columnMap'));
    }

    public function exportExcel(Request $request)
    {
        $search = $request->get('q');
        $selectedColumns = $request->get('columns', []);

        if (empty($selectedColumns)) {
            return response()->json(['message' => 'Silakan pilih minimal 1 kolom harga untuk diekspor'], 400);
        }

        $data = $this->farmasiRepository->getHargaBarangQuery($search)->get();

        return Excel::download(new HargaBarangExport($data, $selectedColumns), 'harga-barang-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $search = $request->get('q');
        $selectedColumns = $request->get('columns', []);

        if (empty($selectedColumns)) {
            return response()->json(['message' => 'Silakan pilih minimal 1 kolom harga untuk diekspor'], 400);
        }

        $columnMap = [
            'dasar' => 'Harga Dasar',
            'h_beli' => 'Harga Beli',
            'ralan' => 'Harga Ralan',
            'kelas1' => 'Kelas 1',
            'kelas2' => 'Kelas 2',
            'kelas3' => 'Kelas 3',
            'utama' => 'Utama',
            'vip' => 'VIP',
            'vvip' => 'VVIP',
            'beliluar' => 'Beli Luar',
            'jualbebas' => 'Jual Bebas',
            'karyawan' => 'Karyawan',
        ];

        $data = $this->farmasiRepository->getHargaBarangQuery($search)->get();

        $pdf = Pdf::loadView('farmasi.harga.pdf', compact('data', 'selectedColumns', 'columnMap'))
            ->setPaper('a4', 'landscape'); // Landscape paper because there are many pricing columns possible

        return $pdf->download('harga-barang-' . now()->format('Y-m-d') . '.pdf');
    }
}
