<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExtractionLog;
use App\Repositories\RawatJalanRepository;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class RawatJalanController extends Controller
{
    protected $rawatJalanRepository;

    public function __construct(RawatJalanRepository $rawatJalanRepository)
    {
        $this->rawatJalanRepository = $rawatJalanRepository;
    }

    public function alamatDanKontak(Request $request)
    {
        $poliklinikList = $this->rawatJalanRepository->getPoliklinik();
        
        $data = null;
        if ($request->has('tgl_mulai') && $request->has('tgl_selesai')) {
            $request->validate([
                'tgl_mulai' => 'required|date',
                'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            ]);

            // Log the extraction
            ExtractionLog::create([
                'username' => Auth::user()->username,
                'filter_date' => $request->tgl_mulai,
                'extraction_type' => 'Alamat dan Kontak Rawat Jalan',
            ]);

            $data = $this->rawatJalanRepository->getAlamatDanKontak(
                $request->tgl_mulai, 
                $request->tgl_selesai, 
                $request->kd_poli
            )->paginate(10);
            
            $data->appends($request->all());
        }

        return view('rawat_jalan.alamat_dan_kontak.index', compact('data', 'poliklinikList'));
    }

    public function alamatDanKontakExportExcel(Request $request)
    {
        $data = $this->rawatJalanRepository->getAlamatDanKontak(
            $request->tgl_mulai, 
            $request->tgl_selesai, 
            $request->kd_poli
        )->get();

        return Excel::download(new \App\Exports\RawatJalanAlamatDanKontakExport($data), 'alamat-kontak-ralan-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function alamatDanKontakExportPdf(Request $request)
    {
        $data = $this->rawatJalanRepository->getAlamatDanKontak(
            $request->tgl_mulai, 
            $request->tgl_selesai, 
            $request->kd_poli
        )->get();

        $pdf = Pdf::loadView('rawat_jalan.alamat_dan_kontak.pdf', compact('data'));
        return $pdf->download('alamat-kontak-ralan-' . now()->format('Y-m-d') . '.pdf');
    }
}
