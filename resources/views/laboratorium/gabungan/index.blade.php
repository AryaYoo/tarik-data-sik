@extends('layouts.app')

@section('content')
    {{-- Alpine.js root — mengatur setting kode periksa via session --}}
    <div class="space-y-6"
         x-data="kodePeriksaSetting()"
         x-init="init()">

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
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-2 text-sm font-medium text-primary bg-primary/10 px-4 py-2 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                    </svg>
                    <span>Real-time Data</span>
                </div>

                {{-- Tombol Setting Kode Periksa (di sebelah kanan Real-time Data) --}}
                <div class="flex items-center gap-2">
                    <button type="button" @click="openSettingModal()" onclick="openSettingModal()"
                        class="flex items-center gap-2 text-xs font-bold px-3.5 py-2 rounded-lg border transition cursor-pointer shadow-sm hover:shadow"
                        :class="isCustomFilterActive
                            ? 'bg-amber-50 border-amber-300 text-amber-700 hover:bg-amber-100'
                            : 'bg-gray-50 border-gray-200 text-gray-700 hover:bg-gray-100'">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" :class="isCustomFilterActive ? 'text-amber-600' : 'text-gray-500'" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                        </svg>
                        <span x-text="isCustomFilterActive ? 'Setting (' + selectedKode.length + ' dipilih)' : 'Setting Kode Periksa'" class="uppercase tracking-wider">Setting Kode Periksa</span>
                        <span x-show="isCustomFilterActive" class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                    </button>
                    <button x-show="isCustomFilterActive" type="button" @click="resetToDefaultFilter()" onclick="resetToDefaultFilter()"
                        class="text-xs font-bold text-red-500 hover:text-red-700 underline transition cursor-pointer"
                        title="Reset filter kode periksa ke default">
                        Reset
                    </button>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <form id="filterForm" action="{{ route('laboratorium.index_gabungan') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
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
                            Menu **Waktu Tunggu Hasil Lab - Gabungan** digunakan untuk mengukur dan memonitor durasi waktu penyelesaian (Turn Around Time / TAT) pemeriksaan laboratorium bagi seluruh pasien rumah sakit, baik rawat jalan maupun rawat inap. Pengukuran dihitung mulai dari waktu sampel diambil (<code class="text-primary font-bold">permintaan_lab.jam_sampel</code>) hingga jam hasil pemeriksaan selesai diperiksa/diinput pada tabel <code class="text-primary font-bold">periksa_lab.jam</code>.
                        </p>
                    </div>

                    <div>
                        <h4 class="text-sm font-black text-gray-800 uppercase tracking-wider mb-2 border-b pb-1">2. Aturan & Rumus Kalkulasi</h4>
                        <ul class="list-disc pl-5 space-y-4 text-sm text-gray-600">
                            <li>
                                <strong>Waktu Tunggu Lab (Durasi):</strong> Selisih waktu antara waktu pemeriksaan selesai dengan waktu pengambilan sampel.
                                <div class="mt-1.5 bg-gray-50 p-2.5 rounded-xl border border-gray-100 font-mono text-[10px] text-gray-700 shadow-inner">
                                    <strong class="text-primary text-[11px]">Formula Matematika:</strong> Durasi = Waktu Periksa (periksa_lab) - Waktu Sampel (permintaan_lab)<br>
                                    <strong class="text-primary text-[11px]">Formula Excel:</strong> <code class="text-red-600 font-bold">=(F2+G2)-(D2+E2)</code> (Di mana kolom F & G merupakan tanggal & jam hasil dari periksa_lab, dan kolom D & E merupakan tanggal & jam sampel dari permintaan_lab).
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
                                        <td class="p-2 font-semibold text-gray-700">Waktu Sampel (Masuk)</td>
                                        <td class="p-2 font-mono text-primary">permintaan_lab</td>
                                        <td class="p-2 font-mono text-primary">tgl_sampel, jam_sampel</td>
                                        <td class="p-2 text-gray-600">Waktu saat sampel laboratorium diambil.</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold text-gray-700">Waktu Hasil</td>
                                        <td class="p-2 font-mono text-primary">periksa_lab</td>
                                        <td class="p-2 font-mono text-primary">tgl_periksa, jam</td>
                                        <td class="p-2 text-gray-600">Tanggal periksa dan jam selesai pemeriksaan laboratorium dari tabel periksa_lab.</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold text-gray-700">Pemeriksaan</td>
                                        <td class="p-2 font-mono text-primary">periksa_lab & jns_perawatan_lab</td>
                                        <td class="p-2 font-mono text-primary">kd_jenis_prw, nm_perawatan</td>
                                        <td class="p-2 text-gray-600">Nama jenis pemeriksaan laboratorium yang diambil dari tabel periksa_lab dan dihubungkan ke jns_perawatan_lab (diakumulasikan via GROUP_CONCAT).</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold text-gray-700">Filter Kode Periksa</td>
                                        <td class="p-2 font-mono text-primary">periksa_lab</td>
                                        <td class="p-2 font-mono text-primary">kd_jenis_prw</td>
                                        <td class="p-2 text-gray-600">Secara default mengecualikan kode periksa paket internal (seperti XBPJS, LIBI, BPJS paket) dan dapat disesuaikan melalui tombol <strong>Setting Kode Periksa</strong>.</td>
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

    {{-- ==================================================== --}}
    {{-- MODAL SETTING FILTER KODE PERIKSA (Sama persis dg Kategori Pasien) --}}
    {{-- ==================================================== --}}
    <div id="settingModal"
         class="fixed inset-0 z-50 hidden overflow-y-auto"
         x-show="showSettingModal"
         x-cloak
         role="dialog"
         aria-modal="true"
         aria-labelledby="setting-modal-title"
         style="display: none;">

        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-gray-900 bg-opacity-60 transition-opacity backdrop-blur-sm"
             @click="closeSettingModal()"
             onclick="closeSettingModal()"></div>

        {{-- Modal Panel --}}
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-2xl border border-gray-100 overflow-hidden"
                 @click.stop>

                {{-- Modal Header --}}
                <div class="bg-gradient-to-r from-primary to-green-700 px-6 py-5 flex items-center justify-between">
                    <div class="flex items-center gap-3 text-white">
                        <div class="bg-white/20 p-2 rounded-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div>
                            <h3 id="setting-modal-title" class="text-lg font-black uppercase tracking-wider">Setting Filter Kode Periksa</h3>
                            <p class="text-white/70 text-xs font-medium mt-0.5">Pilih kode periksa yang ingin diikutsertakan dalam data</p>
                        </div>
                    </div>
                    <button type="button" @click="closeSettingModal()" onclick="closeSettingModal()" class="text-white hover:text-white/70 focus:outline-none transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Info Banner --}}
                <div class="bg-amber-50 border-b border-amber-100 px-6 py-3 flex items-start gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-500 mt-0.5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                    <p class="text-xs text-amber-700 font-medium leading-relaxed">
                        <strong>Default:</strong> Kode periksa paket internal (seperti XBPJS, LIBI, BPJS paket) dikecualikan secara default. Anda dapat memilih kode pemeriksaan yang ingin ditampilkan.
                    </p>
                </div>

                {{-- Search & Actions --}}
                <div class="px-6 pt-5 pb-3 space-y-3">
                    {{-- Search Box --}}
                    <div class="relative">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                        <input type="text"
                               x-model="searchKode"
                               placeholder="Cari kode periksa atau nama pemeriksaan..."
                               class="w-full pl-9 pr-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:ring-2 focus:ring-primary/20 focus:border-primary/40 outline-none text-sm text-gray-800 transition">
                    </div>

                    {{-- Action buttons + counter --}}
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <button type="button" @click="selectAllKode()"
                                class="text-xs font-bold text-primary hover:text-green-800 px-3 py-1.5 rounded-lg bg-primary/10 hover:bg-primary/20 transition">
                                Pilih Semua
                            </button>
                            <button type="button" @click="clearAllKode()"
                                class="text-xs font-bold text-gray-500 hover:text-gray-700 px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 transition">
                                Kosongkan
                            </button>
                            <button type="button" @click="resetToDefault()"
                                class="text-xs font-bold text-amber-700 hover:text-amber-800 px-3 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 transition border border-amber-200">
                                Default (Kecualikan Paket)
                            </button>
                        </div>
                        <div class="text-xs font-bold text-gray-500">
                            <span class="text-primary font-black" x-text="tempSelected.length"></span>
                            dari
                            <span x-text="availableKodeList.length"></span>
                            dipilih
                        </div>
                    </div>
                </div>

                {{-- Checkbox Grid (Scrollable) --}}
                <div class="px-6 pb-4 max-h-64 overflow-y-auto">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                        <template x-for="item in filteredKodeList" :key="item.kd_jenis_prw">
                            <label class="flex items-center gap-3 px-3 py-2.5 rounded-xl cursor-pointer border transition hover:border-primary/30"
                                   :class="tempSelected.includes(item.kd_jenis_prw)
                                       ? 'bg-primary/5 border-primary/30'
                                       : 'bg-white border-gray-100 hover:bg-gray-50'">
                                <input type="checkbox"
                                       :value="item.kd_jenis_prw"
                                       :checked="tempSelected.includes(item.kd_jenis_prw)"
                                       @change="toggleKode(item.kd_jenis_prw)"
                                       class="w-4 h-4 rounded accent-primary flex-shrink-0">
                                <div class="min-w-0">
                                    <div class="text-xs font-black text-gray-800 font-mono" x-text="item.kd_jenis_prw"></div>
                                    <div class="text-[10px] text-gray-500 font-medium truncate" x-text="item.nm_perawatan || '-'"></div>
                                </div>
                            </label>
                        </template>

                        {{-- Empty search state --}}
                        <div x-show="filteredKodeList.length === 0" class="col-span-2 text-center py-8 text-gray-400 text-sm font-medium">
                            Tidak ada kode yang cocok dengan pencarian "
                            <span class="font-bold text-gray-600" x-text="searchKode"></span>
                            "
                        </div>
                    </div>
                </div>

                {{-- Footer Actions --}}
                <div class="bg-gray-50 border-t border-gray-100 px-6 py-4 flex items-center justify-between gap-3">
                    <button type="button" @click="resetToDefaultFilter()" onclick="resetToDefaultFilter()"
                        class="text-sm font-bold text-red-500 hover:text-red-700 transition flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                        Reset ke Default
                    </button>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="closeSettingModal()" onclick="closeSettingModal()"
                            class="px-5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-sm transition">
                            Batal
                        </button>
                        <button type="button" @click="saveAndApply()"
                            class="px-6 py-2.5 rounded-xl bg-primary hover:bg-green-800 text-white font-black text-sm transition shadow-lg shadow-primary/30 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Simpan & Terapkan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script>
        // ====================================================
        // Kode Periksa Filter & Modal Logic (Session-based)
        // ====================================================
        // Kode default yang dikecualikan (paket BPJS, LIBI, dll sesuai request)
        const DEFAULT_EXCLUDED = @json(\App\Repositories\LaboratoriumRepository::$defaultExcludedKode);

        function kodePeriksaSetting() {
            return {
                availableKodeList: @json($availableKode ?? []),
                selectedKode: [],
                tempSelected: [],
                showSettingModal: false,
                searchKode: '',
                isCustomFilterActive: @json($filter_kode_aktif ?? false),

                getDefaultKode() {
                    return this.availableKodeList
                        .map(i => i.kd_jenis_prw)
                        .filter(kd => !DEFAULT_EXCLUDED.includes(kd));
                },

                get filteredKodeList() {
                    if (!this.searchKode.trim()) return this.availableKodeList;
                    const q = this.searchKode.toLowerCase();
                    return this.availableKodeList.filter(item =>
                        (item.kd_jenis_prw && item.kd_jenis_prw.toLowerCase().includes(q)) ||
                        (item.nm_perawatan && item.nm_perawatan.toLowerCase().includes(q))
                    );
                },

                init() {
                    window.alpineSettingComponent = this;

                    const sessionKode = @json($session_kode ?? null);
                    if (sessionKode !== null && Array.isArray(sessionKode) && sessionKode.length > 0) {
                        this.selectedKode = sessionKode;
                        this.isCustomFilterActive = true;
                    } else {
                        // Default: semua kode kecuali paket
                        this.selectedKode = this.getDefaultKode();
                        this.isCustomFilterActive = false;
                    }

                    this.tempSelected = [...this.selectedKode];
                },

                openSettingModal() {
                    this.tempSelected = [...this.selectedKode];
                    this.searchKode = '';
                    this.showSettingModal = true;
                    const el = document.getElementById('settingModal');
                    if (el) {
                        el.classList.remove('hidden');
                        el.style.display = 'block';
                    }
                    document.body.style.overflow = 'hidden';
                },

                closeSettingModal() {
                    this.showSettingModal = false;
                    const el = document.getElementById('settingModal');
                    if (el) {
                        el.classList.add('hidden');
                        el.style.display = 'none';
                    }
                    document.body.style.overflow = '';
                    this.searchKode = '';
                },

                toggleKode(kode) {
                    const idx = this.tempSelected.indexOf(kode);
                    if (idx === -1) {
                        this.tempSelected.push(kode);
                    } else {
                        this.tempSelected.splice(idx, 1);
                    }
                },

                selectAllKode() {
                    const filteredKodes = this.filteredKodeList.map(i => i.kd_jenis_prw);
                    filteredKodes.forEach(kode => {
                        if (!this.tempSelected.includes(kode)) {
                            this.tempSelected.push(kode);
                        }
                    });
                },

                clearAllKode() {
                    const filteredKodes = this.filteredKodeList.map(i => i.kd_jenis_prw);
                    this.tempSelected = this.tempSelected.filter(k => !filteredKodes.includes(k));
                },

                resetToDefault() {
                    this.tempSelected = this.getDefaultKode();
                },

                saveAndApply() {
                    Swal.fire({
                        title: 'Menyimpan Pengaturan...',
                        text: 'Menerapkan filter kode periksa pilihan Anda',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });

                    fetch('{{ route('laboratorium.gabungan.save_settings') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ kode: this.tempSelected })
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.selectedKode = [...this.tempSelected];
                        this.isCustomFilterActive = true;
                        this.closeSettingModal();
                        document.getElementById('filterForm').submit();
                    })
                    .catch(err => {
                        Swal.fire('Error', 'Gagal menyimpan pengaturan: ' + err.message, 'error');
                    });
                },

                resetToDefaultFilter() {
                    Swal.fire({
                        title: 'Mereset Pengaturan...',
                        text: 'Mengembalikan ke filter default (paket dikecualikan)',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });

                    fetch('{{ route('laboratorium.gabungan.reset_settings') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        document.getElementById('filterForm').submit();
                    })
                    .catch(err => {
                        document.getElementById('filterForm').submit();
                    });
                }
            };
        }

        // Global functions for direct onclick
        function openSettingModal() {
            if (window.alpineSettingComponent) {
                window.alpineSettingComponent.openSettingModal();
            } else {
                const el = document.getElementById('settingModal');
                if (el) {
                    el.classList.remove('hidden');
                    el.style.display = 'block';
                }
                document.body.style.overflow = 'hidden';
            }
        }

        function closeSettingModal() {
            if (window.alpineSettingComponent) {
                window.alpineSettingComponent.closeSettingModal();
            } else {
                const el = document.getElementById('settingModal');
                if (el) {
                    el.classList.add('hidden');
                    el.style.display = 'none';
                }
                document.body.style.overflow = '';
            }
        }

        function resetToDefaultFilter() {
            if (window.alpineSettingComponent) {
                window.alpineSettingComponent.resetToDefaultFilter();
            } else {
                fetch('{{ route('laboratorium.gabungan.reset_settings') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).then(() => {
                    document.getElementById('filterForm').submit();
                });
            }
        }

        function openInfoModal() {
            document.getElementById('infoModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeInfoModal() {
            document.getElementById('infoModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        // Register with Alpine if available
        document.addEventListener('alpine:init', () => {
            Alpine.data('kodePeriksaSetting', kodePeriksaSetting);
        });
        window.kodePeriksaSetting = kodePeriksaSetting;
    </script>
@endsection
