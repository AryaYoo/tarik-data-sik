@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="bg-primary/10 text-primary p-3 rounded-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-2xl font-bold text-gray-800 uppercase tracking-tighter italic">Stock Opname Farmasi</h2>
                        <button type="button" onclick="openInfoModal()" class="text-primary hover:text-green-800 transition duration-150 focus:outline-none" title="Informasi Formula & Sumber Data">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </button>
                    </div>
                    <p class="text-gray-500 text-sm mt-1">Monitoring dan pencocokan kuantitas persediaan fisik obat dengan catatan sistem.</p>
                </div>
            </div>
            <div class="flex items-center gap-2 text-sm font-medium text-primary bg-primary/10 px-4 py-2 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.254.675A5.002 5.002 0 0018.001 13h-3a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.675-1.254z" clip-rule="evenodd" />
                </svg>
                <span>Real-time Data</span>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <form action="{{ route('farmasi.opname.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Tanggal Opname</label>
                    <input type="date" name="tanggal" value="{{ $tanggal }}" 
                        class="w-full px-4 py-3 rounded-xl bg-gray-50 border-0 focus:bg-white focus:ring-4 focus:ring-primary/10 transition outline-none text-gray-800 shadow-inner text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Depo / Lokasi</label>
                    <select name="kd_bangsal" 
                        class="w-full px-4 py-3 rounded-xl bg-gray-50 border-0 focus:bg-white focus:ring-4 focus:ring-primary/10 transition outline-none text-gray-800 shadow-inner text-sm">
                        <option value="">Semua Depo / Gudang</option>
                        @foreach($depots as $depot)
                            <option value="{{ $depot->kd_bangsal }}" {{ $kd_bangsal == $depot->kd_bangsal ? 'selected' : '' }}>
                                {{ $depot->nm_bangsal }}
                            </option>
                        @endforeach
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

        @if($data)
            <!-- Export Section -->
            <div class="flex items-center gap-4">
                <a href="javascript:void(0)" 
                    onclick="handleDownload('{{ route('farmasi.opname.export.excel', ['tanggal' => $tanggal, 'kd_bangsal' => $kd_bangsal]) }}', 'stock-opname-farmasi-{{ $tanggal }}.xlsx')"
                    class="bg-white border border-gray-200 text-gray-700 font-bold px-6 py-3 rounded-xl hover:bg-gray-50 transition shadow-sm flex items-center gap-2 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    Ekspor Excel
                </a>
                <a href="javascript:void(0)" 
                    onclick="handleDownload('{{ route('farmasi.opname.export.pdf', ['tanggal' => $tanggal, 'kd_bangsal' => $kd_bangsal]) }}', 'stock-opname-farmasi-{{ $tanggal }}.pdf')"
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
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest w-12 text-center">No</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Nama Obat</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest w-32">Satuan</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-right w-36">Stok Sistem</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-right w-36">Stok Fisik</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-right w-36">Selisih</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-center w-40">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($data as $index => $item)
                                <tr class="hover:bg-gray-50/30 transition-colors">
                                    <td class="px-6 py-5 text-sm font-bold text-gray-300 text-center">
                                        {{ str_pad($data->firstItem() + $index, 2, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="text-sm font-black text-gray-800 uppercase">{{ $item->nama_brng }}</div>
                                        <div class="flex items-center gap-1.5 mt-1 text-[9px] font-bold text-gray-400 uppercase tracking-tight">
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-gray-100 text-gray-500 font-mono">
                                                {{ $item->kode_brng }}
                                            </span>
                                            <span>&bull;</span>
                                            <span class="text-primary font-semibold">{{ $item->nm_bangsal }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-600 uppercase">
                                            {{ $item->satuan }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5 text-right font-semibold text-gray-700">
                                        {{ number_format($item->stok_sistem, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-5 text-right font-semibold text-gray-700">
                                        {{ number_format($item->stok_fisik, 0, ',', '.') }}
                                    </td>
                                    @php
                                        $selisih = $item->selisih;
                                        
                                        if ($selisih < 0) {
                                            $selisihColor = 'text-red-600';
                                            $selisihSign = '';
                                            $badgeClass = 'bg-red-50 text-red-700 border-red-200';
                                            $badgeText = 'Kurang';
                                        } elseif ($selisih > 0) {
                                            $selisihColor = 'text-green-600';
                                            $selisihSign = '+';
                                            $badgeClass = 'bg-green-50 text-green-700 border-green-200';
                                            $badgeText = 'Lebih';
                                        } else {
                                            $selisihColor = 'text-gray-500';
                                            $selisihSign = '';
                                            $badgeClass = 'bg-gray-50 text-gray-600 border-gray-200';
                                            $badgeText = 'Sesuai';
                                        }
                                    @endphp
                                    <td class="px-6 py-5 text-right font-black {{ $selisihColor }}">
                                        {{ $selisihSign }}{{ number_format($selisih, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <span class="inline-flex items-center justify-center px-3 py-1 text-[10px] font-black uppercase tracking-wider rounded-full border {{ $badgeClass }} w-24">
                                            {{ $badgeText }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-20 text-center text-gray-400 italic">Data stock opname tidak ditemukan untuk kriteria ini.</td>
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
            <!-- Empty State -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-20 text-center">
                <div class="flex flex-col items-center">
                    <div class="p-6 bg-gray-50 rounded-full mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-black text-gray-800 mb-2">Silakan Pilih Tanggal</h2>
                    <p class="text-gray-500 max-w-md mx-auto">Gunakan filter di atas untuk menarik data stock opname farmasi pada satu tanggal spesifik.</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Modal Informasi Stock Opname -->
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
                        <h3 class="text-lg font-black uppercase tracking-wider">Informasi Formula & Sumber Data</h3>
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
                            Menu <strong>Stock Opname Farmasi</strong> digunakan untuk menyinkronkan kuantitas fisik obat dan alkes yang ada di depo/gudang penyimpanan secara riil dengan catatan persediaan di database sistem pada suatu tanggal spesifik.
                        </p>
                    </div>

                    <div>
                        <h4 class="text-sm font-black text-gray-800 uppercase tracking-wider mb-2 border-b pb-1">2. Aturan & Rumus Kalkulasi</h4>
                        <ul class="list-disc pl-5 space-y-4 text-sm text-gray-600">
                            <li>
                                <strong>Stok Sistem:</strong> Jumlah saldo obat yang tercatat di database komputer pada tanggal opname.
                            </li>
                            <li>
                                <strong>Stok Fisik (Real):</strong> Hasil perhitungan kuantitas fisik barang secara manual oleh petugas di lapangan.
                            </li>
                            <li>
                                <strong>Selisih:</strong> Selisih perbedaan antara kuantitas fisik dan sistem.
                                <div class="mt-1.5 bg-gray-50 p-2.5 rounded-xl border border-gray-100 font-mono text-[10px] text-gray-700 shadow-inner">
                                    <strong class="text-primary text-[11px]">Formula Matematika:</strong> Selisih = Stok Fisik - Stok Sistem<br>
                                    <strong class="text-primary text-[11px]">Formula Excel:</strong> <code class="text-red-600 font-bold">=D2-C2</code> (Stok Fisik - Stok Sistem)
                                </div>
                            </li>
                            <li>
                                <strong>Keterangan:</strong> Klasifikasi hasil pencocokan stok.
                                <div class="mt-1.5 bg-gray-50 p-2.5 rounded-xl border border-gray-100 font-mono text-[10px] text-gray-700 shadow-inner">
                                    <strong>KURANG:</strong> Selisih &lt; 0 (Stok fisik di lapangan lebih sedikit dibanding sistem)<br>
                                    <strong>LEBIH:</strong> Selisih &gt; 0 (Stok fisik di lapangan lebih banyak dibanding sistem)<br>
                                    <strong>SESUAI:</strong> Selisih = 0 (Stok fisik cocok dengan sistem)
                                </div>
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
                                        <th class="p-2 font-bold text-gray-500 uppercase">Keterangan / Kondisi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    <tr>
                                        <td class="p-2 font-semibold text-gray-700">Nama Obat</td>
                                        <td class="p-2 font-mono text-primary">databarang</td>
                                        <td class="p-2 font-mono text-primary">nm_brng</td>
                                        <td class="p-2 text-gray-600">Nama obat atau alat kesehatan terdaftar di master barang.</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold text-gray-700">Satuan</td>
                                        <td class="p-2 font-mono text-primary">kodesatuan</td>
                                        <td class="p-2 font-mono text-primary">satuan</td>
                                        <td class="p-2 text-gray-600">Satuan kemasan obat/barang (kemasan terkecil).</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold text-gray-700">Stok Sistem</td>
                                        <td class="p-2 font-mono text-primary">opname</td>
                                        <td class="p-2 font-mono text-primary">stok</td>
                                        <td class="p-2 text-gray-600">Saldo kuantitas persediaan di program komputer saat opname.</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold text-gray-700">Stok Fisik</td>
                                        <td class="p-2 font-mono text-primary">opname</td>
                                        <td class="p-2 font-mono text-primary">real</td>
                                        <td class="p-2 text-gray-600">Kuantitas fisik riil hasil perhitungan petugas.</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold text-gray-700">Selisih</td>
                                        <td class="p-2 font-mono text-primary">opname</td>
                                        <td class="p-2 font-mono text-primary">selisih</td>
                                        <td class="p-2 text-gray-600">Selisih numerik (`real - stok`).</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold text-gray-700">Depo / Lokasi</td>
                                        <td class="p-2 font-mono text-primary">bangsal</td>
                                        <td class="p-2 font-mono text-primary">nm_bangsal</td>
                                        <td class="p-2 text-gray-600">Nama unit/gudang farmasi yang melakukan opname.</td>
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
