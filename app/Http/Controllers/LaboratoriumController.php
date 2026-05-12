<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\ExtractionLog;
use App\Repositories\LaboratoriumRepository;

class LaboratoriumController extends Controller
{
    protected $labRepository;

    public function __construct(LaboratoriumRepository $labRepository)
    {
        $this->labRepository = $labRepository;
    }

    public function index(Request $request)
    {
        $tgl_mulai = $request->tgl_mulai ?? date('Y-m-01');
        $tgl_selesai = $request->tgl_selesai ?? date('Y-m-t');
        $kd_pj = $request->kd_pj;
        $ketepatan = $request->ketepatan;

        $penjabs = DB::table('penjab')->select('kd_pj', 'png_jawab')->orderBy('png_jawab', 'asc')->get();

        $data = null;
        if ($request->has('tgl_mulai')) {
            // Log the extraction
            ExtractionLog::create([
                'username' => Auth::user()->username ?? Auth::user()->name,
                'filter_date' => $tgl_mulai,
                'extraction_type' => 'Lab Rawat Jalan',
            ]);

            $data = $this->labRepository->getLabQuery('ralan', $tgl_mulai, $tgl_selesai, $kd_pj, $ketepatan)->paginate(20);
            $data->appends($request->all());
        }

        return view('laboratorium.ralan.index', compact('data', 'tgl_mulai', 'tgl_selesai', 'penjabs', 'kd_pj', 'ketepatan'));
    }

    public function index_ranap(Request $request)
    {
        $tgl_mulai = $request->tgl_mulai ?? date('Y-m-01');
        $tgl_selesai = $request->tgl_selesai ?? date('Y-m-t');
        $kd_pj = $request->kd_pj;
        $ketepatan = $request->ketepatan;

        $penjabs = DB::table('penjab')->select('kd_pj', 'png_jawab')->orderBy('png_jawab', 'asc')->get();

        $data = null;
        if ($request->has('tgl_mulai')) {
            // Log the extraction
            ExtractionLog::create([
                'username' => Auth::user()->username ?? Auth::user()->name,
                'filter_date' => $tgl_mulai,
                'extraction_type' => 'Lab Rawat Inap',
            ]);

            $data = $this->labRepository->getLabQuery('ranap', $tgl_mulai, $tgl_selesai, $kd_pj, $ketepatan)->paginate(20);
            $data->appends($request->all());
        }

        return view('laboratorium.ranap.index', compact('data', 'tgl_mulai', 'tgl_selesai', 'penjabs', 'kd_pj', 'ketepatan'));
    }

    public function index_gabungan(Request $request)
    {
        $tgl_mulai = $request->tgl_mulai ?? date('Y-m-01');
        $tgl_selesai = $request->tgl_selesai ?? date('Y-m-t');
        $kd_pj = $request->kd_pj;
        $ketepatan = $request->ketepatan;

        $penjabs = DB::table('penjab')->select('kd_pj', 'png_jawab')->orderBy('png_jawab', 'asc')->get();

        $data = null;
        if ($request->has('tgl_mulai')) {
            // Log the extraction
            ExtractionLog::create([
                'username' => Auth::user()->username ?? Auth::user()->name,
                'filter_date' => $tgl_mulai,
                'extraction_type' => 'Lab Gabungan',
            ]);

            $data = $this->labRepository->getLabQuery('gabungan', $tgl_mulai, $tgl_selesai, $kd_pj, $ketepatan)->paginate(20);
            $data->appends($request->all());
        }

        return view('laboratorium.gabungan.index', compact('data', 'tgl_mulai', 'tgl_selesai', 'penjabs', 'kd_pj', 'ketepatan'));
    }

    public function exportExcel(Request $request)
    {
        $tgl_mulai = $request->tgl_mulai;
        $tgl_selesai = $request->tgl_selesai;
        $type = $request->type; // ralan, ranap, gabungan
        $kd_pj = $request->kd_pj;
        $ketepatan = $request->ketepatan;

        $data = $this->labRepository->getLabQuery($type, $tgl_mulai, $tgl_selesai, $kd_pj, $ketepatan)->get();

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\LaboratoriumExport($data, strtoupper($type)), 'waktu-tunggu-lab-' . $type . '-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $tgl_mulai = $request->tgl_mulai;
        $tgl_selesai = $request->tgl_selesai;
        $type = $request->type;
        $kd_pj = $request->kd_pj;
        $ketepatan = $request->ketepatan;

        $data = $this->labRepository->getLabQuery($type, $tgl_mulai, $tgl_selesai, $kd_pj, $ketepatan)->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView("laboratorium.$type.pdf", compact('data', 'tgl_mulai', 'tgl_selesai'));
        return $pdf->download('waktu-tunggu-lab-' . $type . '-' . now()->format('Y-m-d') . '.pdf');
    }
}
