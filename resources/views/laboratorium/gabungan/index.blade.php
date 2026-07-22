@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-2xl font-bold text-gray-800 uppercase tracking-tighter italic">Waktu Tunggu Hasil Lab - Gabungan</h2>
                        <button type="button" onclick="openInfoModal()" class="text-primary hover:text-green-800 transition duration-150 focus:outline-none" title="Informasi Formula & Sumber Data">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </button>
                    </div>
                    <p class="text-gray-500 text-sm mt-1">Monitoring durasi penyelesaian hasil pemeriksaan laboratorium pasien ralan & ranap.</p>
                </div>
            </div>
            <div class="flex items-center gap-2 text-sm font-medium text-primary bg-primary/10 px-4 py-2 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                </svg>
                <span>Real-time Data</span>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <form action="{{ route('laboratorium.index_gabungan') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Tanggal Mulai</label>
                    <input type="date" name="tgl_mulai" value="{{ $tgl_mulai }}" 
                        class="w-full px-4 py-3 rounded-xl bg-gray-50 border-0 focus:bg-white focus:ring-4 focus:ring-primary/10 transition outline-none text-gray-800 shadow-inner text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Tanggal Selesai</label>
                    <input type="date" name="tgl_selesai" value="{{ $tgl_selesai }}" 
                        class="w-full px-4 py-3 rounded-xl bg-gray-50 border-0 focus:bg-white focus:ring-4 focus:ring-primary/10 transition outline-none text-gray-800 shadow-inner text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Jenis Bayar</label>
                    <select name="kd_pj" class="w-full px-4 py-3 rounded-xl bg-gray-50 border-0 focus:bg-white focus:ring-4 focus:ring-primary/10 transition outline-none text-gray-800 shadow-inner text-sm">
                        <option value="">-- Semua --</option>
                        @foreach($penjabs as $pj)
                            <option value="{{ $pj->kd_pj }}" {{ $kd_pj == $pj->kd_pj ? 'selected' : '' }}>{{ $pj->png_jawab }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Ketepatan</label>
                    <select name="ketepatan" class="w-full px-4 py-3 rounded-xl bg-gray-50 border-0 focus:bg-white focus:ring-4 focus:ring-primary/10 transition outline-none text-gray-800 shadow-inner text-sm">
                        <option value="">-- Semua --</option>
                        <option value="tepat" {{ $ketepatan == 'tepat' ? 'selected' : '' }}>Tepat Waktu</option>
                        <option value="tidak_tepat" {{ $ketepatan == 'tidak_tepat' ? 'selected' : '' }}>Tidak Sesuai</option>
                    </select>
                </div>
                <div>
                    <button type="submit" 
                        class="w-full bg-primary hover:bg-green-800 text-white font-black px-6 py-4 rounded-xl transition shadow-xl shadow-primary/20 uppercase tracking-widest text-[10px] flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd" />
                        </svg>
                        Filter
                    </button>
                </div>
            </form>
        </div>

        @if($data)
            <!-- Export Section -->
            <div class="flex items-center gap-4">
                <a href="javascript:void(0)" 
                    onclick="handleDownload('{{ route('laboratorium.export.excel', ['tgl_mulai' => $tgl_mulai, 'tgl_selesai' => $tgl_selesai, 'type' => 'gabungan', 'kd_pj' => $kd_pj, 'ketepatan' => $ketepatan]) }}', 'lab-gabungan-{{ $tgl_mulai }}-{{ $tgl_selesai }}.xlsx')"
                    class="bg-white border border-gray-200 text-gray-700 font-bold px-6 py-3 rounded-xl hover:bg-gray-50 transition shadow-sm flex items-center gap-2 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    Ekspor Excel
                </a>
                <a href="javascript:void(0)" 
                    onclick="handleDownload('{{ route('laboratorium.export.pdf', ['tgl_mulai' => $tgl_mulai, 'tgl_selesai' => $tgl_selesai, 'type' => 'gabungan', 'kd_pj' => $kd_pj, 'ketepatan' => $ketepatan]) }}', 'lab-gabungan-{{ $tgl_mulai }}-{{ $tgl_selesai }}.pdf')"
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
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100">
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">No</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Tanggal Sampel</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Nama Pasien</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Jenis Bayar</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Pemeriksaan</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">No. RM</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-center">Unit</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-center">Waktu Masuk & Hasil</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-center">Total Waktu Tunggu</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($data as $index => $item)
                                <tr class="hover:bg-gray-50/30 transition-colors">
                                    <td class="px-6 py-5 text-sm font-bold text-gray-300">
                                        {{ str_pad($data->firstItem() + $index, 2, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="text-sm font-black text-gray-700">
                                            {{ \Carbon\Carbon::parse($item->tgl_sampel)->format('d/m/Y') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="text-sm font-black text-gray-800 uppercase">{{ $item->nm_pasien }}</div>
                                        <div class="text-[10px] font-bold text-gray-400 mt-0.5 tracking-widest">{{ $item->no_rawat }}</div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="inline-flex items-center px-2 py-1 rounded text-[10px] font-black bg-blue-50 text-blue-600 tracking-tighter uppercase">
                                            {{ $item->png_jawab }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="text-[10px] font-medium text-gray-600 leading-relaxed max-w-[200px]">
                                            {{ $item->pemeriksaan }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-[10px] font-black bg-gray-100 text-gray-500 tracking-tighter">
                                            {{ $item->no_rkm_medis }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5">
                                        @if($item->status == 'ralan')
                                            <span class="inline-flex items-center px-2 py-1 rounded text-[10px] font-black bg-primary/10 text-primary tracking-widest">RALAN</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded text-[10px] font-black bg-indigo-100 text-indigo-600 tracking-widest">RANAP</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <div class="flex items-center justify-center gap-3">
                                            <div class="text-center">
                                                <div class="text-[10px] font-bold text-gray-300 uppercase tracking-widest mb-0.5">Sampel</div>
                                                <div class="text-sm font-black text-gray-600">{{ $item->jam_sampel }}</div>
                                            </div>
                                            <div class="h-8 w-px bg-gray-100"></div>
                                            <div class="text-center">
                                                <div class="text-[10px] font-bold text-gray-300 uppercase tracking-widest mb-0.5">Hasil</div>
                                                <div class="text-sm font-black text-gray-600">{{ $item->jam_hasil }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        @php
                                            $parts = explode(':', $item->total_waktu);
                                            $hours = (int)($parts[0] ?? 0);
                                            $minutes = (int)($parts[1] ?? 0);
                                            $seconds = (int)($parts[2] ?? 0);
                                            $totalSeconds = ($hours * 3600) + ($minutes * 60) + $seconds;
                                            
                                            $isTepatWaktu = $totalSeconds < 3600;
                                            $colorClass = $isTepatWaktu ? 'text-primary bg-primary/10' : 'text-red-600 bg-red-50';
                                        @endphp
                                        <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-black {{ $colorClass }} shadow-sm">
                                            @if($hours > 0) {{ $hours }}j @endif
                                            @if($minutes > 0) {{ $minutes }}m @endif
                                            {{ $seconds }}d
                                        </span>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        @if($isTepatWaktu)
                                            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-[10px] font-black bg-primary/10 text-primary uppercase tracking-widest border border-primary/20">TEPAT WAKTU</span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-[10px] font-black bg-red-100 text-red-600 uppercase tracking-widest border border-red-200">TIDAK SESUAI</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-6 py-20 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="p-6 bg-gray-50 rounded-full mb-4">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                            <p class="text-gray-400 font-bold uppercase tracking-widest text-xs">Data tidak ditemukan</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($data->hasPages())
                    <div class="px-6 py-6 border-t border-gray-50 bg-gray-50/20">
                        {{ $data->links() }}
                    </div>
                @endif
            </div>
        @else
            <div class="bg-white p-20 rounded-3xl shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center">
                <div class="p-6 bg-primary/5 rounded-full mb-8">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-primary/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-gray-800 mb-3 uppercase tracking-tighter italic">Pilih Periode Tanggal</h3>
                <p class="text-gray-400 text-sm max-w-sm font-medium">Silakan tentukan rentang tanggal untuk melihat statistik waktu tunggu laboratorium.</p>
            </div>
        @endif
    </div>

    <!-- Modal Informasi Waktu Tunggu Hasil Lab -->
    <div id="infoModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Overlay -->
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeInfoModal()"></div>

            <!-- Center modal content -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-100">
                <!-- Header -->
                <div class="bg-primary px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-2 text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="text-lg font-black uppercase tracking-wider">Informasi Waktu Tunggu Hasil Lab</h3>
                    </div>
                    <button onclick="closeInfoModal()" class="text-white hover:text-gray-200 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Content -->
                <div class="p-6 space-y-6 max-h-[70vh] overflow-y-auto">
                    <div>
                        <h4 class="text-sm font-black text-gray-800 uppercase tracking-wider mb-2 border-b pb-1">1. Deskripsi Menu</h4>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            Menu **Waktu Tunggu Hasil Lab - Gabungan** digunakan untuk mengukur dan memonitor durasi waktu penyelesaian (Turn Around Time / TAT) pemeriksaan laboratorium bagi seluruh pasien rumah sakit, baik rawat jalan maupun rawat inap. Pengukuran dihitung mulai dari waktu sampel diambil hingga jam hasil laboratorium selesai divalidasi dan diunggah.
                        </p>
                    </div>

                    <div>
                        <h4 class="text-sm font-black text-gray-800 uppercase tracking-wider mb-2 border-b pb-1">2. Aturan & Rumus Kalkulasi</h4>
                        <ul class="list-disc pl-5 space-y-4 text-sm text-gray-600">
                            <li>
                                <strong>Waktu Tunggu Lab (Durasi):</strong> Selisih waktu antara waktu penyerahan hasil laboratorium dengan waktu pengambilan sampel.
                                <div class="mt-1.5 bg-gray-50 p-2.5 rounded-xl border border-gray-100 font-mono text-[10px] text-gray-700 shadow-inner">
                                    <strong class="text-primary text-[11px]">Formula Matematika:</strong> Durasi = Waktu Hasil Keluar - Waktu Sampel<br>
                                    <strong class="text-primary text-[11px]">Formula Excel:</strong> <code class="text-red-600 font-bold">=(F2+G2)-(D2+E2)</code> (Di mana kolom F & G merupakan tanggal & jam hasil, dan kolom D & E merupakan tanggal & jam sampel).
                                </div>
                            </li>
                            <li>
                                <strong>Klasifikasi Kecepatan (Color-Coding):</strong>
                                <div class="mt-2 grid grid-cols-1 md:grid-cols-3 gap-2">
                                    <div class="bg-green-50 p-3 rounded-xl border border-green-200">
                                        <div class="text-green-800 font-black text-xs">HIJAU (CEPAT)</div>
                                        <div class="text-[10px] text-green-600 font-medium">Durasi &lt; 30 Menit</div>
                                    </div>
                                    <div class="bg-orange-50 p-3 rounded-xl border border-orange-200">
                                        <div class="text-orange-800 font-black text-xs">KUNING (SEDANG)</div>
                                        <div class="text-[10px] text-orange-600 font-medium">Durasi 30 s/d 59 Menit</div>
                                    </div>
                                    <div class="bg-red-50 p-3 rounded-xl border border-red-200">
                                        <div class="text-red-800 font-black text-xs">MERAH (LAMBAT)</div>
                                        <div class="text-[10px] text-red-600 font-medium">Durasi &ge; 60 Menit</div>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <strong>Status Ketepatan:</strong> Berdasarkan SPM (Standar Pelayanan Minimal) laboratorium, penyelesaian hasil laboratorium dianggap **Tepat Waktu** jika total durasi kurang dari **60 menit**, dan dianggap **Tidak Sesuai** jika durasi mencapai atau lebih dari **60 menit**.
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="text-sm font-black text-gray-800 uppercase tracking-wider mb-2 border-b pb-1">3. Pemetaan Basis Data (SIMRS Khanza)</h4>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs border border-gray-100">
                                <thead>
                                    <tr class="bg-gray-50 border-b border-gray-100">
                                        <th class="p-2 font-bold text-gray-500 uppercase">Kolom UI</th>
                                        <th class="p-2 font-bold text-gray-500 uppercase">Nama Tabel</th>
                                        <th class="p-2 font-bold text-gray-500 uppercase">Nama Kolom</th>
                                        <th class="p-2 font-bold text-gray-500 uppercase">Keterangan / Kondisi SQL</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    <tr>
                                        <td class="p-2 font-semibold text-gray-700">No. Order / RM</td>
                                        <td class="p-2 font-mono text-primary">permintaan_lab</td>
                                        <td class="p-2 font-mono text-primary">noorder, no_rawat</td>
                                        <td class="p-2 text-gray-600">Nomor order unik permintaan lab dan nomor perawatan pasien.</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold text-gray-700">Nama Pasien</td>
                                        <td class="p-2 font-mono text-primary">pasien</td>
                                        <td class="p-2 font-mono text-primary">nm_pasien</td>
                                        <td class="p-2 text-gray-600">Nama lengkap pasien.</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold text-gray-700">Unit</td>
                                        <td class="p-2 font-mono text-primary">permintaan_lab</td>
                                        <td class="p-2 font-mono text-primary">status</td>
                                        <td class="p-2 text-gray-600">Status rujukan asal pasien (ralan / ranap).</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold text-gray-700">Waktu Sampel</td>
                                        <td class="p-2 font-mono text-primary">permintaan_lab</td>
                                        <td class="p-2 font-mono text-primary">tgl_sampel, jam_sampel</td>
                                        <td class="p-2 text-gray-600">Waktu saat sampel laboratorium diambil.</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold text-gray-700">Waktu Hasil</td>
                                        <td class="p-2 font-mono text-primary">permintaan_lab</td>
                                        <td class="p-2 font-mono text-primary">tgl_hasil, jam_hasil</td>
                                        <td class="p-2 text-gray-600">Waktu saat hasil pemeriksaan laboratorium selesai divalidasi.</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold text-gray-700">Pemeriksaan</td>
                                        <td class="p-2 font-mono text-primary">jns_perawatan_lab</td>
                                        <td class="p-2 font-mono text-primary">nm_perawatan</td>
                                        <td class="p-2 text-gray-600">Nama jenis pemeriksaan laboratorium yang diajukan (diakumulasikan via GROUP_CONCAT).</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
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
