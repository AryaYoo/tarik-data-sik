@extends('layouts.app')

@section('content')
    <div class="space-y-6" x-data="{ infoModalOpen: false }">

        {{-- Header --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-2xl font-bold text-gray-800 uppercase tracking-tighter italic">Detail Tindakan Laboratorium</h2>
                        <button type="button" @click="infoModalOpen = true" class="text-primary hover:text-green-800 transition duration-150 focus:outline-none" title="Informasi & Sumber Data">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </button>
                    </div>
                    <p class="text-gray-500 text-sm mt-1">Rekapitulasi rinci setiap transaksi pemeriksaan tindakan laboratorium pasien beserta dokter penanggung jawab dan perujuk.</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-2 text-sm font-medium text-primary bg-primary/10 px-4 py-2 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                    </svg>
                    <span>Real-time Data</span>
                </div>
            </div>
        </div>

        {{-- Filter Card --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <form id="filterForm" action="{{ route('laboratorium.detail_tindakan.index') }}" method="GET"
                  class="grid grid-cols-1 md:grid-cols-7 gap-4 items-end">
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
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Status Kunjungan</label>
                    <select name="status" class="w-full px-4 py-3 rounded-xl bg-gray-50 border-0 focus:bg-white focus:ring-4 focus:ring-primary/10 transition outline-none text-gray-800 shadow-inner text-sm">
                        <option value="">-- Semua --</option>
                        <option value="Ralan" {{ $status == 'Ralan' ? 'selected' : '' }}>Rawat Jalan</option>
                        <option value="Ranap" {{ $status == 'Ranap' ? 'selected' : '' }}>Rawat Inap</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Jenis Kelamin</label>
                    <select name="jk" class="w-full px-4 py-3 rounded-xl bg-gray-50 border-0 focus:bg-white focus:ring-4 focus:ring-primary/10 transition outline-none text-gray-800 shadow-inner text-sm">
                        <option value="">-- Semua --</option>
                        <option value="L" {{ $jk == 'L' ? 'selected' : '' }}>Laki-laki (L)</option>
                        <option value="P" {{ $jk == 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Cari Keyword</label>
                    <input type="text" name="search" value="{{ $search }}" placeholder="No Rawat/RM/Nama..."
                        class="w-full px-4 py-3 rounded-xl bg-gray-50 border-0 focus:bg-white focus:ring-4 focus:ring-primary/10 transition outline-none text-gray-800 shadow-inner text-sm">
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
            {{-- Summary Cards --}}
            @if(!empty($summary))
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                {{-- Total --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex flex-col gap-1">
                    <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Tindakan</div>
                    <div class="text-3xl font-black text-gray-800">{{ number_format($summary['total']) }}</div>
                    <div class="text-xs text-gray-400 font-medium">Transaksi Pemeriksaan</div>
                </div>
                {{-- Laki-laki --}}
                <div class="bg-cyan-50 border border-cyan-100 rounded-2xl p-5 flex flex-col gap-1">
                    <div class="text-[10px] font-black text-cyan-500 uppercase tracking-widest">Laki-laki (L)</div>
                    <div class="text-3xl font-black text-cyan-700">{{ number_format($summary['laki'] ?? 0) }}</div>
                    <div class="text-xs text-cyan-500 font-medium">Pasien Laki-laki</div>
                </div>
                {{-- Perempuan --}}
                <div class="bg-rose-50 border border-rose-100 rounded-2xl p-5 flex flex-col gap-1">
                    <div class="text-[10px] font-black text-rose-500 uppercase tracking-widest">Perempuan (P)</div>
                    <div class="text-3xl font-black text-rose-700">{{ number_format($summary['perempuan'] ?? 0) }}</div>
                    <div class="text-xs text-rose-500 font-medium">Pasien Perempuan</div>
                </div>
                {{-- Rawat Jalan / Inap --}}
                <div class="bg-blue-50 border border-blue-100 rounded-2xl p-5 flex flex-col gap-1">
                    <div class="text-[10px] font-black text-blue-400 uppercase tracking-widest">Ralan / Ranap</div>
                    <div class="text-xl font-black text-blue-800">
                        {{ number_format($summary['ralan']) }} <span class="text-xs text-blue-400 font-normal">Ralan</span> / {{ number_format($summary['ranap']) }} <span class="text-xs text-blue-400 font-normal">Ranap</span>
                    </div>
                    <div class="text-xs text-blue-400 font-medium">Status Kunjungan</div>
                </div>
                {{-- Pasien Unik --}}
                <div class="bg-primary/5 border border-primary/10 rounded-2xl p-5 flex flex-col gap-1">
                    <div class="text-[10px] font-black text-primary/60 uppercase tracking-widest">Pasien Unik</div>
                    <div class="text-3xl font-black text-primary">{{ number_format($summary['unique_patients']) }}</div>
                    <div class="text-xs text-primary/50 font-medium">Total Pasien Melakukan Lab</div>
                </div>
            </div>
            @endif

            {{-- Export Buttons --}}
            <div class="flex items-center flex-wrap gap-3">
                <a href="javascript:void(0)"
                    onclick="handleDownload('{{ route('laboratorium.detail_tindakan.export.excel', request()->except('page')) }}', 'detail-tindakan-lab-{{ $tgl_mulai }}-{{ $tgl_selesai }}.xlsx')"
                    class="bg-white border border-gray-200 text-gray-700 font-bold px-6 py-3 rounded-xl hover:bg-gray-50 transition shadow-sm flex items-center gap-2 text-sm cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    Ekspor Excel
                </a>
                <a href="javascript:void(0)"
                    onclick="handleDownload('{{ route('laboratorium.detail_tindakan.export.pdf', request()->except('page')) }}', 'detail-tindakan-lab-{{ $tgl_mulai }}-{{ $tgl_selesai }}.pdf')"
                    class="bg-white border border-gray-200 text-gray-700 font-bold px-6 py-3 rounded-xl hover:bg-gray-50 transition shadow-sm flex items-center gap-2 text-sm cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    Ekspor PDF
                </a>
            </div>

            {{-- Table Card --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100">
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-center">No</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">No. Rawat , No RM, Nama & JK</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-center">Kode Periksa</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Pemeriksaan</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Dokter Penanggung jawab LAB, Kode PJ, NIP</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Dokter Perujuk, Kode Perujuk</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-center">Tanggal & Jam</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Cara Bayar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($data as $index => $item)
                                @php
                                    $jkUpper = strtoupper((string) $item->jk);
                                    $jkBadgeStyle = match($jkUpper) {
                                        'L' => 'bg-cyan-100 text-cyan-700 border-cyan-200',
                                        'P' => 'bg-rose-100 text-rose-700 border-rose-200',
                                        default => 'bg-gray-100 text-gray-600 border-gray-200',
                                    };
                                    $jkLabel = match($jkUpper) {
                                        'L' => 'Laki-laki (L)',
                                        'P' => 'Perempuan (P)',
                                        default => '-',
                                    };
                                @endphp
                                <tr class="hover:bg-gray-50/30 transition-colors">
                                    <td class="px-6 py-5 text-sm font-bold text-gray-300 text-center">
                                        {{ str_pad($data->firstItem() + $index, 2, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex flex-col gap-0.5">
                                            <div class="text-sm font-black text-primary font-mono">{{ $item->no_rawat }}</div>
                                            <div class="flex items-center gap-2">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black bg-gray-100 text-gray-600 tracking-tighter">
                                                    RM: {{ $item->no_rkm_medis }}
                                                </span>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black border uppercase tracking-wider {{ $jkBadgeStyle }}">
                                                    {{ $jkLabel }}
                                                </span>
                                            </div>
                                            <div class="text-xs font-black text-gray-800 uppercase mt-0.5">{{ $item->nm_pasien }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-black font-mono bg-primary/10 text-primary border border-primary/20">
                                            {{ $item->kd_jenis_prw }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="text-xs font-bold text-gray-800 leading-relaxed">
                                            {{ $item->nm_perawatan ?: '-' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex flex-col gap-0.5">
                                            <div class="text-xs font-black text-gray-800 uppercase">{{ $item->nm_dokter_pj ?: '-' }}</div>
                                            <div class="flex items-center gap-2 text-[10px] text-gray-500 font-medium">
                                                <span class="bg-amber-50 text-amber-700 px-1.5 py-0.5 rounded font-bold border border-amber-200">Kode PJ: {{ $item->kd_pj_lab ?: '-' }}</span>
                                                <span class="bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded font-mono">NIP: {{ $item->nip ?: '-' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex flex-col gap-0.5">
                                            <div class="text-xs font-black text-gray-800 uppercase">{{ $item->nm_dokter_perujuk ?: '-' }}</div>
                                            <div class="text-[10px] text-gray-500 font-medium">
                                                <span class="bg-blue-50 text-blue-700 px-1.5 py-0.5 rounded font-bold border border-blue-200">Kode Perujuk: {{ $item->kd_dokter_perujuk ?: '-' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <div class="text-xs font-black text-gray-700">
                                            {{ \Carbon\Carbon::parse($item->tgl_periksa)->format('d/m/Y') }}
                                        </div>
                                        <div class="text-[10px] font-bold text-gray-400 font-mono mt-0.5">
                                            {{ $item->jam }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex flex-col items-start gap-1">
                                            <span class="inline-flex items-center px-2 py-1 rounded text-[10px] font-black bg-emerald-50 text-emerald-700 tracking-tighter uppercase border border-emerald-200">
                                                {{ $item->png_jawab }}
                                            </span>
                                            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">
                                                {{ strtolower($item->status) == 'ralan' ? 'Rawat Jalan' : 'Rawat Inap' }}
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-20 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="p-6 bg-gray-50 rounded-full mb-4">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
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
            {{-- Empty State --}}
            <div class="bg-white p-20 rounded-3xl shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center">
                <div class="p-6 bg-primary/5 rounded-full mb-8">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-primary/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-gray-800 mb-3 uppercase tracking-tighter italic">Pilih Periode Tanggal</h3>
                <p class="text-gray-400 text-sm max-w-sm font-medium">Silakan tentukan rentang tanggal untuk menampilkan data detail tindakan laboratorium.</p>
            </div>
        @endif

        {{-- Info Modal --}}
        <div x-show="infoModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="infoModalOpen = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-100">
                    {{-- Modal Header --}}
                    <div class="bg-primary px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-2 text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="text-lg font-black uppercase tracking-wider">Informasi Detail Tindakan Laboratorium</h3>
                        </div>
                        <button type="button" @click="infoModalOpen = false" class="text-white hover:text-gray-200 focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Modal Content --}}
                    <div class="p-6 space-y-6 max-h-[70vh] overflow-y-auto">
                        <div>
                            <h4 class="text-sm font-black text-gray-800 uppercase tracking-wider mb-2 border-b pb-1">1. Deskripsi Menu</h4>
                            <p class="text-sm text-gray-600 leading-relaxed">
                                Menu <strong>Detail Tindakan Laboratorium</strong> menyajikan data transaksi per-tindakan pemeriksaan laboratorium secara rinci. Data yang ditampilkan mencakup identitas pasien, jenis kelamin (JK), kode periksa, nama pemeriksaan, dokter penanggung jawab LAB (beserta kode PJ & NIP petugas), dokter perujuk (beserta kode perujuk), tanggal dan jam pemeriksaan, serta cara bayar pasien.
                            </p>
                        </div>

                        <div>
                            <h4 class="text-sm font-black text-gray-800 uppercase tracking-wider mb-2 border-b pb-1">2. Pemetaan Basis Data (SIMRS Khanza)</h4>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs border border-gray-100">
                                    <thead>
                                        <tr class="bg-gray-50 border-b border-gray-100">
                                            <th class="p-2 font-bold text-gray-500 uppercase">Kolom Tampilan UI</th>
                                            <th class="p-2 font-bold text-gray-500 uppercase">Tabel SQL</th>
                                            <th class="p-2 font-bold text-gray-500 uppercase">Kolom SQL</th>
                                            <th class="p-2 font-bold text-gray-500 uppercase">Keterangan / Relasi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        <tr>
                                            <td class="p-2 font-semibold text-gray-700">No. Rawat , No RM, Nama & JK</td>
                                            <td class="p-2 font-mono text-primary">periksa_lab, reg_periksa, pasien</td>
                                            <td class="p-2 font-mono text-primary">no_rawat, no_rkm_medis, nm_pasien, jk</td>
                                            <td class="p-2 text-gray-600">Nomor rawat transaksi, nomor rekam medis, nama pasien, dan jenis kelamin (L/P) dari tabel pasien.</td>
                                        </tr>
                                        <tr>
                                            <td class="p-2 font-semibold text-gray-700">Kode Periksa</td>
                                            <td class="p-2 font-mono text-primary">periksa_lab</td>
                                            <td class="p-2 font-mono text-primary">kd_jenis_prw</td>
                                            <td class="p-2 text-gray-600">Kode jenis perawatan / pemeriksaan laboratorium.</td>
                                        </tr>
                                        <tr>
                                            <td class="p-2 font-semibold text-gray-700">Pemeriksaan</td>
                                            <td class="p-2 font-mono text-primary">jns_perawatan_lab</td>
                                            <td class="p-2 font-mono text-primary">nm_perawatan</td>
                                            <td class="p-2 text-gray-600">Nama jenis perawatan/pemeriksaan laboratorium.</td>
                                        </tr>
                                        <tr>
                                            <td class="p-2 font-semibold text-gray-700">Dokter PJ LAB, Kode PJ, NIP</td>
                                            <td class="p-2 font-mono text-primary">periksa_lab, dokter, petugas</td>
                                            <td class="p-2 font-mono text-primary">kd_dokter, nm_dokter, nip</td>
                                            <td class="p-2 text-gray-600">Kode dokter penanggung jawab LAB, nama dokter PJ, serta NIP analis/petugas lab.</td>
                                        </tr>
                                        <tr>
                                            <td class="p-2 font-semibold text-gray-700">Dokter Perujuk, Kode Perujuk</td>
                                            <td class="p-2 font-mono text-primary">periksa_lab, dokter</td>
                                            <td class="p-2 font-mono text-primary">dokter_perujuk, nm_dokter</td>
                                            <td class="p-2 text-gray-600">Kode dokter perujuk dan nama dokter yang merujuk pemeriksaan.</td>
                                        </tr>
                                        <tr>
                                            <td class="p-2 font-semibold text-gray-700">Tanggal & Jam</td>
                                            <td class="p-2 font-mono text-primary">periksa_lab</td>
                                            <td class="p-2 font-mono text-primary">tgl_periksa, jam</td>
                                            <td class="p-2 text-gray-600">Tanggal dan jam pelaksanaan pemeriksaan laboratorium.</td>
                                        </tr>
                                        <tr>
                                            <td class="p-2 font-semibold text-gray-700">Cara Bayar</td>
                                            <td class="p-2 font-mono text-primary">penjab</td>
                                            <td class="p-2 font-mono text-primary">png_jawab</td>
                                            <td class="p-2 text-gray-600">Nama penanggung jawab biaya/asuransi/cara bayar pasien (BPJS, UMUM, dll).</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex justify-end">
                        <button type="button" @click="infoModalOpen = false" class="bg-primary hover:bg-green-800 text-white font-bold px-6 py-2 rounded-xl text-sm transition focus:outline-none">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
