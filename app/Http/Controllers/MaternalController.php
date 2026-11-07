<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExtractionLog;
use App\Repositories\MaternalRepository;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class MaternalController extends Controller
{
    protected $maternalRepository;

    public function __construct(MaternalRepository $maternalRepository)
    {
        $this->maternalRepository = $maternalRepository;
    }

    public function index(Request $request)
    {
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
                'extraction_type' => 'Kesehatan Ibu Maternal',
            ]);

            $data = $this->maternalRepository->getLaporanMaternal(
                $request->tgl_mulai,
                $request->tgl_selesai
            );
        }

        return view('rawat_jalan.kesehatan_ibu_maternal.index', compact('data'));
    }

    public function exportExcel(Request $request)
    {
        $data = $this->maternalRepository->getLaporanMaternal(
            $request->tgl_mulai,
            $request->tgl_selesai
        );

        return Excel::download(
            new \App\Exports\MaternalExport($data, $request->tgl_mulai, $request->tgl_selesai),
            'laporan-maternal-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function exportPdf(Request $request)
    {
        $data = $this->maternalRepository->getLaporanMaternal(
            $request->tgl_mulai,
            $request->tgl_selesai
        );

        $tglMulai = $request->tgl_mulai;
        $tglSelesai = $request->tgl_selesai;

        $pdf = Pdf::loadView('rawat_jalan.kesehatan_ibu_maternal.pdf', compact('data', 'tglMulai', 'tglSelesai'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('laporan-maternal-' . now()->format('Y-m-d') . '.pdf');
    }
}

