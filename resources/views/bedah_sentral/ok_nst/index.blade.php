@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="bg-primary/10 p-3 rounded-xl text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879a3 3 0 11-4.242-4.242 3 3 0 014.242 0L12 12zm0 0L9.121 9.121a3 3 0 10-4.242 4.242 3 3 0 004.242 0L12 12z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-2xl font-bold text-gray-800 uppercase tracking-tighter italic">Data OK NST</h2>
                        <span class="text-xs font-black bg-primary/10 text-primary px-2.5 py-1 rounded-lg uppercase tracking-wider">Bedah Sentral</span>
                        <button type="button" onclick="openInfoModal()" class="text-primary hover:text-green-800 transition duration-150 focus:outline-none" title="Informasi Indikasi NST & Sumber Data">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </button>
                    </div>
                    <p class="text-gray-500 text-sm mt-1">Daftar pasien OK dengan diagnosa indikasi NST (ICD-10) atau prosedur rekam jantung janin / CTG (ICD-9 CM 75.34).</p>
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
            <form action="{{ route('bedah_sentral.ok_nst.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4 items-end">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Tanggal Mulai</label>
                    <input type="date" name="tgl_mulai" value="{{ request('tgl_mulai', date('Y-m-01')) }}" required
                        class="w-full px-4 py-3 rounded-xl bg-gray-50 border-0 focus:bg-white focus:ring-4 focus:ring-primary/10 transition outline-none text-gray-800 shadow-inner text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Tanggal Selesai</label>
                    <input type="date" name="tgl_selesai" value="{{ request('tgl_selesai', date('Y-m-d')) }}" required
                        class="w-full px-4 py-3 rounded-xl bg-gray-50 border-0 focus:bg-white focus:ring-4 focus:ring-primary/10 transition outline-none text-gray-800 shadow-inner text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Jenis / Kode NST</label>
                    <select name="jenis_nst" class="w-full px-4 py-3 rounded-xl bg-gray-50 border-0 focus:bg-white focus:ring-4 focus:ring-primary/10 transition outline-none text-gray-800 shadow-inner text-sm">
                        <option value="all" {{ request('jenis_nst', 'all') == 'all' ? 'selected' : '' }}>-- Semua Kode NST --</option>
                        <optgroup label="ICD-10 (Indikasi Medis NST)">
                            <option value="O68" {{ request('jenis_nst') == 'O68' ? 'selected' : '' }}>O68 / 068 - Gawat Janin (Persalinan)</option>
                            <option value="O36.83" {{ request('jenis_nst') == 'O36.83' ? 'selected' : '' }}>O36.83 - Kelainan Detak/Irama Jantung Janin</option>
                            <option value="O36.3" {{ request('jenis_nst') == 'O36.3' ? 'selected' : '' }}>O36.3 - Hipoksia Janin (Antepartum)</option>
                            <option value="O41.0" {{ request('jenis_nst') == 'O41.0' ? 'selected' : '' }}>O41.0 - Oligohidramnion (Ketuban Sedikit)</option>
                            <option value="O48" {{ request('jenis_nst') == 'O48' ? 'selected' : '' }}>O48 - Kehamilan Lewat Waktu (Post-term)</option>
                            <option value="Z35.9" {{ request('jenis_nst') == 'Z35.9' ? 'selected' : '' }}>Z35.9 - Kehamilan Risiko Tinggi</option>
                        </optgroup>
                        <optgroup label="ICD-9 CM (Prosedur Tindakan)">
                            <option value="75.34" {{ request('jenis_nst') == '75.34' ? 'selected' : '' }}>75.34 - Pemantauan Janin / NST / CTG</option>
                        </optgroup>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Status Operasi</label>
                    <select name="status_operasi" class="w-full px-4 py-3 rounded-xl bg-gray-50 border-0 focus:bg-white focus:ring-4 focus:ring-primary/10 transition outline-none text-gray-800 shadow-inner text-sm">
                        <option value="">-- Semua Status --</option>
                        <option value="Menunggu" {{ request('status_operasi') == 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
                        <option value="Proses Operasi" {{ request('status_operasi') == 'Proses Operasi' ? 'selected' : '' }}>Proses Operasi</option>
                        <option value="Selesai" {{ request('status_operasi') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>
                <div>
                    <button type="submit"
                        class="w-full bg-primary hover:bg-green-800 text-white font-black px-6 py-4 rounded-xl transition shadow-xl shadow-primary/20 uppercase tracking-widest text-[10px] flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
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
                <a href="javascript:void(0)"
                    onclick="handleDownload('{{ route('bedah_sentral.ok_nst.export.excel', request()->all()) }}', 'data-ok-nst-{{ request('tgl_mulai') }}-{{ request('tgl_selesai') }}.xlsx')"
                    class="bg-white border border-gray-200 text-gray-700 font-bold px-6 py-3 rounded-xl hover:bg-gray-50 transition shadow-sm flex items-center gap-2 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    Ekspor Excel
                </a>
                <a href="javascript:void(0)"
                    onclick="handleDownload('{{ route('bedah_sentral.ok_nst.export.pdf', request()->all()) }}', 'data-ok-nst-{{ request('tgl_mulai') }}-{{ request('tgl_selesai') }}.pdf')"
                    class="bg-white border border-gray-200 text-gray-700 font-bold px-6 py-3 rounded-xl hover:bg-gray-50 transition shadow-sm flex items-center gap-2 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    Ekspor PDF
                </a>

                <!-- Info total -->
                <div class="ml-auto flex items-center gap-2 text-sm font-bold text-gray-500">
                    <span>Total:</span>
                    <span class="bg-primary text-white px-3 py-1 rounded-lg font-black text-sm">{{ $data->total() }} data</span>
                </div>
            </div>

            <!-- NST Category Summary Cards -->
            @if($nstSummary->isNotEmpty())
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-50 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
                    </svg>
                    <h3 class="text-xs font-black text-gray-500 uppercase tracking-widest">Ringkasan per Kategori NST</h3>
                    <span class="ml-auto text-[10px] font-black text-gray-400 bg-gray-50 px-2 py-1 rounded-lg">{{ $nstSummary->count() }} kategori</span>
                </div>
                <div class="overflow-x-auto">
                    <div class="flex gap-4 p-5 min-w-max">
                        @foreach($nstSummary as $index => $item)
                        <div class="bg-white border border-gray-100 rounded-2xl p-5 min-w-[180px] flex flex-col relative overflow-hidden group hover:shadow-md hover:border-primary/20 transition-all duration-300 shadow-sm">
                            {{-- Watermark icon --}}
                            <div class="absolute -right-3 -bottom-3 opacity-[0.04] group-hover:opacity-[0.07] group-hover:scale-110 transition-all duration-500">
                                <svg class="w-20 h-20 text-primary" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z"/>
                                </svg>
                            </div>

                            {{-- Rank badge + Tipe NST --}}
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-8 h-8 rounded-xl flex items-center justify-center font-black text-sm shadow-inner flex-shrink-0
                                    @if($index == 0) bg-yellow-100 text-yellow-600
                                    @elseif($index == 1) bg-gray-100 text-gray-500
                                    @elseif($index == 2) bg-orange-100 text-orange-600
                                    @else bg-primary/10 text-primary
                                    @endif">
                                    {{ $index + 1 }}
                                </div>
                                @if($item->tipe_nst === 'ICD-9 CM (Prosedur)')
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-black bg-blue-100 text-blue-700 uppercase tracking-wider flex-shrink-0">ICD-9</span>
                                @else
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-black bg-amber-100 text-amber-700 uppercase tracking-wider flex-shrink-0">ICD-10</span>
                                @endif
                            </div>

                            {{-- Kode diagnosa --}}
                            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Kode</div>
                            <div class="text-sm font-black text-gray-800 font-mono mb-1">{{ $item->kode_diagnosa }}</div>

                            {{-- Nama diagnosa --}}
                            <div class="text-[10px] font-medium text-gray-500 leading-tight line-clamp-2 mb-4 flex-1"
                                 title="{{ $item->nama_diagnosa }}">
                                {{ Str::limit($item->nama_diagnosa, 60) }}
                            </div>

                            {{-- Jumlah kasus --}}
                            <div class="flex items-end justify-between mt-auto">
                                <span class="text-2xl font-black text-primary tracking-tighter">{{ $item->jumlah }}</span>
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest bg-gray-50 px-2 py-1 rounded-lg">KASUS</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif


            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left whitespace-nowrap">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100">
                                <th class="px-5 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-center">No</th>
                                <th class="px-5 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">No. RM</th>
                                <th class="px-5 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Nama Pasien</th>
                                <th class="px-5 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">No. Rawat</th>
                                <th class="px-5 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Diagnosa Awal NST</th>
                                <th class="px-5 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Tgl & Jam Operasi</th>
                                <th class="px-5 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Paket OK</th>
                                <th class="px-5 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($data as $index => $row)
                                <tr class="hover:bg-gray-50/30 transition-colors">
                                    <td class="px-5 py-5 text-sm font-bold text-gray-300 text-center">
                                        {{ str_pad($data->firstItem() + $index, 2, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <!-- No. RM -->
                                    <td class="px-5 py-5">
                                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-[10px] font-black bg-gray-100 text-gray-600 tracking-tighter font-mono">
                                            {{ $row->no_rkm_medis }}
                                        </span>
                                    </td>
                                    <!-- Nama Pasien -->
                                    <td class="px-5 py-5">
                                        <div class="text-sm font-black text-gray-800 uppercase">{{ $row->nm_pasien }}</div>
                                    </td>
                                    <!-- No. Rawat -->
                                    <td class="px-5 py-5">
                                        <div class="text-xs font-black text-gray-700 font-mono">{{ $row->no_rawat }}</div>
                                    </td>
                                    <!-- Diagnosa / Prosedur NST -->
                                    <td class="px-5 py-5">
                                        <div class="flex items-center gap-2">
                                            @if(($row->tipe_nst ?? '') === 'ICD-9 CM (Prosedur)')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-black bg-blue-50 text-blue-700 border border-blue-200 uppercase font-mono">
                                                    {{ $row->kode_diagnosa }}
                                                </span>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold bg-blue-100 text-blue-800 uppercase tracking-wider">
                                                    ICD-9 Prosedur
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-black bg-amber-50 text-amber-700 border border-amber-200 uppercase font-mono">
                                                    {{ $row->kode_diagnosa }}
                                                </span>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold bg-amber-100 text-amber-800 uppercase tracking-wider">
                                                    ICD-10 Indikasi
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-xs font-semibold text-gray-700 mt-1 max-w-[280px] truncate" title="{{ $row->nama_diagnosa }}">
                                            {{ $row->nama_diagnosa }}
                                        </div>
                                        @if(isset($row->prioritas) && $row->prioritas == 1)
                                            <div class="mt-0.5">
                                                <span class="text-[9px] font-black text-primary uppercase tracking-wider">&#x25CF; Diagnosa Utama</span>
                                            </div>
                                        @endif
                                    </td>
                                    <!-- Tanggal & Jam Operasi -->
                                    <td class="px-5 py-5">
                                        <div class="text-sm font-bold text-gray-700">
                                            {{ \Carbon\Carbon::parse($row->tgl_operasi)->format('d/m/Y') }}
                                        </div>
                                        <div class="text-[10px] text-gray-400 font-mono">
                                            {{ substr($row->jam_mulai, 0, 5) }} - {{ substr($row->jam_selesai, 0, 5) }}
                                        </div>
                                    </td>
                                    <!-- Paket OK -->
                                    <td class="px-5 py-5">
                                        <div class="text-xs font-semibold text-gray-600 max-w-[180px]">{{ $row->nama_paket ?: '-' }}</div>
                                    </td>
                                    <!-- Status Operasi -->
                                    <td class="px-5 py-5 text-center">
                                        @if($row->status_operasi == 'Selesai')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black bg-green-100 text-green-800 uppercase tracking-wider">
                                                Selesai
                                            </span>
                                        @elseif($row->status_operasi == 'Proses Operasi')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black bg-blue-100 text-blue-700 uppercase tracking-wider animate-pulse">
                                                Proses
                                            </span>
                                        @elseif($row->status_operasi == 'Menunggu')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black bg-yellow-100 text-yellow-700 uppercase tracking-wider">
                                                Menunggu
                                            </span>
                                        @else
                                            <span class="text-gray-300 text-xs">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-20 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="p-6 bg-gray-50 rounded-full mb-4">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                            <p class="text-gray-400 font-bold uppercase tracking-widest text-xs">Tidak ada data OK NST ditemukan</p>
                                            <p class="text-gray-300 text-xs mt-1">pada periode dan filter yang dipilih</p>
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
            <!-- Empty State -->
            <div class="bg-white p-20 rounded-3xl shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center">
                <div class="p-6 bg-primary/5 rounded-full mb-8">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-primary/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879a3 3 0 11-4.242-4.242 3 3 0 014.242 0L12 12zm0 0L9.121 9.121a3 3 0 10-4.242 4.242 3 3 0 004.242 0L12 12z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-gray-800 mb-3 uppercase tracking-tighter italic">Pilih Periode Tanggal Operasi</h3>
                <p class="text-gray-400 text-sm max-w-md font-medium">Silakan tentukan rentang tanggal untuk menarik daftar pasien OK dengan diagnosa indikasi NST (ICD-10) atau prosedur pemantauan janin (ICD-9 CM).</p>
                <div class="mt-6 flex flex-col sm:flex-row items-center gap-3 text-xs text-gray-500 bg-amber-50/80 border border-amber-100 px-5 py-3 rounded-2xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Hanya menampilkan diagnosa indikasi NST (O68/068, O36.83, O36.3, O41.0, O48, Z35.9) & prosedur CTG/NST (75.34). Diagnosa non-NST otomatis disaring keluar.</span>
                </div>
            </div>
        @endif
    </div>

    <!-- Modal Informasi & Pemetaan Basis Data (SOP 7) -->
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
                        <h3 class="text-lg font-black uppercase tracking-wider">Dokumentasi Standar Kode NST & Pemetaan Data</h3>
                    </div>
                    <button onclick="closeInfoModal()" class="text-white hover:text-gray-200 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Body Modal -->
                <div class="p-6 space-y-6 max-h-[80vh] overflow-y-auto">
                    <!-- Penjelasan Klinis NST -->
                    <div>
                        <h4 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-2">Penjelasan NST (Non-Stress Test) Kebidanan</h4>
                        <p class="text-gray-600 text-xs leading-relaxed">
                            NST (Non-Stress Test) atau pemeriksaan rekam jantung janin / CTG (Cardiotocography) adalah pemeriksaan kebidanan untuk memantau detak jantung janin (DJJ) dan reaktivitas janin dalam kandungan. Karena NST merupakan <strong>prosedur tes</strong>, di rekam medis pencatatannya dibedakan menjadi:
                        </p>
                    </div>

                    <!-- 2 Kategori Kode -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- ICD-9 CM -->
                        <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="bg-blue-600 text-white text-[10px] font-black px-2 py-0.5 rounded">1. ICD-9 CM</span>
                                <span class="text-xs font-bold text-blue-900">Kode Tindakan / Prosedur</span>
                            </div>
                            <ul class="text-xs text-blue-800 space-y-1.5 list-disc list-inside">
                                <li><strong>75.34</strong> : Fetal monitoring / pemantauan janin (termasuk pemeriksaan DJJ dan NST / CTG).</li>
                                <li><strong>75.3</strong> : Tindakan operatif/monitoring janin lainnya.</li>
                            </ul>
                            <p class="text-[11px] text-blue-600 mt-2 italic">Tersimpan pada tabel <code class="bg-blue-100 px-1 rounded">prosedur_pasien</code> &rarr; <code class="bg-blue-100 px-1 rounded">icd9</code></p>
                        </div>

                        <!-- ICD-10 -->
                        <div class="bg-amber-50 border border-amber-100 rounded-2xl p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="bg-amber-600 text-white text-[10px] font-black px-2 py-0.5 rounded">2. ICD-10</span>
                                <span class="text-xs font-bold text-amber-900">Diagnosis Indikasi Medis</span>
                            </div>
                            <ul class="text-xs text-amber-800 space-y-1.5 list-disc list-inside">
                                <li><strong>O68 / 068</strong> : Gawat janin saat persalinan (fetal distress).</li>
                                <li><strong>O36.83</strong> : Kelainan detak atau irama jantung janin antepartum.</li>
                                <li><strong>O36.3</strong> : Tanda hipoksia janin sebelum persalinan.</li>
                                <li><strong>O41.0</strong> : Oligohidramnion (air ketuban sedikit).</li>
                                <li><strong>O48</strong> : Kehamilan lewat waktu (post-term / lewat HPL).</li>
                                <li><strong>Z35.9</strong> : Pengawasan kehamilan risiko tinggi.</li>
                            </ul>
                            <p class="text-[11px] text-amber-600 mt-2 italic">Tersimpan pada tabel <code class="bg-amber-100 px-1 rounded">diagnosa_pasien</code> &rarr; <code class="bg-amber-100 px-1 rounded">penyakit</code></p>
                        </div>
                    </div>

                    <!-- Aturan Filter Non-NST -->
                    <div class="bg-green-50 border border-green-100 rounded-2xl p-4 text-xs text-green-900">
                        <strong class="text-green-800 uppercase tracking-wider block mb-1">&#x2714; Penyaringan Ketat Non-NST</strong>
                        Sistem secara otomatis mengecualikan diagnosa umum yang non-NST (seperti kode penyakit infeksi A00-B99, penyakit kronis I00-I99, dll.) sehingga daftar yang disajikan murni pasien dengan indikasi atau tindakan NST.
                    </div>

                    <!-- Pemetaan Basis Data SIMRS Khanza -->
                    <div>
                        <h4 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-3">Pemetaan Basis Data (SIMRS Khanza)</h4>
                        <div class="border border-gray-100 rounded-xl overflow-hidden">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 font-bold uppercase">
                                    <tr>
                                        <th class="p-3">Kolom Tampilan</th>
                                        <th class="p-3">Tabel SQL</th>
                                        <th class="p-3">Kolom SQL</th>
                                        <th class="p-3">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <tr>
                                        <td class="p-3 font-semibold text-gray-800">No. RM</td>
                                        <td class="p-3 text-gray-600 font-mono">pasien</td>
                                        <td class="p-3 text-gray-600 font-mono">no_rkm_medis</td>
                                        <td class="p-3 text-gray-500">Nomor rekam medis pasien</td>
                                    </tr>
                                    <tr>
                                        <td class="p-3 font-semibold text-gray-800">Nama Pasien</td>
                                        <td class="p-3 text-gray-600 font-mono">pasien</td>
                                        <td class="p-3 text-gray-600 font-mono">nm_pasien</td>
                                        <td class="p-3 text-gray-500">Nama lengkap pasien</td>
                                    </tr>
                                    <tr>
                                        <td class="p-3 font-semibold text-gray-800">No. Rawat</td>
                                        <td class="p-3 text-gray-600 font-mono">booking_operasi</td>
                                        <td class="p-3 text-gray-600 font-mono">no_rawat</td>
                                        <td class="p-3 text-gray-500">Nomor registrasi perawatan OK</td>
                                    </tr>
                                    <tr>
                                        <td class="p-3 font-semibold text-gray-800">Diagnosa Indikasi NST</td>
                                        <td class="p-3 text-gray-600 font-mono">diagnosa_pasien, penyakit</td>
                                        <td class="p-3 text-gray-600 font-mono">kd_penyakit, nm_penyakit</td>
                                        <td class="p-3 text-gray-500">Filter ICD-10: O68/068, O36.83, O36.3, O41.0, O48, Z35.9</td>
                                    </tr>
                                    <tr>
                                        <td class="p-3 font-semibold text-gray-800">Prosedur NST / CTG</td>
                                        <td class="p-3 text-gray-600 font-mono">prosedur_pasien, icd9</td>
                                        <td class="p-3 text-gray-600 font-mono">kode, deskripsi_panjang</td>
                                        <td class="p-3 text-gray-500">Filter ICD-9 CM: 75.34 (Fetal monitoring)</td>
                                    </tr>
                                    <tr>
                                        <td class="p-3 font-semibold text-gray-800">Tgl & Jam Operasi</td>
                                        <td class="p-3 text-gray-600 font-mono">booking_operasi</td>
                                        <td class="p-3 text-gray-600 font-mono">tanggal, jam_mulai, jam_selesai</td>
                                        <td class="p-3 text-gray-500">Jadwal operasi di Bedah Sentral</td>
                                    </tr>
                                    <tr>
                                        <td class="p-3 font-semibold text-gray-800">Paket OK</td>
                                        <td class="p-3 text-gray-600 font-mono">paket_operasi</td>
                                        <td class="p-3 text-gray-600 font-mono">nm_perawatan</td>
                                        <td class="p-3 text-gray-500">Jenis tindakan operasi OK</td>
                                    </tr>
                                    <tr>
                                        <td class="p-3 font-semibold text-gray-800">Status Operasi</td>
                                        <td class="p-3 text-gray-600 font-mono">booking_operasi</td>
                                        <td class="p-3 text-gray-600 font-mono">status</td>
                                        <td class="p-3 text-gray-500">Menunggu / Proses Operasi / Selesai</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Footer Modal -->
                <div class="bg-gray-50 px-6 py-4 flex justify-end">
                    <button type="button" onclick="closeInfoModal()" class="px-5 py-2.5 bg-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-300 transition text-xs">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Modal Info -->
    <script>
        function openInfoModal() {
            document.getElementById('infoModal').classList.remove('hidden');
        }

        function closeInfoModal() {
            document.getElementById('infoModal').classList.add('hidden');
        }

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeInfoModal();
            }
        });
    </script>
@endsection
