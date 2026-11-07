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

    public function pelayananIbuHamil(Request $request)
    {
        $poliklinikList = $this->rawatJalanRepository->getPoliklinik();

        $data = null;
        if ($request->has('tgl_mulai') && $request->has('tgl_selesai')) {
            $request->validate([
                'tgl_mulai' => 'required|date',
                'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            ]);

            // Log extraction sesuai SOP
            ExtractionLog::create([
                'username' => Auth::user()->username ?? 'system',
                'filter_date' => $request->tgl_mulai,
                'extraction_type' => 'Pelayanan Ibu Hamil Rawat Jalan',
            ]);

            $data = $this->rawatJalanRepository->getPelayananIbuHamil(
                $request->tgl_mulai,
                $request->tgl_selesai,
                $request->kd_poli
            )->paginate(15);

            $data->appends($request->all());
        }

        return view('rawat_jalan.pelayanan_ibu_hamil.index', compact('data', 'poliklinikList'));
    }

    public function pelayananIbuHamilExportExcel(Request $request)
    {
        $data = $this->rawatJalanRepository->getPelayananIbuHamil(
            $request->tgl_mulai,
            $request->tgl_selesai,
            $request->kd_poli
        )->get();

        return Excel::download(
            new \App\Exports\PelayananIbuHamilExport($data),
            'pelayanan-ibu-hamil-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function pelayananIbuHamilExportPdf(Request $request)
    {
        $data = $this->rawatJalanRepository->getPelayananIbuHamil(
            $request->tgl_mulai,
            $request->tgl_selesai,
            $request->kd_poli
        )->get();

        $pdf = Pdf::loadView('rawat_jalan.pelayanan_ibu_hamil.pdf', compact('data'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('pelayanan-ibu-hamil-' . now()->format('Y-m-d') . '.pdf');
    }
}
