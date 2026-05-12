<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ExtractionLog;
use App\Repositories\FarmasiRepository;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ExtractionRalanController extends Controller
{
    protected $farmasiRepository;

    public function __construct(FarmasiRepository $farmasiRepository)
    {
        $this->farmasiRepository = $farmasiRepository;
    }

    public function index()
    {
        return view('farmasi.ralan.index');
    }

    public function tarik(Request $request)
    {
        $request->validate([
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
        ]);

        // Log the extraction
        ExtractionLog::create([
            'username' => Auth::user()->username,
            'filter_date' => $request->tgl_mulai,
            'extraction_type' => 'Outpatient Real Data',
        ]);

        $data = $this->farmasiRepository->getRalanQuery($request->tgl_mulai, $request->tgl_selesai)->paginate(10);
        $data->appends($request->all());

        $obat = $this->farmasiRepository->getObatRalan(collect($data->items())->pluck('no_rawat'));

        return view('farmasi.ralan.index', compact('data', 'obat'));
    }

    public function exportExcel(Request $request)
    {
        $data = $this->farmasiRepository->getRalanQuery($request->tgl_mulai, $request->tgl_selesai)->get();
        $obat = $this->farmasiRepository->getObatRalan($data->pluck('no_rawat'));
        return Excel::download(new \App\Exports\ExtractionRalanExport($data, $obat), 'extraction-ralan-range-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $data = $this->farmasiRepository->getRalanQuery($request->tgl_mulai, $request->tgl_selesai)->get();
        $obat = $this->farmasiRepository->getObatRalan($data->pluck('no_rawat'));
        $pdf = Pdf::loadView('farmasi.ralan.pdf', compact('data', 'obat'));
        return $pdf->download('extraction-ralan-range-' . now()->format('Y-m-d') . '.pdf');
    }
}
