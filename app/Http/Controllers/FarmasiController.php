<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\FarmasiRepository;
use App\Models\ExtractionLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FarmasiController extends Controller
{
    protected $farmasiRepository;

    public function __construct(FarmasiRepository $farmasiRepository)
    {
        $this->farmasiRepository = $farmasiRepository;
    }

    public function index(Request $request)
    {
        $tgl_mulai = $request->tgl_mulai ?? date('Y-m-d');
        $tgl_selesai = $request->tgl_selesai ?? date('Y-m-d');
        $kd_pj = $request->kd_pj;
        $avgSeconds = null;

        $penjabs = DB::table('penjab')->select('kd_pj', 'png_jawab')->orderBy('png_jawab', 'asc')->get();

        $data = null;
        if ($request->has('tgl_mulai')) {
            // Log the extraction
            ExtractionLog::create([
                'username' => Auth::user()->username ?? Auth::user()->name,
                'filter_date' => $tgl_mulai,
                'extraction_type' => 'Waktu Tunggu Ralan Farmasi',
            ]);

            $data = $this->farmasiRepository->getWaktuTungguRalan($tgl_mulai, $tgl_selesai, $kd_pj)
                ->paginate(20)
                ->appends($request->all());
            
            $avgSeconds = $this->farmasiRepository->getAverageWaktuTunggu($tgl_mulai, $tgl_selesai, $kd_pj);
        }

        return view('farmasi.waktu_tunggu_ralan.index', compact('data', 'tgl_mulai', 'tgl_selesai', 'kd_pj', 'penjabs', 'avgSeconds'));
    }

    public function exportExcel(Request $request)
    {
        $tgl_mulai = $request->tgl_mulai;
        $tgl_selesai = $request->tgl_selesai;
        $kd_pj = $request->kd_pj;

        $data = $this->farmasiRepository->getWaktuTungguRalan($tgl_mulai, $tgl_selesai, $kd_pj)->get();
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\FarmasiExport($data), "waktu-tunggu-farmasi-$tgl_mulai-$tgl_selesai.xlsx");
    }

    public function exportPdf(Request $request)
    {
        $tgl_mulai = $request->tgl_mulai;
        $tgl_selesai = $request->tgl_selesai;
        $kd_pj = $request->kd_pj;

        $data = $this->farmasiRepository->getWaktuTungguRalan($tgl_mulai, $tgl_selesai, $kd_pj)->get();
        $avgSeconds = $this->farmasiRepository->getAverageWaktuTunggu($tgl_mulai, $tgl_selesai, $kd_pj);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('farmasi.waktu_tunggu_ralan.pdf', compact('data', 'tgl_mulai', 'tgl_selesai', 'avgSeconds'));
        return $pdf->download("waktu-tunggu-farmasi-$tgl_mulai-$tgl_selesai.pdf");
    }

    public function waktuTungguBpjs(Request $request)
    {
        $tgl_mulai = $request->tgl_mulai ?? date('Y-m-d');
        $tgl_selesai = $request->tgl_selesai ?? date('Y-m-d');
        $avgSeconds = null;

        $data = null;
        if ($request->has('tgl_mulai')) {
            // Log the extraction
            ExtractionLog::create([
                'username' => Auth::user()->username ?? Auth::user()->name,
                'filter_date' => $tgl_mulai,
                'extraction_type' => 'Waktu Tunggu Ralan Farmasi BPJS',
            ]);

            $data = $this->farmasiRepository->getWaktuTungguBpjs($tgl_mulai, $tgl_selesai)
                ->paginate(20)
                ->appends($request->all());
            
            $avgSeconds = $this->farmasiRepository->getAverageWaktuTungguBpjs($tgl_mulai, $tgl_selesai);
        }

        return view('farmasi.waktu_tunggu_bpjs.index', compact('data', 'tgl_mulai', 'tgl_selesai', 'avgSeconds'));
    }

    public function exportBpjsExcel(Request $request)
    {
        $tgl_mulai = $request->tgl_mulai;
        $tgl_selesai = $request->tgl_selesai;

        $data = $this->farmasiRepository->getWaktuTungguBpjs($tgl_mulai, $tgl_selesai)->get();
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\FarmasiExport($data), "waktu-tunggu-farmasi-bpjs-$tgl_mulai-$tgl_selesai.xlsx");
    }

    public function exportBpjsPdf(Request $request)
    {
        $tgl_mulai = $request->tgl_mulai;
        $tgl_selesai = $request->tgl_selesai;

        $data = $this->farmasiRepository->getWaktuTungguBpjs($tgl_mulai, $tgl_selesai)->get();
        $avgSeconds = $this->farmasiRepository->getAverageWaktuTungguBpjs($tgl_mulai, $tgl_selesai);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('farmasi.waktu_tunggu_bpjs.pdf', compact('data', 'tgl_mulai', 'tgl_selesai', 'avgSeconds'));
        return $pdf->download("waktu-tunggu-farmasi-bpjs-$tgl_mulai-$tgl_selesai.pdf");
    }

    public function sirkulasiIndex(Request $request)
    {
        $tgl_mulai = $request->tgl_mulai ?? date('Y-m-d');
        $tgl_selesai = $request->tgl_selesai ?? date('Y-m-d');

        $data = null;
        if ($request->has('tgl_mulai')) {
            // Log the extraction
            ExtractionLog::create([
                'username' => Auth::user()->username ?? Auth::user()->name,
                'filter_date' => $tgl_mulai,
                'extraction_type' => 'Perputaran Obat',
            ]);

            $data = $this->farmasiRepository->getSirkulasiObatQuery($tgl_mulai, $tgl_selesai)
                ->paginate(20)
                ->appends($request->all());
        }

        return view('farmasi.sirkulasi.index', compact('data', 'tgl_mulai', 'tgl_selesai'));
    }

    public function sirkulasiExportExcel(Request $request)
    {
        $tgl_mulai = $request->tgl_mulai;
        $tgl_selesai = $request->tgl_selesai;

        $data = $this->farmasiRepository->getSirkulasiObatQuery($tgl_mulai, $tgl_selesai)->get();
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\SirkulasiObatExport($data), "perputaran-obat-$tgl_mulai-$tgl_selesai.xlsx");
    }

    public function sirkulasiExportPdf(Request $request)
    {
        $tgl_mulai = $request->tgl_mulai;
        $tgl_selesai = $request->tgl_selesai;

        $data = $this->farmasiRepository->getSirkulasiObatQuery($tgl_mulai, $tgl_selesai)->get();
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('farmasi.sirkulasi.pdf', compact('data', 'tgl_mulai', 'tgl_selesai'));
        return $pdf->download("perputaran-obat-$tgl_mulai-$tgl_selesai.pdf");
     }

    public function opnameIndex(Request $request)
    {
        $tanggal = $request->tanggal ?? date('Y-m-d');
        $kd_bangsal = $request->kd_bangsal;

        // Fetch depots that have opname records
        $depots = $this->farmasiRepository->getOpnameDepots();

        $data = null;
        if ($request->has('tanggal')) {
            // Log the extraction
            ExtractionLog::create([
                'username' => Auth::user()->username ?? Auth::user()->name,
                'filter_date' => $tanggal,
                'extraction_type' => 'Stock Opname Farmasi',
            ]);

            $data = $this->farmasiRepository->getOpnameQuery($tanggal, $kd_bangsal)
                ->paginate(20)
                ->appends($request->all());
        }

        return view('farmasi.opname.index', compact('data', 'tanggal', 'kd_bangsal', 'depots'));
    }

    public function opnameExportExcel(Request $request)
    {
        $tanggal = $request->tanggal;
        $kd_bangsal = $request->kd_bangsal;

        $data = $this->farmasiRepository->getOpnameQuery($tanggal, $kd_bangsal)->get();
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\OpnameExport($data), "stock-opname-farmasi-$tanggal.xlsx");
    }

    public function opnameExportPdf(Request $request)
    {
        $tanggal = $request->tanggal;
        $kd_bangsal = $request->kd_bangsal;

        $data = $this->farmasiRepository->getOpnameQuery($tanggal, $kd_bangsal)->get();
        
        $nm_bangsal = 'Semua Depo';
        if ($kd_bangsal) {
            $bangsal = DB::table('bangsal')->where('kd_bangsal', $kd_bangsal)->first();
            if ($bangsal) {
                $nm_bangsal = $bangsal->nm_bangsal;
            }
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('farmasi.opname.pdf', compact('data', 'tanggal', 'nm_bangsal'));
        return $pdf->download("stock-opname-farmasi-$tanggal.pdf");
    }
}
