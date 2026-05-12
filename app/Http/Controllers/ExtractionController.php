<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ExtractionLog;
use App\Repositories\FarmasiRepository;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ExtractionController extends Controller
{
    protected $farmasiRepository;

    public function __construct(FarmasiRepository $farmasiRepository)
    {
        $this->farmasiRepository = $farmasiRepository;
    }

    public function index()
    {
        return view('farmasi.ranap.index');
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
            'extraction_type' => 'Inpatient Real Data',
        ]);

        $data = $this->farmasiRepository->getRanapQuery($request->tgl_mulai, $request->tgl_selesai)->paginate(10);
        $data->appends($request->all());

        $instruksi = $this->farmasiRepository->getInstruksi($data->getCollection()->pluck('no_rawat'));

        return view('farmasi.ranap.index', compact('data', 'instruksi'));
    }

    public function exportExcel(Request $request)
    {
        $data = $this->farmasiRepository->getRanapQuery($request->tgl_mulai, $request->tgl_selesai)->get();
        $instruksi = $this->farmasiRepository->getInstruksi($data->pluck('no_rawat'));
        return Excel::download(new \App\Exports\ExtractionExport($data, $instruksi), 'extraction-range-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $data = $this->farmasiRepository->getRanapQuery($request->tgl_mulai, $request->tgl_selesai)->get();
        $instruksi = $this->farmasiRepository->getInstruksi($data->pluck('no_rawat'));
        $pdf = Pdf::loadView('farmasi.ranap.pdf', compact('data', 'instruksi'));
        return $pdf->download('extraction-range-' . now()->format('Y-m-d') . '.pdf');
    }
}
