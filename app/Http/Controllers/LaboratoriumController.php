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
        $tgl_mulai   = $request->tgl_mulai   ?? date('Y-m-01');
        $tgl_selesai = $request->tgl_selesai ?? date('Y-m-t');
        $kd_pj       = $request->kd_pj;
        $status      = $request->status;
        $ketepatan   = $request->ketepatan;
        $search      = $request->search;

        // Ambil filter kode periksa dari session (jika user pernah custom setting)
        $session_kode = session('lab_gabungan_selected_kode', null);
        if (is_array($session_kode) && count($session_kode) === 0) {
            session()->forget('lab_gabungan_selected_kode');
            $session_kode = null;
        }

        $filter_kode_aktif = !is_null($session_kode);
        $kode_periksa_list = $session_kode;

        $penjabs       = DB::table('penjab')->select('kd_pj', 'png_jawab')->orderBy('png_jawab', 'asc')->get();
        $availableKode = $this->labRepository->getAvailableKodePeriksa();

        $data = null;
        $summary_gabungan = [];
        if ($request->has('tgl_mulai')) {
            // Log the extraction
            ExtractionLog::create([
                'username'        => Auth::user()->username ?? Auth::user()->name,
                'filter_date'     => $tgl_mulai,
                'extraction_type' => 'Lab Gabungan',
            ]);

            $data = $this->labRepository
                ->getLabGabunganQuery($tgl_mulai, $tgl_selesai, $kd_pj, $ketepatan, $kode_periksa_list, $status, $search)
                ->paginate(20);
            $data->appends($request->except('kode_periksa'));

            // Hitung summary indikator & statistik rata-rata & interval waktu
            $allRecords = $this->labRepository
                ->getLabGabunganQuery($tgl_mulai, $tgl_selesai, $kd_pj, null, $kode_periksa_list, $status, $search)
                ->get();

            $summary_gabungan = $this->calculateGabunganSummary($allRecords);
        }

        return view('laboratorium.gabungan.index', compact(
            'data', 'tgl_mulai', 'tgl_selesai', 'penjabs', 'kd_pj', 'ketepatan', 'status', 'search',
            'availableKode', 'filter_kode_aktif', 'session_kode', 'summary_gabungan'
        ));
    }

    /**
     * Hitung kalkulasi rata-rata & interval distribusi waktu tunggu gabungan
     */
    private function calculateGabunganSummary($allRecords)
    {
        $allRecords->transform(function ($item) {
            $tPermintaan = !empty($item->jam_permintaan_full) ? strtotime($item->jam_permintaan_full) : null;
            $tSampel     = !empty($item->jam_sampel_full) ? strtotime($item->jam_sampel_full) : null;
            $tHasil      = !empty($item->jam_hasil_full) ? strtotime($item->jam_hasil_full) : null;

            $item->ps_min = ($tSampel && $tPermintaan) ? round(($tSampel - $tPermintaan) / 60, 2) : 0;
            $item->sh_min = ($tHasil && $tSampel) ? round(($tHasil - $tSampel) / 60, 2) : 0;
            $item->ph_min = ($tHasil && $tPermintaan) ? round(($tHasil - $tPermintaan) / 60, 2) : 0;

            return $item;
        });

        $totalCount = $allRecords->count();
        $ps_avg     = $totalCount > 0 ? round($allRecords->avg('ps_min'), 2) : 0;
        $sh_avg     = $totalCount > 0 ? round($allRecords->avg('sh_min'), 2) : 0;
        $ph_avg     = $totalCount > 0 ? round($allRecords->avg('ph_min'), 2) : 0;

        $ranges = [
            [
                'label' => '0 - 15 Menit',
                'ps'    => $allRecords->filter(fn($i) => $i->ps_min <= 15)->count(),
                'sh'    => $allRecords->filter(fn($i) => $i->sh_min <= 15)->count(),
                'ph'    => $allRecords->filter(fn($i) => $i->ph_min <= 15)->count(),
            ],
            [
                'label' => '>15 - <=30 Menit',
                'ps'    => $allRecords->filter(fn($i) => $i->ps_min > 15 && $i->ps_min <= 30)->count(),
                'sh'    => $allRecords->filter(fn($i) => $i->sh_min > 15 && $i->sh_min <= 30)->count(),
                'ph'    => $allRecords->filter(fn($i) => $i->ph_min > 15 && $i->ph_min <= 30)->count(),
            ],
            [
                'label' => '>30 - <=60 Menit',
                'ps'    => $allRecords->filter(fn($i) => $i->ps_min > 30 && $i->ps_min <= 60)->count(),
                'sh'    => $allRecords->filter(fn($i) => $i->sh_min > 30 && $i->sh_min <= 60)->count(),
                'ph'    => $allRecords->filter(fn($i) => $i->ph_min > 30 && $i->ph_min <= 60)->count(),
            ],
            [
                'label' => '>60 - <=120 Menit',
                'ps'    => $allRecords->filter(fn($i) => $i->ps_min > 60 && $i->ps_min <= 120)->count(),
                'sh'    => $allRecords->filter(fn($i) => $i->sh_min > 60 && $i->sh_min <= 120)->count(),
                'ph'    => $allRecords->filter(fn($i) => $i->ph_min > 60 && $i->ph_min <= 120)->count(),
            ],
            [
                'label' => '>120 Menit',
                'ps'    => $allRecords->filter(fn($i) => $i->ps_min > 120)->count(),
                'sh'    => $allRecords->filter(fn($i) => $i->sh_min > 120)->count(),
                'ph'    => $allRecords->filter(fn($i) => $i->ph_min > 120)->count(),
            ],
        ];

        return [
            'total'       => $totalCount,
            'ps_avg'      => $ps_avg,
            'sh_avg'      => $sh_avg,
            'ph_avg'      => $ph_avg,
            'ranges'      => $ranges,
            'tepat'       => $allRecords->filter(fn($i) => $i->ph_min < 60)->count(),
            'tidak_tepat' => $allRecords->filter(fn($i) => $i->ph_min >= 60)->count(),
            'ralan'       => $allRecords->filter(fn($i) => strtolower($i->status) == 'ralan')->count(),
            'ranap'       => $allRecords->filter(fn($i) => strtolower($i->status) == 'ranap')->count(),
        ];
    }

    /**
     * AJAX POST: Simpan filter kode periksa gabungan ke dalam session
     */
    public function gabunganSaveSettings(Request $request)
    {
        $kode = $request->input('kode', []);
        if (empty($kode)) {
            session()->forget('lab_gabungan_selected_kode');
            return response()->json(['success' => true, 'count' => 0, 'reset' => true]);
        }
        session(['lab_gabungan_selected_kode' => $kode]);
        return response()->json(['success' => true, 'count' => count($kode)]);
    }

    /**
     * AJAX POST: Reset filter kode periksa gabungan ke default (hapus dari session)
     */
    public function gabunganResetSettings(Request $request)
    {
        session()->forget('lab_gabungan_selected_kode');
        return response()->json(['success' => true]);
    }

    public function exportExcel(Request $request)
    {
        $tgl_mulai   = $request->tgl_mulai;
        $tgl_selesai = $request->tgl_selesai;
        $type        = $request->type; // ralan, ranap, gabungan
        $kd_pj       = $request->kd_pj;
        $ketepatan   = $request->ketepatan;
        $status      = $request->status;
        $search      = $request->search;

        if ($type === 'gabungan') {
            $kode_periksa_list = session('lab_gabungan_selected_kode', null);
            $data = $this->labRepository
                ->getLabGabunganQuery($tgl_mulai, $tgl_selesai, $kd_pj, $ketepatan, $kode_periksa_list, $status, $search)
                ->get();
            $summary = $this->calculateGabunganSummary($data);

            return \Maatwebsite\Excel\Facades\Excel::download(
                new \App\Exports\WaktuTungguGabunganExport($data, $summary, $tgl_mulai, $tgl_selesai),
                'lama-pelayanan-lab-patologi-klinis-' . now()->format('Y-m-d') . '.xlsx'
            );
        }

        $data = $this->labRepository->getLabQuery($type, $tgl_mulai, $tgl_selesai, $kd_pj, $ketepatan)->get();

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\LaboratoriumExport($data, strtoupper($type)),
            'waktu-tunggu-lab-' . $type . '-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function exportPdf(Request $request)
    {
        $tgl_mulai   = $request->tgl_mulai;
        $tgl_selesai = $request->tgl_selesai;
        $type        = $request->type;
        $kd_pj       = $request->kd_pj;
        $ketepatan   = $request->ketepatan;
        $status      = $request->status;
        $search      = $request->search;

        if ($type === 'gabungan') {
            $kode_periksa_list = session('lab_gabungan_selected_kode', null);
            $data = $this->labRepository
                ->getLabGabunganQuery($tgl_mulai, $tgl_selesai, $kd_pj, $ketepatan, $kode_periksa_list, $status, $search)
                ->get();
            $summary = $this->calculateGabunganSummary($data);

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
                'laboratorium.gabungan.pdf',
                compact('data', 'summary', 'tgl_mulai', 'tgl_selesai')
            )->setPaper('a4', 'landscape');

            return $pdf->download('lama-pelayanan-lab-patologi-klinis-' . now()->format('Y-m-d') . '.pdf');
        }

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

    public function detailTindakan(Request $request)
    {
        $tgl_mulai   = $request->tgl_mulai   ?? date('Y-m-01');
        $tgl_selesai = $request->tgl_selesai ?? date('Y-m-t');
        $kd_pj       = $request->kd_pj;
        $status      = $request->status;
        $jk          = $request->jk;
        $search      = $request->search;

        $penjabs = DB::table('penjab')->select('kd_pj', 'png_jawab')->orderBy('png_jawab', 'asc')->get();

        $data    = null;
        $summary = [];

        if ($request->has('tgl_mulai')) {
            ExtractionLog::create([
                'username'        => Auth::user()->username ?? Auth::user()->name,
                'filter_date'     => $tgl_mulai,
                'extraction_type' => 'Lab Detail Tindakan',
            ]);

            $data = $this->labRepository
                ->getDetailTindakanQuery($tgl_mulai, $tgl_selesai, $kd_pj, $status, $search, $jk)
                ->paginate(20);
            $data->appends($request->all());

            // Summary metrics
            $allData = $this->labRepository
                ->getDetailTindakanQuery($tgl_mulai, $tgl_selesai, $kd_pj, $status, $search, $jk)
                ->get();

            $totalTindakan  = $allData->count();
            $ralanCount     = $allData->where('status', 'Ralan')->count();
            $ranapCount     = $allData->where('status', 'Ranap')->count();
            $lakiCount      = $allData->where('jk', 'L')->count();
            $perempuanCount = $allData->where('jk', 'P')->count();
            $uniquePatients = $allData->pluck('no_rkm_medis')->unique()->count();

            $summary = [
                'total'           => $totalTindakan,
                'ralan'           => $ralanCount,
                'ranap'           => $ranapCount,
                'laki'            => $lakiCount,
                'perempuan'       => $perempuanCount,
                'unique_patients' => $uniquePatients,
            ];
        }

        return view('laboratorium.detail_tindakan.index', compact(
            'data', 'tgl_mulai', 'tgl_selesai', 'penjabs', 'kd_pj', 'status', 'jk', 'search', 'summary'
        ));
    }

    public function detailTindakanExportExcel(Request $request)
    {
        $tgl_mulai   = $request->tgl_mulai   ?? date('Y-m-01');
        $tgl_selesai = $request->tgl_selesai ?? date('Y-m-t');
        $kd_pj       = $request->kd_pj;
        $status      = $request->status;
        $jk          = $request->jk;
        $search      = $request->search;

        $data = $this->labRepository
            ->getDetailTindakanQuery($tgl_mulai, $tgl_selesai, $kd_pj, $status, $search, $jk)
            ->get();

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\DetailTindakanLabExport($data, $tgl_mulai, $tgl_selesai),
            'detail-tindakan-lab-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function detailTindakanExportPdf(Request $request)
    {
        $tgl_mulai   = $request->tgl_mulai   ?? date('Y-m-01');
        $tgl_selesai = $request->tgl_selesai ?? date('Y-m-t');
        $kd_pj       = $request->kd_pj;
        $status      = $request->status;
        $jk          = $request->jk;
        $search      = $request->search;

        $data = $this->labRepository
            ->getDetailTindakanQuery($tgl_mulai, $tgl_selesai, $kd_pj, $status, $search, $jk)
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'laboratorium.detail_tindakan.pdf',
            compact('data', 'tgl_mulai', 'tgl_selesai')
        )->setPaper('a4', 'landscape');

        return $pdf->download('detail-tindakan-lab-' . now()->format('Y-m-d') . '.pdf');
    }
}

