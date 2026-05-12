<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ExtractionLog;
use App\Repositories\FarmasiRepository;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class PemberianObatController extends Controller
{
    protected $farmasiRepository;

    public function __construct(FarmasiRepository $farmasiRepository)
    {
        $this->farmasiRepository = $farmasiRepository;
    }

    public function index(Request $request)
    {
        $tgl_mulai = $request->tgl_mulai ?? date('Y-m-01');
        $tgl_selesai = $request->tgl_selesai ?? date('Y-m-t');

        // Check if user has actually requested a data pull
        if ($request->has('tgl_mulai')) {
            // Log the extraction
            ExtractionLog::create([
                'username' => Auth::user()->username,
                'filter_date' => $tgl_mulai,
                'extraction_type' => 'Pemberian Obat & BHP Unit Farmasi',
            ]);

            $sort = $request->get('sort', 'barang');
            $direction = $request->get('direction', 'asc');

            // Validate allowed sort columns
            $allowedSort = ['barang', 'jumlah'];
            if (!in_array($sort, $allowedSort)) {
                $sort = 'barang';
            }

            $baseQuery = $this->farmasiRepository->getPemberianQuery($tgl_mulai, $tgl_selesai);

            // Fetch details: per drug, per doctor
            $details = $this->farmasiRepository->getPemberianDetails($tgl_mulai, $tgl_selesai);

            // Top 5 items based on total quantity
            $top3 = (clone $baseQuery)
                ->orderBy('jumlah', 'desc')
                ->limit(5)
                ->get();

            // Paginated data for table, with dynamic sorting
            $data = (clone $baseQuery)
                ->orderBy($sort, $direction)
                ->paginate(20);

            $data->appends($request->all());
        } else {
            // Initial state: No data fetched
            $top3 = collect();
            $details = collect();
            $data = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
            $sort = 'barang';
            $direction = 'asc';
        }

        return view('farmasi.pemberian_obat.index', compact('data', 'tgl_mulai', 'tgl_selesai', 'top3', 'sort', 'direction', 'details'));
    }

    public function exportExcel(Request $request)
    {
        $tgl_mulai = $request->tgl_mulai ?? date('Y-m-01');
        $tgl_selesai = $request->tgl_selesai ?? date('Y-m-t');

        $data = $this->farmasiRepository->getPemberianDetails($tgl_mulai, $tgl_selesai)->flatten(1);

        return Excel::download(new \App\Exports\PenerimaanObatExport($data), 'rekap-pemberian-obat-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $tgl_mulai = $request->tgl_mulai ?? date('Y-m-01');
        $tgl_selesai = $request->tgl_selesai ?? date('Y-m-t');

        $data = $this->farmasiRepository->getPemberianDetails($tgl_mulai, $tgl_selesai)->flatten(1);

        $pdf = Pdf::loadView('farmasi.pemberian_obat.pdf', compact('data', 'tgl_mulai', 'tgl_selesai'));
        return $pdf->download('rekap-pemberian-obat-' . now()->format('Y-m-d') . '.pdf');
    }
}
