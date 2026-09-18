<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExtractionLog;
use App\Repositories\BedahSentralRepository;
use App\Exports\DataOkNstExport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class BedahSentralController extends Controller
{
    protected $bedahSentralRepository;

    public function __construct(BedahSentralRepository $bedahSentralRepository)
    {
        $this->bedahSentralRepository = $bedahSentralRepository;
    }

    /**
     * Tampilan Menu Data OK NST Bedah Sentral
     */
    public function okNst(Request $request)
    {
        $data       = null;
        $nstSummary = collect();

        if ($request->has('tgl_mulai') && $request->has('tgl_selesai')) {
            $request->validate([
                'tgl_mulai'   => 'required|date',
                'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            ]);

            // Pencatatan aktivitas ke ExtractionLog (SOP 5)
            ExtractionLog::create([
                'username'        => Auth::user()->username,
                'filter_date'     => $request->tgl_mulai,
                'extraction_type' => 'Bedah Sentral - Data OK NST',
            ]);

            $data = $this->bedahSentralRepository->getDataOkNstQuery(
                $request->tgl_mulai,
                $request->tgl_selesai,
                $request->status_operasi,
                $request->get('jenis_nst', 'all')
            )->paginate(15);

            $data->appends($request->all());

            // Summary jumlah kasus per kategori NST (untuk card ringkasan)
            $nstSummary = $this->bedahSentralRepository->getDataOkNstSummary(
                $request->tgl_mulai,
                $request->tgl_selesai,
                $request->status_operasi
            );
        }

        return view('bedah_sentral.ok_nst.index', compact('data', 'nstSummary'));
    }

    /**
     * Ekspor Data OK NST ke Excel
     */
    public function okNstExportExcel(Request $request)
    {
        $request->validate([
            'tgl_mulai'   => 'required|date',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
        ]);

        $data = $this->bedahSentralRepository->getDataOkNstQuery(
            $request->tgl_mulai,
            $request->tgl_selesai,
            $request->status_operasi,
            $request->get('jenis_nst', 'all')
        )->get();

        return Excel::download(
            new DataOkNstExport($data),
            'data-ok-nst-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Ekspor Data OK NST ke PDF
     */
    public function okNstExportPdf(Request $request)
    {
        $request->validate([
            'tgl_mulai'   => 'required|date',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
        ]);

        $data = $this->bedahSentralRepository->getDataOkNstQuery(
            $request->tgl_mulai,
            $request->tgl_selesai,
            $request->status_operasi,
            $request->get('jenis_nst', 'all')
        )->limit(500)->get();

        $pdf = Pdf::loadView('bedah_sentral.ok_nst.pdf', [
            'data'           => $data,
            'tgl_mulai'      => $request->tgl_mulai,
            'tgl_selesai'    => $request->tgl_selesai,
            'status_operasi' => $request->status_operasi,
            'jenis_nst'      => $request->get('jenis_nst', 'all'),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('data-ok-nst-' . now()->format('Y-m-d') . '.pdf');
    }
}
