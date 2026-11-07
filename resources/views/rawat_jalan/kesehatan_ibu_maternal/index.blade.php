@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-2xl font-bold text-gray-800 uppercase tracking-tighter italic">Laporan Kesehatan Ibu (Maternal)</h2>
                        <button type="button" onclick="openInfoModal()" class="text-primary hover:text-green-800 transition duration-150 focus:outline-none" title="Informasi Formula & Sumber Data">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </button>
                    </div>
                    <p class="text-gray-500 text-sm mt-1">Rekapitulasi agregat pelayanan maternal (K1, K4, skrining lab/gizi, risiko tinggi, komplikasi, persalinan, nifas, & mortalitas).</p>
                </div>
            </div>
            <div class="flex items-center gap-2 text-sm font-medium text-primary bg-primary/10 px-4 py-2 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                </svg>
                <span>Real-time Aggregate</span>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <form action="{{ route('rawat_jalan.kesehatan_ibu_maternal.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Tanggal Mulai</label>
                    <input type="date" name="tgl_mulai" value="{{ request('tgl_mulai', date('Y-m-01')) }}" 
                        class="w-full px-5 py-4 rounded-2xl bg-gray-50 border-0 focus:bg-white focus:ring-4 focus:ring-primary/10 transition outline-none text-gray-800 shadow-inner">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Tanggal Selesai</label>
                    <input type="date" name="tgl_selesai" value="{{ request('tgl_selesai', date('Y-m-t')) }}" 
                        class="w-full px-5 py-4 rounded-2xl bg-gray-50 border-0 focus:bg-white focus:ring-4 focus:ring-primary/10 transition outline-none text-gray-800 shadow-inner">
                </div>
                <div>
                    <button type="submit" 
                        class="w-full bg-primary hover:bg-green-800 text-white font-black px-10 py-5 rounded-2xl transition shadow-xl shadow-primary/20 uppercase tracking-widest text-xs flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd" />
                        </svg>
                        Tarik Data
                    </button>
                </div>
            </form>
        </div>

        @if(isset($data))
            <!-- Export Section -->
            <div class="flex items-center gap-4">
                <a href="#" onclick="handleDownload('{{ route('rawat_jalan.kesehatan_ibu_maternal.export.excel', request()->all()) }}', 'laporan_kesehatan_ibu_maternal_{{ date('Ymd_His') }}.xlsx'); return false;"
                    class="bg-white border border-gray-200 text-gray-700 font-bold px-6 py-3 rounded-xl hover:bg-gray-50 transition shadow-sm flex items-center gap-2 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    Ekspor Excel
                </a>
                <a href="#" onclick="handleDownload('{{ route('rawat_jalan.kesehatan_ibu_maternal.export.pdf', request()->all()) }}', 'laporan_kesehatan_ibu_maternal_{{ date('Ymd_His') }}.pdf'); return false;"
                    class="bg-white border border-gray-200 text-gray-700 font-bold px-6 py-3 rounded-xl hover:bg-gray-50 transition shadow-sm flex items-center gap-2 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    Ekspor PDF
                </a>
            </div>

            <!-- Table Card -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left whitespace-nowrap text-xs">
                        <thead>
                            <tr class="bg-primary text-white border-b border-primary">
                                <th class="px-4 py-3 font-bold text-center w-12 border-r border-white/20">NO</th>
                                <th class="px-6 py-3 font-bold uppercase tracking-wider border-r border-white/20">KEGIATAN</th>
                                <th class="px-6 py-3 font-bold uppercase tracking-wider text-center border-r border-white/20">Jumlah Dalam Wilayah Surabaya</th>
                                <th class="px-6 py-3 font-bold uppercase tracking-wider text-center border-r border-white/20">Jumlah Luar Wilayah Surabaya</th>
                                <th class="px-6 py-3 font-bold uppercase tracking-wider text-center border-r border-white/20">Jumlah Total</th>
                                <th class="px-4 py-3 font-bold uppercase tracking-wider text-center w-16">Ket</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <!-- Section Header A. MATERNAL -->
                            <tr class="bg-gray-100 font-black text-gray-800">
                                <td class="px-4 py-2.5 text-center font-bold">A.</td>
                                <td class="px-6 py-2.5 uppercase tracking-wider" colspan="5">MATERNAL</td>
                            </tr>

                            @php
                                $reportRows = [
                                    ['no' => '1', 'key' => 'bumil_baru_k1', 'label' => 'Jml.Bumil Baru (K1 Akses)', 'type' => 'normal'],
                                    ['no' => '2', 'key' => 'bumil_baru_k1_murni', 'label' => 'Jml.Bumil Baru < 12 mg. (K1 murni)', 'type' => 'normal'],
                                    ['no' => '3', 'key' => 'bumil_lama', 'label' => 'Jml.Bumil Lama', 'type' => 'normal'],
                                    ['no' => '4', 'key' => 'total_kunjungan', 'label' => 'Total Kunjungan ibu hamil', 'type' => 'normal'],
                                    ['no' => '5', 'key' => 'bumil_poedji_rochjati', 'label' => 'Jumlah Bumil di skor Poedji Rochjati', 'type' => 'normal'],
                                    ['no' => '6', 'key' => 'bumil_buku_kia', 'label' => 'Jml.Bumil mendapatkan buku KIA', 'type' => 'normal'],
                                    ['no' => '7', 'key' => 'bumil_k4', 'label' => 'Jml.Bumil dg. K4', 'type' => 'normal'],
                                    ['no' => '8', 'key' => 'bumil_fe1', 'label' => 'Jml. Bumil yang mendapatkan Fe I', 'type' => 'normal'],
                                    ['no' => '9', 'key' => 'bumil_fe3', 'label' => 'Jml. Bumil yang mendapatkan Fe III', 'type' => 'normal'],
                                    ['no' => '10', 'key' => 'bumil_periksa_hb', 'label' => 'Jml. Bumil yang diperiksa Hb', 'type' => 'normal'],
                                    ['no' => '11', 'key' => 'bumil_hb_8_11', 'label' => 'Jml.Bumil dg.Hb 8 - 11 mg/dl', 'type' => 'normal'],
                                    ['no' => '12', 'key' => 'bumil_hb_lt8', 'label' => 'Jml.Bumil dg.Hb < 8 mg/dl', 'type' => 'normal'],
                                    ['no' => '13', 'key' => 'bumil_lila', 'label' => 'Jml. Bumil yang diukur Lingkar Lengan Atas (LiLA)', 'type' => 'normal'],
                                    ['no' => '14', 'key' => 'bumil_periksa_protein_urine', 'label' => 'Jml. Bumil yang diperiksa Protein Urine', 'type' => 'normal'],
                                    ['no' => '15', 'key' => 'bumil_protein_urine_positif', 'label' => 'Jml. Bumil dengan hasil Protein Urine Positif', 'type' => 'normal'],
                                    ['no' => '16', 'key' => 'bumil_preeklampsia', 'label' => 'Jumlah Bumil yang dideteksi Pre Eklampsia', 'type' => 'normal'],
                                    ['no' => '17', 'key' => 'bumil_preeklampsia_positif', 'label' => 'Jumlah Bumil yang dideteksi Pre Eklampsia dengan hasil positif', 'type' => 'normal'],
                                    ['no' => '18', 'key' => 'bumil_preeklampsia_acetosal', 'label' => 'Jumlah Bumil positif Pre Eklampsia yang mendapat terapi Acetosal', 'type' => 'normal'],
                                    ['no' => '19', 'key' => 'bumil_usg_dopler', 'label' => 'Jumlah Bumil di USG Dopler', 'type' => 'normal'],
                                    ['no' => '20', 'key' => 'bumil_periksa_gula_darah', 'label' => 'Jml. Bumil yang diperiksa Gula Darah', 'type' => 'normal'],
                                    ['no' => '21', 'key' => 'bumil_gula_darah_tinggi', 'label' => 'Jml. Bumil dengan hasil Gula Darah > 140 g/dL', 'type' => 'normal'],
                                    ['no' => '22', 'key' => 'bumil_periksa_hepb', 'label' => 'Jml. Bumil yang diperiksa Hepatitis B', 'type' => 'normal'],
                                    ['no' => '23', 'key' => 'bumil_hepb_positif', 'label' => 'Jml. Bumil dengan Hepatitis B', 'type' => 'normal'],
                                    ['no' => '24', 'key' => 'bumil_risti', 'label' => 'Jml.Bumil dg. Risti', 'type' => 'normal'],
                                    ['no' => '25', 'key' => 'bumil_risti_ditangani', 'label' => 'Jml.Bumil Risti ditangani', 'type' => 'normal'],
                                    ['no' => '26', 'key' => 'bumil_risti_dirujuk', 'label' => 'Jml.Bumil risti dirujuk', 'type' => 'normal'],
                                    ['no' => '27', 'key' => 'bumil_kmk', 'label' => 'Jml. Ibu Hamil Kecil Masa Kehamilan', 'type' => 'highlight'],
                                    ['no' => '28', 'key' => 'bumil_bmk', 'label' => 'Jml. Ibu Hamil Besar Masa Kehamilan', 'type' => 'highlight'],
                                    ['no' => '29', 'key' => '__header_kasus', 'label' => 'Kasus maternal ditemukan', 'type' => 'header'],
                                    ['no' => '', 'key' => 'kasus_hyperemisis', 'label' => 'a. Hyperemisis', 'type' => 'sub'],
                                    ['no' => '', 'key' => 'kasus_keguguran', 'label' => 'b. Keguguran', 'type' => 'sub'],
                                    ['no' => '', 'key' => 'kasus_eklampsia', 'label' => 'c. Eklamsi/ pre eklamsi', 'type' => 'sub'],
                                    ['no' => '', 'key' => 'kasus_perdarahan_kehamilan', 'label' => 'd. Perdarahan kehamilan', 'type' => 'sub'],
                                    ['no' => '', 'key' => 'kasus_perdarahan_persalinan', 'label' => 'e. Perdarahan persalinan', 'type' => 'sub'],
                                    ['no' => '', 'key' => 'kasus_perdarahan_nifas', 'label' => 'f. Perdarahan nifas ( HPP )', 'type' => 'sub'],
                                    ['no' => '', 'key' => 'kasus_partus_lama', 'label' => 'g. Partus lama', 'type' => 'sub'],
                                    ['no' => '', 'key' => 'kasus_infeksi', 'label' => 'h. Infeksi', 'type' => 'sub'],
                                    ['no' => '', 'key' => 'kasus_tb', 'label' => 'i. TB', 'type' => 'sub'],
                                    ['no' => '', 'key' => 'kasus_malaria', 'label' => 'j. Malaria', 'type' => 'sub'],
                                    ['no' => '', 'key' => 'kasus_jantung', 'label' => 'k. Jantung', 'type' => 'sub'],
                                    ['no' => '', 'key' => 'kasus_kek', 'label' => 'l. Kurang energi kronis (KEK)', 'type' => 'sub'],
                                    ['no' => '', 'key' => 'kasus_hiv', 'label' => 'm. Bumil dg HIV (+)', 'type' => 'sub'],
                                    ['no' => '', 'key' => 'kasus_lain', 'label' => 'n. Lain-lain', 'type' => 'sub'],
                                    ['no' => '30', 'key' => 'bumil_ditawarkan_hiv_sifilis', 'label' => 'Jumlah Ibu Hamil yang ditawarkan tes HIV/ AIDS dan Sifilis', 'type' => 'normal'],
                                    ['no' => '31', 'key' => 'bumil_dirujuk_vct', 'label' => 'Jumlah Ibu Hamil yang dirujuk ke Klinik VCT', 'type' => 'normal'],
                                    ['no' => '32', 'key' => 'bumil_tes_hiv', 'label' => 'Jumlah Ibu Hamil yang melakukan tes HIV/ AIDS', 'type' => 'normal'],
                                    ['no' => '33', 'key' => 'bumil_periksa_sifilis', 'label' => 'Jumlah Ibu Hamil yang diperiksa Sifilis', 'type' => 'normal'],
                                    ['no' => '34', 'key' => 'bumil_sifilis', 'label' => 'Jumlah Ibu Hamil dengan Sifilis', 'type' => 'normal'],
                                    ['no' => '35', 'key' => 'persalinan_normal', 'label' => 'Persalinan normal', 'type' => 'normal'],
                                    ['no' => '36', 'key' => 'persalinan_imd', 'label' => 'Jumlah persalinan dengan IMD', 'type' => 'normal'],
                                    ['no' => '37', 'key' => 'persalinan_imd_berhasil', 'label' => 'Jumlah persalinan dengan IMD yang berhasil', 'type' => 'normal'],
                                    ['no' => '38', 'key' => 'persalinan_dirujuk_rs', 'label' => 'Persalinan dirujuk ke RS', 'type' => 'normal'],
                                    ['no' => '39', 'key' => 'persalinan_operasi', 'label' => 'Persalinan dengan operasi', 'type' => 'normal'],
                                    ['no' => '40', 'key' => '__header_tindakan', 'label' => 'Persalinan dengan tindakan', 'type' => 'header'],
                                    ['no' => '', 'key' => 'tindakan_drip', 'label' => 'a. Drip', 'type' => 'sub'],
                                    ['no' => '', 'key' => 'tindakan_vakum', 'label' => 'b. Vakum', 'type' => 'sub'],
                                    ['no' => '', 'key' => 'tindakan_curatage', 'label' => 'c. Curatage', 'type' => 'sub'],
                                    ['no' => '', 'key' => 'tindakan_placenta_manual', 'label' => 'd. Placenta manual', 'type' => 'sub'],
                                    ['no' => '41', 'key' => 'nifas_baru', 'label' => 'Jumlah Ibu Nifas Baru', 'type' => 'normal'],
                                    ['no' => '42', 'key' => 'total_kunjungan_nifas', 'label' => 'Total Kunjungan Ibu Nifas', 'type' => 'normal'],
                                    ['no' => '43', 'key' => 'nifas_dirujuk', 'label' => 'Jumlah Ibu Nifas dirujuk', 'type' => 'normal'],
                                    ['no' => '44', 'key' => 'nifas_hpp_ditangani', 'label' => 'Jumlah Ibu Nifas dengan HPP yang ditangani', 'type' => 'normal'],
                                    ['no' => '46', 'key' => 'nifas_vit_a', 'label' => 'Jml. Ibu nifas dapat Vit. A', 'type' => 'normal'],
                                    ['no' => '47', 'key' => 'ibu_meneteki_baru', 'label' => 'Jml. Ibu meneteki baru', 'type' => 'normal'],
                                    ['no' => '48', 'key' => 'total_kunj_ibu_meneteki', 'label' => 'Total kunj. Ibu meneteki', 'type' => 'normal'],
                                    ['no' => '49', 'key' => 'kematian_maternal', 'label' => 'Jml. Kematian Ibu maternal', 'type' => 'normal'],
                                    ['no' => '50', 'key' => '__header_kematian', 'label' => 'Penyebab kematian maternal', 'type' => 'header'],
                                    ['no' => '', 'key' => 'kematian_perdarahan', 'label' => 'a. Perdarahan', 'type' => 'sub'],
                                    ['no' => '', 'key' => 'kematian_infeksi_jalan_lahir', 'label' => 'b. Infeksi jalan lahir', 'type' => 'sub'],
                                    ['no' => '', 'key' => 'kematian_eklamsi', 'label' => 'c. Keracunan/ eklamsi', 'type' => 'sub'],
                                    ['no' => '', 'key' => 'kematian_lain', 'label' => 'd. Lain-lain', 'type' => 'sub'],
                                    ['no' => '51', 'key' => '__header_penolong', 'label' => 'Penolong Persalinan Terakhir dari kematian maternal', 'type' => 'header'],
                                    ['no' => '', 'key' => 'penolong_dokter_spog', 'label' => 'a. Dokter/ SpOG', 'type' => 'sub'],
                                    ['no' => '', 'key' => 'penolong_bidan', 'label' => 'b. Bidan', 'type' => 'sub'],
                                ];
                            @endphp

                            @foreach($reportRows as $row)
                                @php
                                    $isHeader = ($row['type'] === 'header');
                                    $isHighlight = ($row['type'] === 'highlight');
                                    $isSub = ($row['type'] === 'sub');

                                    $trClass = 'hover:bg-gray-50/50 transition-colors';
                                    if ($isHeader) {
                                        $trClass = 'bg-gray-100/80 font-bold text-gray-800';
                                    } elseif ($isHighlight) {
                                        $trClass = 'bg-amber-100/90 text-amber-950 font-semibold';
                                    }

                                    $key = $row['key'];
                                    $hasData = isset($data[$key]);
                                    $dalam = $hasData && $data[$key]['dalam'] > 0 ? $data[$key]['dalam'] : '';
                                    $luar = $hasData && $data[$key]['luar'] > 0 ? $data[$key]['luar'] : '';
                                    $total = $hasData ? $data[$key]['total'] : 0;
                                @endphp

                                <tr class="{{ $trClass }}">
                                    <td class="px-4 py-2.5 text-center font-bold {{ $isSub ? 'text-transparent' : 'text-gray-500' }}">
                                        {{ $row['no'] }}
                                    </td>
                                    <td class="px-6 py-2.5 {{ $isSub ? 'pl-10 text-gray-700' : 'font-medium text-gray-800' }}">
                                        {{ $row['label'] }}
                                    </td>
                                    @if($isHeader)
                                        <td class="px-6 py-2.5 text-center bg-gray-200/50" colspan="4"></td>
                                    @else
                                        <td class="px-6 py-2.5 text-center font-bold text-gray-800 {{ $isHighlight ? 'text-amber-950' : '' }}">
                                            {{ $dalam }}
                                        </td>
                                        <td class="px-6 py-2.5 text-center font-bold text-gray-800 {{ $isHighlight ? 'text-amber-950' : '' }}">
                                            {{ $luar }}
                                        </td>
                                        <td class="px-6 py-2.5 text-center font-black {{ $isHighlight ? 'text-amber-950' : 'text-primary' }}">
                                            {{ $total }}
                                        </td>
                                        <td class="px-4 py-2.5 text-center text-gray-400"></td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <!-- Empty State Before Filter -->
            <div class="bg-white p-20 rounded-3xl shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center">
                <div class="p-6 bg-primary/5 rounded-full mb-8">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-primary/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-gray-800 mb-3 uppercase tracking-tighter italic">Pilih Periode Laporan</h3>
                <p class="text-gray-400 text-sm max-w-md font-medium">Tentukan rentang tanggal untuk melakukan kalkulasi otomatis rekapitulasi data maternal (wilayah Surabaya vs luar Surabaya).</p>
            </div>
        @endif
    </div>

    <!-- Modal Informasi & Dokumentasi Sumber Data Sesuai SOP -->
    <div id="infoModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeInfoModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full border border-gray-100">
                <!-- Header Modal -->
                <div class="bg-primary px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-2 text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="text-lg font-black uppercase tracking-wider">Informasi Laporan Kesehatan Ibu (Maternal)</h3>
                    </div>
                    <button onclick="closeInfoModal()" class="text-white hover:text-gray-200 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Konten Modal -->
                <div class="p-6 space-y-6 max-h-[75vh] overflow-y-auto">
                    <!-- 1. Deskripsi Menu -->
                    <div>
                        <h4 class="text-sm font-black text-gray-800 uppercase tracking-wider mb-2 border-b pb-1">1. Deskripsi Fitur</h4>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            Modul ini mengekstraksi dan mengakumulasikan seluruh indikator kesehatan ibu (maternal) secara otomatis dari database SIMRS Khanza sesuai format pelaporan Dinas Kesehatan / Puskesmas. Data dipisahkan secara akurat menjadi <strong>Jumlah Dalam Wilayah Surabaya</strong> dan <strong>Jumlah Luar Wilayah Surabaya</strong> berdasarkan domisili kabupaten/kota pasien.
                        </p>
                    </div>

                    <!-- 2. Logika Agregasi & Formula -->
                    <div>
                        <h4 class="text-sm font-black text-gray-800 uppercase tracking-wider mb-2 border-b pb-1">2. Logika Agregasi & Pembagian Wilayah</h4>
                        <div class="space-y-3 text-xs text-gray-700 bg-gray-50 p-4 rounded-2xl border border-gray-100">
                            <div>
                                <span class="font-bold text-gray-900 block">Klasifikasi Wilayah:</span>
                                Pasien dikategorikan <em>Dalam Wilayah Surabaya</em> jika kolom <code class="bg-white px-1.5 py-0.5 rounded border">kabupaten.nm_kab LIKE '%Surabaya%'</code>. Selebihnya masuk ke <em>Luar Wilayah Surabaya</em>.
                            </div>
                            <div>
                                <span class="font-bold text-gray-900 block">K1 Murni vs K1 Akses:</span>
                                <em>K1 Akses</em>: Pasien status baru di poli kebidanan. <em>K1 Murni</em>: Pasien baru kebidanan dengan usia kehamilan &lt; 12 minggu.
                            </div>
                            <div>
                                <span class="font-bold text-gray-900 block">K4:</span>
                                Ibu hamil yang telah melakukan pemeriksaan antenatal minimal 4 kali pada periode terkait.
                            </div>
                            <div>
                                <span class="font-bold text-gray-900 block">KEK (Kurang Energi Kronis):</span>
                                Ibu hamil dengan ukuran Lingkar Lengan Atas (LiLA) &lt; 23.5 cm.
                            </div>
                            <div>
                                <span class="font-bold text-gray-900 block">KMK & BMK:</span>
                                Berdasarkan evaluasi Tinggi Fundus Uteri (TFU) terhadap usia kehamilan.
                            </div>
                        </div>
                    </div>

                    <!-- 3. Pemetaan Basis Data SIMRS Khanza -->
                    <div>
                        <h4 class="text-sm font-black text-gray-800 uppercase tracking-wider mb-2 border-b pb-1">3. Pemetaan Basis Data (SIMRS Khanza)</h4>
                        <div class="overflow-x-auto rounded-xl border border-gray-200">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="bg-gray-50 border-b border-gray-200">
                                        <th class="p-2.5 font-bold text-gray-600 uppercase">Indikator</th>
                                        <th class="p-2.5 font-bold text-gray-600 uppercase">Tabel Sumber SQL</th>
                                        <th class="p-2.5 font-bold text-gray-600 uppercase">Kriteria / Kolom Query</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-gray-700">
                                    <tr>
                                        <td class="p-2 font-semibold">Kunjungan Bumil (K1, Lama)</td>
                                        <td class="p-2 font-mono text-primary">reg_periksa, pasien, poliklinik</td>
                                        <td class="p-2 text-gray-600">status_poli = 'Baru' / 'Lama', poli kandungan/kebidanan</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold">LiLA & Usia Kehamilan</td>
                                        <td class="p-2 font-mono text-primary">penilaian_awal_keperawatan_kebidanan</td>
                                        <td class="p-2 text-gray-600">lila, usia_kehamilan, tfu</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold">Pemeriksaan Lab (Hb, Protein, GDA, HepB, HIV, Sifilis)</td>
                                        <td class="p-2 font-mono text-primary">periksa_lab, detail_periksa_lab, template_laboratorium</td>
                                        <td class="p-2 text-gray-600">Pemeriksaan LIKE '%Hb%', '%Protein%', '%HBsAg%', nilai</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold">Pemberian Obat (Fe, Acetosal)</td>
                                        <td class="p-2 font-mono text-primary">detail_pemberian_obat, databarang</td>
                                        <td class="p-2 text-gray-600">nama_brng LIKE '%Fe%', '%Acetosal%'</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold">Diagnosa Maternal (Risti, PE, HPP, Kasus)</td>
                                        <td class="p-2 font-mono text-primary">diagnosa_pasien, penyakit</td>
                                        <td class="p-2 text-gray-600">kd_penyakit kode ICD-10 (O14, O15, O72, O21, dsb)</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold">Persalinan & Tindakan Operasi</td>
                                        <td class="p-2 font-mono text-primary">kamar_inap, operasi, paket_operasi</td>
                                        <td class="p-2 text-gray-600">kategori = 'Kebidanan', nm_perawatan</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold">Kematian Maternal</td>
                                        <td class="p-2 font-mono text-primary">kamar_inap, diagnosa_pasien</td>
                                        <td class="p-2 text-gray-600">stts_pulang = 'Meninggal' dan diagnosa kasus kebidanan (O)</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Footer Modal -->
                <div class="bg-gray-50 px-6 py-4 flex justify-end">
                    <button onclick="closeInfoModal()" class="bg-primary hover:bg-green-800 text-white font-bold px-6 py-2 rounded-xl text-sm transition focus:outline-none">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openInfoModal() {
            document.getElementById('infoModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeInfoModal() {
            document.getElementById('infoModal').classList.add('hidden');
            document.body.style.overflow = '';
        }
    </script>
@endsection

