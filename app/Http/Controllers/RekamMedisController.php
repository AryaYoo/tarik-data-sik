<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExtractionLog;
use App\Repositories\RekamMedisRepository;
use App\Exports\KunjunganRsExport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class RekamMedisController extends Controller
{
    protected $rekamMedisRepository;

    public function __construct(RekamMedisRepository $rekamMedisRepository)
    {
        $this->rekamMedisRepository = $rekamMedisRepository;
    }

    /**
     * Tampilan Menu Kunjungan RS
     */
    public function kunjunganRs(Request $request)
    {
        $poliklinikList = $this->rekamMedisRepository->getPoliklinik();
        $penjaminList   = $this->rekamMedisRepository->getPenjamin();

        $data = null;
        if ($request->has('tgl_mulai') && $request->has('tgl_selesai')) {
            $request->validate([
                'tgl_mulai'   => 'required|date',
                'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            ]);

            // Pencatatan aktivitas ke ExtractionLog (SOP 5)
            ExtractionLog::create([
                'username'        => Auth::user()->username,
                'filter_date'     => $request->tgl_mulai,
                'extraction_type' => 'Kunjungan RS',
            ]);

            $data = $this->rekamMedisRepository->getKunjunganRsQuery(
                $request->tgl_mulai,
                $request->tgl_selesai,
                $request->status_lanjut,
                $request->kd_poli,
                $request->kd_pj
            )->paginate(15);

            $data->appends($request->all());
        }

        return view('rekam_medis.kunjungan_rs.index', compact(
            'data',
            'poliklinikList',
            'penjaminList'
        ));
    }

    /**
     * Ekspor Data Kunjungan RS ke Excel
     */
    public function kunjunganRsExportExcel(Request $request)
    {
        $request->validate([
            'tgl_mulai'   => 'required|date',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
        ]);

        $data = $this->rekamMedisRepository->getKunjunganRsQuery(
            $request->tgl_mulai,
            $request->tgl_selesai,
            $request->status_lanjut,
            $request->kd_poli,
            $request->kd_pj
        )->get();

        return Excel::download(
            new KunjunganRsExport($data),
            'kunjungan-rs-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Ekspor Data Kunjungan RS ke PDF
     */
    public function kunjunganRsExportPdf(Request $request)
    {
        $request->validate([
            'tgl_mulai'   => 'required|date',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
        ]);

        $data = $this->rekamMedisRepository->getKunjunganRsQuery(
            $request->tgl_mulai,
            $request->tgl_selesai,
            $request->status_lanjut,
            $request->kd_poli,
            $request->kd_pj
        )->limit(500)->get();

        $pdf = Pdf::loadView('rekam_medis.kunjungan_rs.pdf', [
            'data'          => $data,
            'tgl_mulai'     => $request->tgl_mulai,
            'tgl_selesai'   => $request->tgl_selesai,
            'status_lanjut' => $request->status_lanjut,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('kunjungan-rs-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Tampilan Menu Kelengkapan ERM
     */
    public function kelengkapanErm(Request $request)
    {
        return view('rekam_medis.kelengkapan_erm.index');
    }
}
