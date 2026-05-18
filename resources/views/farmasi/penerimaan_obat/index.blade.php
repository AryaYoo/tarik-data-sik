@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-2xl font-bold text-gray-800 uppercase tracking-tighter italic">Rekap Penerimaan Obat dan BHP - Farmasi</h2>
                        <button type="button" onclick="openInfoModal()" class="text-primary hover:text-green-800 transition duration-150 focus:outline-none" title="Informasi Formula & Sumber Data">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </button>
                    </div>
                    <p class="text-gray-500 text-sm mt-1">Laporan rekapitulasi pengadaan obat dan barang medis habis pakai.</p>
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
            <form action="{{ route('penerimaan_obat_farmasi.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
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
                                <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">PENGADAAN</h4>
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
                    onclick="handleDownload('{{ route('penerimaan_obat_farmasi.export.excel', request()->all()) }}', 'penerimaan-obat-farmasi-{{ date('Y-m-d') }}.xlsx')"
                    class="bg-white border border-gray-200 text-gray-700 font-bold px-6 py-3 rounded-xl hover:bg-gray-50 transition shadow-sm flex items-center gap-2 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    Ekspor Excel
                </a>
                <a href="javascript:void(0)" 
                    onclick="handleDownload('{{ route('penerimaan_obat_farmasi.export.pdf', request()->all()) }}', 'penerimaan-obat-farmasi-{{ date('Y-m-d') }}.pdf')"
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
                                    <a href="{{ route('penerimaan_obat_farmasi.index', array_merge(request()->all(), ['sort' => 'barang', 'direction' => request('sort') == 'barang' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center gap-1 hover:text-primary transition">
                                        Nama Barang / Item
                                        @if (request('sort', 'barang') == 'barang')
                                            <svg class="w-3 h-3 {{ request('direction', 'asc') == 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-center">Satuan</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-center">
                                    <a href="{{ route('penerimaan_obat_farmasi.index', array_merge(request()->all(), ['sort' => 'jumlah', 'direction' => request('sort') == 'jumlah' && request('direction') == 'desc' ? 'asc' : 'desc'])) }}" class="flex items-center justify-center gap-1 hover:text-primary transition">
                                        Total Penerimaan
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
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($data as $index => $row)
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
                                    <td class="px-6 py-5 text-center font-black text-gray-700">
                                        {{ (float) $row->jumlah }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-20 text-center">
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-gray-800 mb-3 uppercase tracking-tighter italic">Laporan Pengadaan Barang</h3>
                <p class="text-gray-400 text-sm max-w-sm font-medium">Silakan tentukan rentang tanggal untuk melihat statistik rekapitulasi penerimaan obat dan BHP.</p>
            </div>
        @endif
    </div>

    <!-- Modal Informasi Rekap Penerimaan Obat -->
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
                        <h3 class="text-lg font-black uppercase tracking-wider">Informasi Rekap Penerimaan Obat & BHP</h3>
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
                            Menu **Rekap Penerimaan Obat dan BHP** menyajikan ringkasan laporan mengenai kuantitas fisik barang masuk (pengadaan/pemesanan) dari pemasok ke gudang farmasi selama rentang periode saring. Fitur ini mempermudah pelacakan volume barang yang diadakan untuk dianalisis oleh pihak manajemen.
                        </p>
                    </div>

                    <div>
                        <h4 class="text-sm font-black text-gray-800 uppercase tracking-wider mb-2 border-b pb-1">2. Aturan & Rumus Kalkulasi</h4>
                        <ul class="list-disc pl-5 space-y-4 text-sm text-gray-600">
                            <li>
                                <strong>Total Penerimaan:</strong> Akumulasi jumlah kuantitas barang medis yang secara resmi masuk ke depo melalui faktur pengadaan selama periode filter.
                                <div class="mt-1.5 bg-gray-50 p-2.5 rounded-xl border border-gray-100 font-mono text-[10px] text-gray-700 shadow-inner">
                                    <strong class="text-primary text-[11px]">Formula Matematika:</strong> Total Penerimaan = Σ (Kuantitas Masuk pada Faktur)<br>
                                    <strong class="text-primary text-[11px]">Formula Excel:</strong> <code class="text-red-600 font-bold">=SUM(Kuantitas_Fisik_Semua_Faktur)</code>
                                </div>
                            </li>
                            <li>
                                <strong>Top Rank (Peringkat Pengadaan):</strong> Menyaring 5 obat/BHP dengan volume penerimaan pengadaan paling besar untuk dianalisis di panel ringkasan bagian atas halaman secara real-time.
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
                                        <th class="p-2 font-bold text-gray-500 uppercase">Keterangan / Deskripsi Tabel</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    <tr>
                                        <td class="p-2 font-semibold text-gray-700">Nama Barang / Item</td>
                                        <td class="p-2 font-mono text-primary">databarang</td>
                                        <td class="p-2 font-mono text-primary">nama_brng</td>
                                        <td class="p-2 text-gray-600">Nama resmi dari obat atau barang habis pakai medis.</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold text-gray-700">Satuan</td>
                                        <td class="p-2 font-mono text-primary">kodesatuan</td>
                                        <td class="p-2 font-mono text-primary">satuan</td>
                                        <td class="p-2 text-gray-600">Satuan kemasan terkecil barang.</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold text-gray-700">Total Penerimaan</td>
                                        <td class="p-2 font-mono text-primary">detailpesan</td>
                                        <td class="p-2 font-mono text-primary">SUM(jumlah)</td>
                                        <td class="p-2 text-gray-600">Jumlah kuantitas barang masuk pada faktur pesanan.</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold text-gray-700">Tanggal Saring</td>
                                        <td class="p-2 font-mono text-primary">pemesanan</td>
                                        <td class="p-2 font-mono text-primary">tgl_pesan</td>
                                        <td class="p-2 text-gray-600">Penyaringan data menggunakan kondisi `whereBetween` pada tanggal transaksi pemesanan.</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold text-gray-700">Kunci Relasi</td>
                                        <td class="p-2 font-mono text-primary">detailpesan, pemesanan</td>
                                        <td class="p-2 font-mono text-primary">no_faktur</td>
                                        <td class="p-2 text-gray-600">Kunci utama penghubung tabel detail pesanan ke master pemesanan.</td>
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