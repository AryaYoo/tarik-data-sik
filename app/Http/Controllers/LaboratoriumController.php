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

    public function kategoriPasien(Request $request)
    {
        $tgl_mulai     = $request->tgl_mulai   ?? date('Y-m-01');
        $tgl_selesai   = $request->tgl_selesai ?? date('Y-m-t');
        $kd_pj         = $request->kd_pj;
        $kategori_usia = $request->kategori_usia;

        // Ambil filter kode periksa dari session (jika user pernah custom setting)
        $session_kode      = session('lab_kategori_selected_kode', null);
        $filter_kode_aktif = !is_null($session_kode);
        $kode_periksa_list = $session_kode;

        $penjabs        = DB::table('penjab')->select('kd_pj', 'png_jawab')->orderBy('png_jawab', 'asc')->get();
        $availableKode  = $this->labRepository->getAvailableKodePeriksa();

        $data    = null;
        $summary = [];

        if ($request->has('tgl_mulai')) {
            ExtractionLog::create([
                'username'        => Auth::user()->username ?? Auth::user()->name,
                'filter_date'     => $tgl_mulai,
                'extraction_type' => 'Lab Kategori Pasien',
            ]);

            $data = $this->labRepository
                ->getKategoriPasienQuery($tgl_mulai, $tgl_selesai, $kd_pj, $kategori_usia, $kode_periksa_list)
                ->paginate(20);
            $data->appends($request->except('kode_periksa'));

            // Hitung summary per kategori usia (tanpa pagination, pasien unik dihitung berdasarkan No. RM atau Nama)
            $allData = $this->labRepository
                ->getKategoriPasienQuery($tgl_mulai, $tgl_selesai, $kd_pj, null, $kode_periksa_list)
                ->get();

            $summary = $this->labRepository->calculateKategoriPasienSummary($allData);
        }

        return view('laboratorium.kategori_pasien.index', compact(
            'data', 'tgl_mulai', 'tgl_selesai', 'penjabs', 'kd_pj', 'kategori_usia', 'summary',
            'availableKode', 'filter_kode_aktif', 'session_kode'
        ));
    }

    /**
     * AJAX: Kembalikan semua kode periksa yang tersedia (untuk inisialisasi modal setting).
     */
    public function kategoriPasienAvailableKode()
    {
        $kode = $this->labRepository->getAvailableKodePeriksa();
        return response()->json($kode);
    }

    /**
     * AJAX POST: Simpan filter kode periksa ke dalam session
     */
    public function kategoriPasienSaveSettings(Request $request)
    {
        $kode = $request->input('kode', []);
        session(['lab_kategori_selected_kode' => $kode]);
        return response()->json(['success' => true, 'count' => count($kode)]);
    }

    /**
     * AJAX POST: Reset filter kode periksa ke default (hapus dari session)
     */
    public function kategoriPasienResetSettings(Request $request)
    {
        session()->forget('lab_kategori_selected_kode');
        return response()->json(['success' => true]);
    }

    public function kategoriPasienExportExcel(Request $request)
    {
        $tgl_mulai     = $request->tgl_mulai;
        $tgl_selesai   = $request->tgl_selesai;
        $kd_pj         = $request->kd_pj;
        $kategori_usia = $request->kategori_usia;

        // Ambil kode_periksa_list dari session
        $kode_periksa_list = session('lab_kategori_selected_kode', null);

        $data = $this->labRepository
            ->getKategoriPasienQuery($tgl_mulai, $tgl_selesai, $kd_pj, $kategori_usia, $kode_periksa_list)
            ->get();

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\KategoriPasienLabExport($data, $tgl_mulai, $tgl_selesai),
            'kategori-pasien-lab-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function kategoriPasienExportPdf(Request $request)
    {
        $tgl_mulai     = $request->tgl_mulai;
        $tgl_selesai   = $request->tgl_selesai;
        $kd_pj         = $request->kd_pj;
        $kategori_usia = $request->kategori_usia;

        // Ambil kode_periksa_list dari session
        $kode_periksa_list = session('lab_kategori_selected_kode', null);

        $data = $this->labRepository
            ->getKategoriPasienQuery($tgl_mulai, $tgl_selesai, $kd_pj, $kategori_usia, $kode_periksa_list)
            ->get();

        // Hitung summary untuk PDF (pasien unik dihitung berdasarkan No. RM atau Nama)
        $summary = $this->labRepository->calculateKategoriPasienSummary($data);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'laboratorium.kategori_pasien.pdf',
            compact('data', 'tgl_mulai', 'tgl_selesai', 'summary')
        )->setPaper('a4', 'landscape');

        return $pdf->download('kategori-pasien-lab-' . now()->format('Y-m-d') . '.pdf');
    }
}
