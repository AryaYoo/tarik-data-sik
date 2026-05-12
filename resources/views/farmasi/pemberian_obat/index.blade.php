@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 uppercase tracking-tighter italic">Rekap Pemberian Obat dan BHP - Farmasi</h2>
                <p class="text-gray-500 text-sm mt-1">Laporan rekapitulasi distribusi obat dan barang medis habis pakai.</p>
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
            <form action="{{ route('pemberian_obat.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
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
                        Tampilkan Data
                    </button>
                </div>
            </form>
        </div>

        @if (request()->has('tgl_mulai'))
            <!-- Top 5 Stats -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                @foreach ($top3 as $index => $item)
                    <div class="bg-white p-5 rounded-3xl shadow-sm border border-gray-100 flex flex-col relative overflow-hidden group hover:shadow-lg transition-all duration-300">
                        <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform duration-500">
                            <svg class="w-24 h-24 text-primary" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19 3H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V5a2 2 0 00-2-2m-7 14h-2v-4h2v4m4 0h-2v-6h2v6m-8 0H6v-2h2v2z" />
                            </svg>
                        </div>
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center font-black text-xl 
                                @if ($index == 0) bg-yellow-100 text-yellow-600 @elseif($index == 1) bg-gray-100 text-gray-500 @elseif($index == 2) bg-orange-100 text-orange-600 @else bg-primary/10 text-primary @endif shadow-inner">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1">
                                <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">TOP RANK</h4>
                                <p class="text-xs font-black text-gray-800 line-clamp-1" title="{{ $item->barang }}">{{ $item->barang }}</p>
                            </div>
                        </div>
                        <div class="flex items-end justify-between mt-auto">
                            <span class="text-2xl font-black text-primary tracking-tighter">{{ (float) $item->jumlah }}</span>
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest bg-gray-50 px-2 py-1 rounded-lg">{{ $item->satuan }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Export Section -->
            <div class="flex items-center gap-4">
                <a href="javascript:void(0)" 
                    onclick="handleDownload('{{ route('pemberian_obat.export.excel', request()->all()) }}', 'pemberian-obat-{{ date('Y-m-d') }}.xlsx')"
                    class="bg-white border border-gray-200 text-gray-700 font-bold px-6 py-3 rounded-xl hover:bg-gray-50 transition shadow-sm flex items-center gap-2 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    Ekspor Excel
                </a>
                <a href="javascript:void(0)" 
                    onclick="handleDownload('{{ route('pemberian_obat.export.pdf', request()->all()) }}', 'pemberian-obat-{{ date('Y-m-d') }}.pdf')"
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
                    <table class="w-full text-left whitespace-nowrap">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100">
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest w-16">No</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">
                                    <a href="{{ route('pemberian_obat.index', array_merge(request()->all(), ['sort' => 'barang', 'direction' => request('sort') == 'barang' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center gap-1 hover:text-primary transition">
                                        Nama Barang
                                        @if (request('sort', 'barang') == 'barang')
                                            <svg class="w-3 h-3 {{ request('direction', 'asc') == 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-center">Satuan</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-center">
                                    <a href="{{ route('pemberian_obat.index', array_merge(request()->all(), ['sort' => 'jumlah', 'direction' => request('sort') == 'jumlah' && request('direction') == 'desc' ? 'asc' : 'desc'])) }}" class="flex items-center justify-center gap-1 hover:text-primary transition">
                                        Total Distribusi
                                        @if (request('sort') == 'jumlah')
                                            <svg class="w-3 h-3 {{ request('direction') == 'desc' ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        @else
                                            <svg class="w-3 h-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-center">Rincian</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($data as $index => $row)
                                @php
                                    $rowDetails = $details[$row->barang] ?? collect();
                                    $jumlahResep = $rowDetails->count();
                                    $rowId = 'details-' . md5($row->barang);
                                @endphp
                                <tr class="hover:bg-gray-50/30 transition-colors">
                                    <td class="px-6 py-5 text-sm font-black text-primary/40">{{ $data->firstItem() + $index }}</td>
                                    <td class="px-6 py-5">
                                        <div class="text-sm font-black text-gray-800 uppercase">{{ $row->barang }}</div>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-[10px] font-black uppercase bg-gray-100 text-gray-500 tracking-widest">
                                            {{ $row->satuan }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <span class="text-sm font-black text-gray-700">{{ (float) $row->jumlah }}</span>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        @if ($jumlahResep > 0)
                                            <button onclick="toggleDetails('{{ $rowId }}')" id="btn-{{ $rowId }}"
                                                class="inline-flex items-center gap-2 bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-black px-4 py-2 rounded-xl hover:bg-amber-100 transition shadow-sm">
                                                <span id="icon-{{ $rowId }}" class="transition-transform duration-200">▶</span>
                                                <span>{{ $jumlahResep }} DOKTER</span>
                                            </button>
                                        @else
                                            <span class="text-[10px] text-gray-300 font-bold tracking-widest">—</span>
                                        @endif
                                    </td>
                                </tr>

                                @if ($jumlahResep > 0)
                                    <tr id="{{ $rowId }}" class="hidden bg-amber-50/20">
                                        <td colspan="5" class="px-6 py-4">
                                            <div class="rounded-2xl border border-amber-100 overflow-hidden shadow-inner bg-white/50">
                                                <table class="w-full text-left">
                                                    <thead>
                                                        <tr class="bg-amber-50/50">
                                                            <th class="px-6 py-3 text-[10px] font-black text-amber-600 uppercase tracking-widest">Nama Dokter Pemberi Resep</th>
                                                            <th class="px-6 py-3 text-[10px] font-black text-amber-600 uppercase tracking-widest text-center w-32">Jumlah</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-amber-50">
                                                        @foreach ($rowDetails as $detail)
                                                            <tr>
                                                                <td class="px-6 py-3 text-xs font-black text-gray-600">
                                                                    {{ $detail->dokter }}
                                                                </td>
                                                                <td class="px-6 py-3 text-xs text-gray-800 font-black text-center">
                                                                    {{ (float) $detail->jumlah }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-20 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="p-6 bg-gray-50 rounded-full mb-4">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
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
                @if ($data->hasPages())
                    <div class="px-6 py-6 border-t border-gray-50 bg-gray-50/20">
                        {{ $data->links() }}
                    </div>
                @endif
            </div>
        @else
            <div class="bg-white p-20 rounded-3xl shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center">
                <div class="p-6 bg-primary/5 rounded-full mb-8">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-primary/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-gray-800 mb-3 uppercase tracking-tighter italic">Analisis Distribusi Obat</h3>
                <p class="text-gray-400 text-sm max-w-sm font-medium">Silakan tentukan rentang tanggal untuk melihat statistik rekapitulasi pemberian obat dan BHP.</p>
            </div>
        @endif
    </div>

    <script>
        function toggleDetails(rowId) {
            const row = document.getElementById(rowId);
            const icon = document.getElementById('icon-' + rowId);
            const isHidden = row.classList.contains('hidden');

            row.classList.toggle('hidden', !isHidden);
            icon.textContent = isHidden ? '▼' : '▶';
            icon.style.transform = isHidden ? 'rotate(90deg)' : 'rotate(0deg)';
        }
    </script>
@endsection
