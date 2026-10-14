<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExtractionLog;
use App\Repositories\RekamMedisRepository;
use App\Repositories\SensusHarianRanapRepository;
use App\Exports\KunjunganRsExport;
use App\Exports\SensusHarianRanapExport;
use App\Exports\SensusHarianRanap2Export;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class RekamMedisController extends Controller
{
    protected $rekamMedisRepository;
    protected $sensusHarianRanapRepository;

    public function __construct(
        RekamMedisRepository $rekamMedisRepository,
        SensusHarianRanapRepository $sensusHarianRanapRepository
    ) {
        $this->rekamMedisRepository = $rekamMedisRepository;
        $this->sensusHarianRanapRepository = $sensusHarianRanapRepository;
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

    /**
     * Tampilan Menu Sensus Harian Rawat Inap
     */
    public function sensusHarianRanap(Request $request)
    {
        $bangsalList = $this->sensusHarianRanapRepository->getBangsal();

        $data = null;
        if ($request->has('bulan')) {
            $request->validate([
                'bulan' => 'required|date_format:Y-m',
            ]);

            // Pencatatan aktivitas ke ExtractionLog (SOP 5)
            ExtractionLog::create([
                'username'        => Auth::user()->username,
                'filter_date'     => $request->bulan . '-01',
                'extraction_type' => 'Sensus Harian Rawat Inap',
            ]);

            $data = $this->sensusHarianRanapRepository->getSensusHarianRanap(
                $request->bulan,
                $request->kd_bangsal
            );

            // Ambil detail data pasien untuk tabel detail
            $detailData = $this->sensusHarianRanapRepository->getDetailSensusPerHari(
                $request->bulan,
                $request->kd_bangsal
            );

            return view('rekam_medis.sensus_harian_ranap.index', compact(
                'data',
                'detailData',
                'bangsalList'
            ));
        }

        return view('rekam_medis.sensus_harian_ranap.index', compact(
            'data',
            'bangsalList'
        ));
    }

    /**
     * Ekspor Sensus Harian Rawat Inap ke Excel
     */
    public function sensusHarianRanapExportExcel(Request $request)
    {
        $request->validate([
            'bulan' => 'required|date_format:Y-m',
        ]);

        $data = $this->sensusHarianRanapRepository->getSensusHarianRanap(
            $request->bulan,
            $request->kd_bangsal
        );

        return Excel::download(
            new SensusHarianRanapExport($data, $request->bulan),
            'sensus-harian-ranap-' . $request->bulan . '.xlsx'
        );
    }

    /**
     * Ekspor Sensus Harian Rawat Inap ke PDF
     */
    public function sensusHarianRanapExportPdf(Request $request)
    {
        $request->validate([
            'bulan' => 'required|date_format:Y-m',
        ]);

        $data = $this->sensusHarianRanapRepository->getSensusHarianRanap(
            $request->bulan,
            $request->kd_bangsal
        );

        $pdf = Pdf::loadView('rekam_medis.sensus_harian_ranap.pdf', [
            'data'  => $data,
            'bulan' => $request->bulan,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('sensus-harian-ranap-' . $request->bulan . '.pdf');
    }

    /**
     * Tampilan Menu Sensus Harian Rawat Inap 2 (Rincian per Pasien)
     */
    public function sensusHarianRanap2(Request $request)
    {
        $bangsalList = $this->sensusHarianRanapRepository->getBangsal();

        $data = null;
        if ($request->has('bulan')) {
            $request->validate([
                'bulan' => 'required|date_format:Y-m',
            ]);

            // Pencatatan aktivitas ke ExtractionLog (SOP 5)
            ExtractionLog::create([
                'username'        => Auth::user()->username,
                'filter_date'     => $request->bulan . '-01',
                'extraction_type' => 'Sensus Harian Rawat Inap 2',
            ]);

            $data = $this->sensusHarianRanapRepository->getSensusHarianRanap2(
                $request->bulan,
                $request->kd_bangsal,
                $request->jenis_pelayanan
            );

            return view('rekam_medis.sensus_harian_ranap_2.index', compact(
                'data',
                'bangsalList'
            ));
        }

        return view('rekam_medis.sensus_harian_ranap_2.index', compact(
            'data',
            'bangsalList'
        ));
    }

    /**
     * Ekspor Sensus Harian Rawat Inap 2 ke Excel
     */
    public function sensusHarianRanap2ExportExcel(Request $request)
    {
        $request->validate([
            'bulan' => 'required|date_format:Y-m',
        ]);

        $data = $this->sensusHarianRanapRepository->getSensusHarianRanap2(
            $request->bulan,
            $request->kd_bangsal,
            $request->jenis_pelayanan
        );

        return Excel::download(
            new SensusHarianRanap2Export($data, $request->bulan),
            'sensus-harian-ranap-2-' . $request->bulan . '.xlsx'
        );
    }

    /**
     * Ekspor Sensus Harian Rawat Inap 2 ke PDF
     */
    public function sensusHarianRanap2ExportPdf(Request $request)
    {
        $request->validate([
            'bulan' => 'required|date_format:Y-m',
        ]);

        $data = $this->sensusHarianRanapRepository->getSensusHarianRanap2(
            $request->bulan,
            $request->kd_bangsal,
            $request->jenis_pelayanan
        );

        $pdf = Pdf::loadView('rekam_medis.sensus_harian_ranap_2.pdf', [
            'data'  => $data,
            'bulan' => $request->bulan,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('sensus-harian-ranap-2-' . $request->bulan . '.pdf');
    }
}
