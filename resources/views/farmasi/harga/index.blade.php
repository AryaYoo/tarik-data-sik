@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 uppercase tracking-tighter italic">Tarik Harga Barang Farmasi</h2>
                <p class="text-gray-500 text-sm mt-1">Ektraksi data harga obat dan BHP medis dari master tabel databarang.</p>
            </div>
            <div class="flex items-center gap-2 text-sm font-medium text-primary bg-primary/10 px-4 py-2 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                </svg>
                <span>Live Price Data</span>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <form action="{{ route('farmasi.harga_barang.index') }}" method="GET" class="space-y-6">
                <input type="hidden" name="tarik" value="1">
                
                <!-- Search Input -->
                <div class="max-w-md">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Cari Barang (Nama / Kode)</label>
                    <div class="relative">
                        <input type="text" name="q" value="{{ $search }}" placeholder="Ketik nama obat atau kode barang..." 
                            class="w-full pl-10 pr-4 py-3 rounded-xl bg-gray-50 border-0 focus:bg-white focus:ring-4 focus:ring-primary/10 transition outline-none text-gray-800 shadow-inner text-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Column Checkboxes -->
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Pilih Kolom Harga Yang Ingin Ditarik</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($columnMap as $col => $label)
                            <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:bg-gray-50 transition cursor-pointer">
                                <input type="checkbox" name="columns[]" value="{{ $col }}" 
                                    {{ in_array($col, $selectedColumns) ? 'checked' : '' }}
                                    class="w-5 h-5 rounded text-primary focus:ring-primary/20 border-gray-300">
                                <span class="text-sm font-semibold text-gray-700">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end pt-2">
                    <button type="submit" 
                        class="bg-primary hover:bg-green-800 text-white font-black px-8 py-4 rounded-xl transition shadow-xl shadow-primary/20 uppercase tracking-widest text-[10px] flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd" />
                        </svg>
                        Tarik Data
                    </button>
                </div>
            </form>
        </div>

        @if($data)
            <!-- Export Section -->
            <div class="flex items-center gap-4">
                <a href="javascript:void(0)" 
                    onclick="handleDownload('{{ route('farmasi.harga_barang.export.excel', ['q' => $search, 'columns' => $selectedColumns]) }}', 'harga-barang-{{ now()->format('Y-m-d') }}.xlsx')"
                    class="bg-white border border-gray-200 text-gray-700 font-bold px-6 py-3 rounded-xl hover:bg-gray-50 transition shadow-sm flex items-center gap-2 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    Ekspor Excel
                </a>
                <a href="javascript:void(0)" 
                    onclick="handleDownload('{{ route('farmasi.harga_barang.export.pdf', ['q' => $search, 'columns' => $selectedColumns]) }}', 'harga-barang-{{ now()->format('Y-m-d') }}.pdf')"
                    class="bg-white border border-gray-200 text-gray-700 font-bold px-6 py-3 rounded-xl hover:bg-gray-50 transition shadow-sm flex items-center gap-2 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    Ekspor PDF
                </a>
            </div>
            
            <!-- Table Section -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100">
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">No</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Kode</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Nama Barang</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Satuan</th>
                                @foreach($selectedColumns as $col)
                                    @if(isset($columnMap[$col]))
                                        <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-right">{{ $columnMap[$col] }}</th>
                                    @endif
                                @endforeach
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($data as $index => $item)
                                <tr class="hover:bg-gray-50/30 transition-colors">
                                    <td class="px-6 py-5 text-sm font-bold text-gray-300">
                                        {{ str_pad($data->firstItem() + $index, 2, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="px-6 py-5 text-sm font-bold text-gray-600">
                                        {{ $item->kode_brng }}
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="text-sm font-black text-gray-800 uppercase">{{ $item->nama_brng }}</div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="inline-flex items-center px-2 py-1 rounded text-[10px] font-black bg-gray-100 text-gray-500 uppercase tracking-tighter">
                                            {{ $item->satuan ?? '-' }}
                                        </span>
                                    </td>
                                    @foreach($selectedColumns as $col)
                                        <td class="px-6 py-5 text-sm font-black text-gray-700 text-right">
                                            Rp{{ number_format($item->$col ?? 0, 0, ',', '.') }}
                                        </td>
                                    @endforeach
                                    <td class="px-6 py-5 text-center">
                                        @if($item->status == '1')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black bg-green-50 text-green-700 tracking-wide uppercase">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black bg-red-50 text-red-700 tracking-wide uppercase">
                                                Non-Aktif
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ count($selectedColumns) + 5 }}" class="px-6 py-20 text-center text-gray-400 italic">Data tidak ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($data->hasPages())
                    <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100">
                        {{ $data->links() }}
                    </div>
                @endif
            </div>
        @else
            <!-- Empty State (SOP 3) -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-20 text-center">
                <div class="flex flex-col items-center">
                    <div class="p-6 bg-gray-50 rounded-full mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-black text-gray-800 mb-2">Pilih Kolom Harga</h2>
                    <p class="text-gray-500 max-w-md mx-auto">Silakan centang kolom-kolom harga yang ingin Anda tarik datanya pada form filter di atas dan tekan tombol "Tarik Data".</p>
                </div>
            </div>
        @endif
    </div>
@endsection
