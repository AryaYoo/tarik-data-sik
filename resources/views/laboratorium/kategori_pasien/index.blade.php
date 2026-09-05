@extends('layouts.app')

@section('content')
    {{-- Alpine.js root — mengatur setting kode periksa via localStorage --}}
    <div class="space-y-6"
         x-data="kodePeriksaSetting()"
         x-init="init()">

        {{-- Header --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-2xl font-bold text-gray-800 uppercase tracking-tighter italic">Kategori Pasien Laboratorium</h2>
                        <button type="button" onclick="openInfoModal()" class="text-primary hover:text-green-800 transition duration-150 focus:outline-none" title="Informasi Formula & Sumber Data">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </button>
                    </div>
                    <p class="text-gray-500 text-sm mt-1">Pengelompokan data pasien laboratorium berdasarkan kategori usia (Neonatus, Bayi, Anak, Dewasa).</p>
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

        {{-- Filter Card --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <form id="filterForm" action="{{ route('laboratorium.kategori_pasien.index') }}" method="GET"
                  class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
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
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Kategori Usia</label>
                    <select name="kategori_usia" class="w-full px-4 py-3 rounded-xl bg-gray-50 border-0 focus:bg-white focus:ring-4 focus:ring-primary/10 transition outline-none text-gray-800 shadow-inner text-sm">
                        <option value="">-- Semua --</option>
                        <option value="neonatus" {{ $kategori_usia == 'neonatus' ? 'selected' : '' }}>Neonatus (&lt; 1 Bulan)</option>
                        <option value="bayi"     {{ $kategori_usia == 'bayi'     ? 'selected' : '' }}>Bayi (1 – 11 Bulan)</option>
                        <option value="anak"     {{ $kategori_usia == 'anak'     ? 'selected' : '' }}>Anak (1 – 17 Tahun)</option>
                        <option value="dewasa"   {{ $kategori_usia == 'dewasa'   ? 'selected' : '' }}>Dewasa (&gt; 17 Tahun)</option>
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
            {{-- Summary Cards --}}
            @if(!empty($summary))
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                {{-- Total --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex flex-col gap-1 col-span-2 md:col-span-1">
                    <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total</div>
                    <div class="text-3xl font-black text-gray-800">{{ number_format($summary['total']) }}</div>
                    <div class="text-xs text-gray-400 font-medium">Semua Kategori</div>
                </div>
                {{-- Neonatus --}}
                <div class="bg-purple-50 border border-purple-100 rounded-2xl p-5 flex flex-col gap-1">
                    <div class="flex items-center justify-between mb-1">
                        <div class="text-[10px] font-black text-purple-400 uppercase tracking-widest">Neonatus</div>
                        <span class="text-[9px] font-black text-purple-300 bg-purple-100 px-2 py-0.5 rounded-full">&lt; 1 Bln</span>
                    </div>
                    <div class="text-3xl font-black text-purple-700">{{ number_format($summary['neonatus']) }}</div>
                    @if($summary['total'] > 0)
                    <div class="text-xs text-purple-400 font-medium">{{ number_format(($summary['neonatus'] / $summary['total']) * 100, 1) }}% dari total</div>
                    @endif
                </div>
                {{-- Bayi --}}
                <div class="bg-blue-50 border border-blue-100 rounded-2xl p-5 flex flex-col gap-1">
                    <div class="flex items-center justify-between mb-1">
                        <div class="text-[10px] font-black text-blue-400 uppercase tracking-widest">Bayi</div>
                        <span class="text-[9px] font-black text-blue-300 bg-blue-100 px-2 py-0.5 rounded-full">1–11 Bln</span>
                    </div>
                    <div class="text-3xl font-black text-blue-700">{{ number_format($summary['bayi']) }}</div>
                    @if($summary['total'] > 0)
                    <div class="text-xs text-blue-400 font-medium">{{ number_format(($summary['bayi'] / $summary['total']) * 100, 1) }}% dari total</div>
                    @endif
                </div>
                {{-- Anak --}}
                <div class="bg-amber-50 border border-amber-100 rounded-2xl p-5 flex flex-col gap-1">
                    <div class="flex items-center justify-between mb-1">
                        <div class="text-[10px] font-black text-amber-400 uppercase tracking-widest">Anak</div>
                        <span class="text-[9px] font-black text-amber-300 bg-amber-100 px-2 py-0.5 rounded-full">1–17 Thn</span>
                    </div>
                    <div class="text-3xl font-black text-amber-700">{{ number_format($summary['anak']) }}</div>
                    @if($summary['total'] > 0)
                    <div class="text-xs text-amber-400 font-medium">{{ number_format(($summary['anak'] / $summary['total']) * 100, 1) }}% dari total</div>
                    @endif
                </div>
                {{-- Dewasa --}}
                <div class="bg-primary/5 border border-primary/10 rounded-2xl p-5 flex flex-col gap-1">
                    <div class="flex items-center justify-between mb-1">
                        <div class="text-[10px] font-black text-primary/60 uppercase tracking-widest">Dewasa</div>
                        <span class="text-[9px] font-black text-primary/40 bg-primary/10 px-2 py-0.5 rounded-full">&gt; 17 Thn</span>
                    </div>
                    <div class="text-3xl font-black text-primary">{{ number_format($summary['dewasa']) }}</div>
                    @if($summary['total'] > 0)
                    <div class="text-xs text-primary/50 font-medium">{{ number_format(($summary['dewasa'] / $summary['total']) * 100, 1) }}% dari total</div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Export Buttons --}}
            <div class="flex items-center flex-wrap gap-3">
                <a href="javascript:void(0)"
                    onclick="handleDownload('{{ route('laboratorium.kategori_pasien.export.excel', request()->except('page')) }}', 'kategori-pasien-lab-{{ $tgl_mulai }}-{{ $tgl_selesai }}.xlsx')"
                    class="bg-white border border-gray-200 text-gray-700 font-bold px-6 py-3 rounded-xl hover:bg-gray-50 transition shadow-sm flex items-center gap-2 text-sm cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    Ekspor Excel
                </a>
                <a href="javascript:void(0)"
                    onclick="handleDownload('{{ route('laboratorium.kategori_pasien.export.pdf', request()->except('page')) }}', 'kategori-pasien-lab-{{ $tgl_mulai }}-{{ $tgl_selesai }}.pdf')"
                    class="bg-white border border-gray-200 text-gray-700 font-bold px-6 py-3 rounded-xl hover:bg-gray-50 transition shadow-sm flex items-center gap-2 text-sm cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
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
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">No</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">No. RM</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Nama Pasien</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Tgl. Pemeriksaan</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-center">Umur & Kategori</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Jenis Pemeriksaan</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Jenis Bayar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($data as $index => $item)
                                @php
                                    // Tentukan warna label kategori usia
                                    $kategori = $item->kategori_usia;
                                    $labelStyle = match($kategori) {
                                        'Neonatus' => 'bg-purple-100 text-purple-700 border-purple-200',
                                        'Bayi'     => 'bg-blue-100 text-blue-700 border-blue-200',
                                        'Anak'     => 'bg-amber-100 text-amber-700 border-amber-200',
                                        default    => 'bg-primary/10 text-primary border-primary/20',
                                    };

                                    // Format umur yang mudah dibaca
                                    $umurTahun  = (int) $item->umur_tahun;
                                    $umurBulan  = (int) $item->umur_bulan_total;
                                    $bulanSisa  = $umurBulan % 12;

                                    if ($umurTahun >= 1) {
                                        $umurTeks = $umurTahun . ' Th ' . $bulanSisa . ' Bln';
                                    } elseif ($umurBulan >= 1) {
                                        $umurTeks = $umurBulan . ' Bulan';
                                    } else {
                                        // Hitung hari jika < 1 bulan
                                        $tglLahir  = \Carbon\Carbon::parse($item->tgl_lahir);
                                        $tglSampel = \Carbon\Carbon::parse($item->tgl_sampel);
                                        $umurTeks  = $tglLahir->diffInDays($tglSampel) . ' Hari';
                                    }
                                @endphp
                                <tr class="hover:bg-gray-50/30 transition-colors">
                                    <td class="px-6 py-5 text-sm font-bold text-gray-300">
                                        {{ str_pad($data->firstItem() + $index, 2, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-[10px] font-black bg-gray-100 text-gray-500 tracking-tighter">
                                            {{ $item->no_rkm_medis }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="text-sm font-black text-gray-800 uppercase">{{ $item->nm_pasien }}</div>
                                        <div class="text-[10px] font-bold text-gray-400 mt-0.5 tracking-widest">{{ $item->no_rawat }}</div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="text-sm font-black text-gray-700">
                                            {{ \Carbon\Carbon::parse($item->tgl_sampel)->format('d/m/Y') }}
                                        </div>
                                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">
                                            {{ $item->status == 'ralan' ? 'Rawat Jalan' : 'Rawat Inap' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <div class="text-sm font-black text-gray-700 mb-1.5">{{ $umurTeks }}</div>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black border uppercase tracking-widest {{ $labelStyle }}">
                                            {{ $kategori }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="text-[10px] font-medium text-gray-600 leading-relaxed max-w-[220px]">
                                            {{ $item->pemeriksaan ?: '-' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="inline-flex items-center px-2 py-1 rounded text-[10px] font-black bg-blue-50 text-blue-600 tracking-tighter uppercase">
                                            {{ $item->png_jawab }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-20 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="p-6 bg-gray-50 rounded-full mb-4">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-gray-800 mb-3 uppercase tracking-tighter italic">Pilih Periode Tanggal</h3>
                <p class="text-gray-400 text-sm max-w-sm font-medium">Silakan tentukan rentang tanggal untuk melihat data kategori pasien laboratorium.</p>
                {{-- Legenda kategori --}}
                <div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div class="flex items-center gap-2 bg-purple-50 border border-purple-100 rounded-xl px-4 py-2.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-purple-400 flex-shrink-0"></span>
                        <div class="text-left">
                            <div class="text-[10px] font-black text-purple-600 uppercase tracking-wider">Neonatus</div>
                            <div class="text-[9px] text-purple-400 font-medium">&lt; 1 Bulan</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 bg-blue-50 border border-blue-100 rounded-xl px-4 py-2.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-400 flex-shrink-0"></span>
                        <div class="text-left">
                            <div class="text-[10px] font-black text-blue-600 uppercase tracking-wider">Bayi</div>
                            <div class="text-[9px] text-blue-400 font-medium">1 – 11 Bulan</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 bg-amber-50 border border-amber-100 rounded-xl px-4 py-2.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-400 flex-shrink-0"></span>
                        <div class="text-left">
                            <div class="text-[10px] font-black text-amber-600 uppercase tracking-wider">Anak</div>
                            <div class="text-[9px] text-amber-400 font-medium">1 – 17 Tahun</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 bg-primary/5 border border-primary/10 rounded-xl px-4 py-2.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-primary flex-shrink-0"></span>
                        <div class="text-left">
                            <div class="text-[10px] font-black text-primary uppercase tracking-wider">Dewasa</div>
                            <div class="text-[9px] text-primary/50 font-medium">&gt; 17 Tahun</div>
                        </div>
                    </div>
                </div>
            @endif

        {{-- ====================================================
             MODAL SETTING KODE PERIKSA
             ==================================================== --}}
        <div id="settingModal"
             x-show="showSettingModal"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto hidden"
             style="display:none;"
             aria-labelledby="setting-modal-title"
             role="dialog" aria-modal="true">

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

        {{-- Info Modal (existing) --}}
        <div id="infoModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeInfoModal()"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-100">
                    {{-- Modal Header --}}
                    <div class="bg-primary px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-2 text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="text-lg font-black uppercase tracking-wider">Informasi Kategori Pasien Lab</h3>
                        </div>
                        <button onclick="closeInfoModal()" class="text-white hover:text-gray-200 focus:outline-none">
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
                                Menu <strong>Kategori Pasien Laboratorium</strong> digunakan untuk mengelompokkan dan menganalisis data pasien laboratorium berdasarkan kategori usia pada saat pemeriksaan dilakukan. Kategori ditentukan secara otomatis berdasarkan selisih tanggal lahir pasien dengan tanggal pemeriksaan laboratorium.
                            </p>
                        </div>

                        <div>
                            <h4 class="text-sm font-black text-gray-800 uppercase tracking-wider mb-2 border-b pb-1">2. Definisi Kategori Usia</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div class="bg-purple-50 p-3 rounded-xl border border-purple-200">
                                    <div class="text-purple-800 font-black text-xs uppercase tracking-wide">Neonatus</div>
                                    <div class="text-purple-600 text-[11px] font-medium mt-0.5">Usia &lt; 1 Bulan saat pemeriksaan</div>
                                </div>
                                <div class="bg-blue-50 p-3 rounded-xl border border-blue-200">
                                    <div class="text-blue-800 font-black text-xs uppercase tracking-wide">Bayi</div>
                                    <div class="text-blue-600 text-[11px] font-medium mt-0.5">Usia 1 – 11 Bulan saat pemeriksaan</div>
                                </div>
                                <div class="bg-amber-50 p-3 rounded-xl border border-amber-200">
                                    <div class="text-amber-800 font-black text-xs uppercase tracking-wide">Anak</div>
                                    <div class="text-amber-600 text-[11px] font-medium mt-0.5">Usia 1 – 17 Tahun saat pemeriksaan</div>
                                </div>
                                <div class="bg-green-50 p-3 rounded-xl border border-green-200">
                                    <div class="text-green-800 font-black text-xs uppercase tracking-wide">Dewasa</div>
                                    <div class="text-green-600 text-[11px] font-medium mt-0.5">Usia &gt; 17 Tahun saat pemeriksaan</div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-black text-gray-800 uppercase tracking-wider mb-2 border-b pb-1">3. Pemetaan Basis Data (SIMRS Khanza)</h4>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs border border-gray-100">
                                    <thead>
                                        <tr class="bg-gray-50 border-b border-gray-100">
                                            <th class="p-2 font-bold text-gray-500 uppercase">Kolom UI</th>
                                            <th class="p-2 font-bold text-gray-500 uppercase">Tabel</th>
                                            <th class="p-2 font-bold text-gray-500 uppercase">Kolom SQL</th>
                                            <th class="p-2 font-bold text-gray-500 uppercase">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        <tr>
                                            <td class="p-2 font-semibold text-gray-700">Tgl. Pemeriksaan</td>
                                            <td class="p-2 font-mono text-primary">permintaan_lab</td>
                                            <td class="p-2 font-mono text-primary">tgl_sampel</td>
                                            <td class="p-2 text-gray-600">Tanggal sampel lab diambil. Digunakan sebagai acuan tanggal pemeriksaan.</td>
                                        </tr>
                                        <tr>
                                            <td class="p-2 font-semibold text-gray-700">No. RM</td>
                                            <td class="p-2 font-mono text-primary">pasien</td>
                                            <td class="p-2 font-mono text-primary">no_rkm_medis</td>
                                            <td class="p-2 text-gray-600">Nomor Rekam Medis pasien.</td>
                                        </tr>
                                        <tr>
                                            <td class="p-2 font-semibold text-gray-700">Nama Pasien</td>
                                            <td class="p-2 font-mono text-primary">pasien</td>
                                            <td class="p-2 font-mono text-primary">nm_pasien</td>
                                            <td class="p-2 text-gray-600">Nama lengkap pasien.</td>
                                        </tr>
                                        <tr>
                                            <td class="p-2 font-semibold text-gray-700">Umur & Kategori</td>
                                            <td class="p-2 font-mono text-primary">pasien</td>
                                            <td class="p-2 font-mono text-primary">tgl_lahir</td>
                                            <td class="p-2 text-gray-600">Dihitung: <code class="text-red-600 font-bold">TIMESTAMPDIFF(YEAR/MONTH, tgl_lahir, tgl_sampel)</code></td>
                                        </tr>
                                        <tr>
                                            <td class="p-2 font-semibold text-gray-700">Jenis Pemeriksaan</td>
                                            <td class="p-2 font-mono text-primary">jns_perawatan_lab</td>
                                            <td class="p-2 font-mono text-primary">nm_perawatan</td>
                                            <td class="p-2 text-gray-600">Nama jenis pemeriksaan lab (via GROUP_CONCAT).</td>
                                        </tr>
                                        <tr>
                                            <td class="p-2 font-semibold text-gray-700">Jenis Bayar</td>
                                            <td class="p-2 font-mono text-primary">penjab</td>
                                            <td class="p-2 font-mono text-primary">png_jawab</td>
                                            <td class="p-2 text-gray-600">Nama penanggung jawab biaya pasien (BPJS, Umum, dll).</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex justify-end">
                        <button onclick="closeInfoModal()" class="bg-primary hover:bg-green-800 text-white font-bold px-6 py-2 rounded-xl text-sm transition focus:outline-none">
                            Tutup
                        </button>
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
                availableKodeList: @json($availableKode),
                selectedKode: [],
                tempSelected: [],
                showSettingModal: false,
                searchKode: '',
                isCustomFilterActive: @json($filter_kode_aktif),

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

                    const sessionKode = @json($session_kode);
                    if (sessionKode !== null && Array.isArray(sessionKode)) {
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

                    fetch('{{ route('laboratorium.kategori_pasien.save_settings') }}', {
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

                    fetch('{{ route('laboratorium.kategori_pasien.reset_settings') }}', {
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
                fetch('{{ route('laboratorium.kategori_pasien.reset_settings') }}', {
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
