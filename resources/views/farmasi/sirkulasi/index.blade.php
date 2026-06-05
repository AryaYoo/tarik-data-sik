@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-2xl font-bold text-gray-800 uppercase tracking-tighter italic">Perputaran Obat (Sirkulasi)</h2>
                        <button type="button" onclick="openInfoModal()" class="text-primary hover:text-green-800 transition duration-150 focus:outline-none" title="Informasi Formula & Sumber Data">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </button>
                    </div>
                    <p class="text-gray-500 text-sm mt-1">Monitoring pergerakan stok awal, penerimaan, pemberian, dan stok akhir obat, alkes, & BHP medis.</p>
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
             <form action="{{ route('farmasi.sirkulasi.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
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
                     onclick="handleDownload('{{ route('farmasi.sirkulasi.export.excel', ['tgl_mulai' => $tgl_mulai, 'tgl_selesai' => $tgl_selesai]) }}', 'perputaran-obat-{{ $tgl_mulai }}-{{ $tgl_selesai }}.xlsx')"
                     class="bg-white border border-gray-200 text-gray-700 font-bold px-6 py-3 rounded-xl hover:bg-gray-50 transition shadow-sm flex items-center gap-2 text-sm">
                     <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                         <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                     </svg>
                     Ekspor Excel
                 </a>
                 <a href="javascript:void(0)" 
                     onclick="handleDownload('{{ route('farmasi.sirkulasi.export.pdf', ['tgl_mulai' => $tgl_mulai, 'tgl_selesai' => $tgl_selesai]) }}', 'perputaran-obat-{{ $tgl_mulai }}-{{ $tgl_selesai }}.pdf')"
                     class="bg-white border border-gray-200 text-gray-700 font-bold px-6 py-3 rounded-xl hover:bg-gray-50 transition shadow-sm flex items-center gap-2 text-sm">
                     <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                         <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                     </svg>
                     Ekspor PDF
                 </a>
             </div>
             
             <!-- Table Section -->
             <div x-data="{ showDetail: false }" class="space-y-3">
                 <div class="flex justify-end pr-2">
                     <label class="flex items-center cursor-pointer gap-2 select-none hover:opacity-80 transition-opacity">
                         <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Tampilkan Rincian Sub-Transaksi</span>
                         <div class="relative">
                             <input type="checkbox" x-model="showDetail" class="sr-only">
                             <div class="block bg-gray-200 w-10 h-6 rounded-full transition-colors duration-300" :class="{'bg-primary': showDetail}"></div>
                             <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform duration-300 shadow-sm" :class="{'transform translate-x-4': showDetail}"></div>
                         </div>
                     </label>
                 </div>
                 <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                     <div class="overflow-x-auto">
                     <table class="w-full text-left">
                         <thead>
                             <tr class="bg-gray-50/50 border-b border-gray-100">
                                 <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest" title="Kode unik barang medis (SIMRS Khanza)">Kode Barang</th>
                                 <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest" title="Nama lengkap barang medis/obat">Nama Barang</th>
                                 <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-center" title="Satuan kemasan terkecil">Satuan</th>
                                 <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-right" title="Harga beli terupdate dari master data obat">Harga Barang</th>
                                 <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-right" title="Stok awal periode (riwayat_barang_medis)">Stok Awal</th>
                                 <th class="px-6 py-5 text-xs font-black text-green-500 uppercase tracking-widest text-right bg-green-50/30" title="Total seluruh mutasi masuk (riwayat_barang_medis)">Total Masuk</th>
                                 <th x-cloak x-show="showDetail" class="px-6 py-5 text-xs font-black text-green-400 uppercase tracking-widest text-right bg-green-50/20" title="Penerimaan supplier (riwayat_barang_medis: posisi 'Penerimaan'/'Pengadaan')">Penerimaan Supplier</th>
                                 <th x-cloak x-show="showDetail" class="px-6 py-5 text-xs font-black text-green-400 uppercase tracking-widest text-right bg-green-50/20" title="Obat diretur pasien (riwayat_barang_medis: posisi 'Retur Pasien')">Retur Pasien</th>
                                 <th x-cloak x-show="showDetail" class="px-6 py-5 text-xs font-black text-green-400 uppercase tracking-widest text-right bg-green-50/20" title="Kiriman dari depo lain (riwayat_barang_medis: posisi 'Mutasi')">Mutasi Masuk</th>
                                 <th x-cloak x-show="showDetail" class="px-6 py-5 text-xs font-black text-green-400 uppercase tracking-widest text-right bg-green-50/20" title="Selisih lebih stock opname (riwayat_barang_medis: posisi 'Opname')">Opname Lebih</th>
                                 <th x-cloak x-show="showDetail" class="px-6 py-5 text-xs font-black text-green-400 uppercase tracking-widest text-right bg-green-50/20" title="Sumber masuk lainnya (riwayat_barang_medis: posisi selain yg didefinisikan)">Lain-lain (Masuk)</th>
                                 <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-right" title="Draft resep dari dokter — belum tentu diserahkan (resep_dokter.jml)">Resep Dokter</th>
                                 <th class="px-6 py-5 text-xs font-black text-red-500 uppercase tracking-widest text-right bg-red-50/30" title="Total seluruh mutasi keluar (riwayat_barang_medis)">Total Keluar</th>
                                 <th x-cloak x-show="showDetail" class="px-6 py-5 text-xs font-black text-red-400 uppercase tracking-widest text-right bg-red-50/20" title="Obat diberikan ke pasien (riwayat_barang_medis: posisi 'Pemberian Obat')">Pemberian Obat</th>
                                 <th x-cloak x-show="showDetail" class="px-6 py-5 text-xs font-black text-red-400 uppercase tracking-widest text-right bg-red-50/20" title="Obat dibawa KRS (riwayat_barang_medis: posisi 'Resep Pulang')">Resep Pulang</th>
                                 <th x-cloak x-show="showDetail" class="px-6 py-5 text-xs font-black text-red-400 uppercase tracking-widest text-right bg-red-50/20" title="Penjualan bebas (riwayat_barang_medis: posisi 'Penjualan')">Detail Jual</th>
                                 <th x-cloak x-show="showDetail" class="px-6 py-5 text-xs font-black text-red-400 uppercase tracking-widest text-right bg-red-50/20" title="Pengeluaran internal BHP (riwayat_barang_medis: posisi 'Stok Keluar')">Stok Keluar</th>
                                 <th x-cloak x-show="showDetail" class="px-6 py-5 text-xs font-black text-red-400 uppercase tracking-widest text-right bg-red-50/20" title="Stok dikirim ke depo lain (riwayat_barang_medis: posisi 'Mutasi')">Mutasi Keluar</th>
                                 <th x-cloak x-show="showDetail" class="px-6 py-5 text-xs font-black text-red-400 uppercase tracking-widest text-right bg-red-50/20" title="Dihibahkan (riwayat_barang_medis: posisi 'Hibah')">Hibah</th>
                                 <th x-cloak x-show="showDetail" class="px-6 py-5 text-xs font-black text-red-400 uppercase tracking-widest text-right bg-red-50/20" title="Dikembalikan ke supplier (riwayat_barang_medis: posisi 'Retur Beli')">Retur Supplier</th>
                                 <th x-cloak x-show="showDetail" class="px-6 py-5 text-xs font-black text-red-400 uppercase tracking-widest text-right bg-red-50/20" title="Selisih kurang stock opname (riwayat_barang_medis: posisi 'Opname')">Opname Kurang</th>
                                 <th x-cloak x-show="showDetail" class="px-6 py-5 text-xs font-black text-red-400 uppercase tracking-widest text-right bg-red-50/20" title="Pengambilan medis (riwayat_barang_medis: posisi 'Pengambilan Medis')">Pengambilan Medis</th>
                                 <th x-cloak x-show="showDetail" class="px-6 py-5 text-xs font-black text-red-400 uppercase tracking-widest text-right bg-red-50/20" title="Sumber keluar lainnya (riwayat_barang_medis: posisi selain yg didefinisikan)">Lain-lain (Keluar)</th>
                                 <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-right" title="Stok akhir periode (riwayat_barang_medis)">Stok Akhir</th>
                                 <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-center" title="Validasi: Stok Awal + Total Masuk - Total Keluar = Stok Akhir">Keterangan</th>
                             </tr>
                         </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($data as $index => $item)
                                <tr class="hover:bg-gray-50/30 transition-colors">
                                    <td class="px-6 py-5">
                                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-[10px] font-black bg-gray-100 text-gray-500 tracking-tighter">
                                            {{ $item->kode_brng }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="text-sm font-black text-gray-800 uppercase">{{ $item->nama_brng }}</div>
                                    </td>
                                    <td class="px-6 py-5 text-center font-bold text-xs text-gray-500 uppercase">
                                        {{ $item->satuan }}
                                    </td>
                                    <td class="px-6 py-5 text-right font-semibold text-gray-700">
                                        <span class="inline-flex items-center px-2 py-1 rounded text-[10px] font-black bg-green-50 text-green-700 tracking-tighter uppercase">
                                            Rp {{ number_format($item->harga_beli, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5 text-right font-semibold text-gray-700">
                                        {{ number_format($item->stok_awal, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-5 text-right font-semibold text-green-600 bg-green-50/30">
                                        {{ number_format($item->penerimaan, 0, ',', '.') }}
                                    </td>
                                    <td x-cloak x-show="showDetail" class="px-6 py-5 text-right text-green-500 bg-green-50/20">
                                        {{ number_format($item->pengadaan, 0, ',', '.') }}
                                    </td>
                                    <td x-cloak x-show="showDetail" class="px-6 py-5 text-right text-green-500 bg-green-50/20">
                                        {{ number_format($item->retur_pasien, 0, ',', '.') }}
                                    </td>
                                    <td x-cloak x-show="showDetail" class="px-6 py-5 text-right text-green-500 bg-green-50/20">
                                        {{ number_format($item->mutasi_masuk, 0, ',', '.') }}
                                    </td>
                                    <td x-cloak x-show="showDetail" class="px-6 py-5 text-right text-green-500 bg-green-50/20">
                                        {{ number_format($item->opname_lebih, 0, ',', '.') }}
                                    </td>
                                    <td x-cloak x-show="showDetail" class="px-6 py-5 text-right text-green-500 bg-green-50/20">
                                        {{ number_format($item->lain_lain_masuk, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-5 text-right font-semibold text-gray-700">
                                        {{ number_format($item->resep_dokter, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-5 text-right font-semibold text-red-600 bg-red-50/30">
                                        {{ number_format($item->distribusi, 0, ',', '.') }}
                                    </td>
                                    <td x-cloak x-show="showDetail" class="px-6 py-5 text-right text-red-500 bg-red-50/20">
                                        {{ number_format($item->pemberian_obat, 0, ',', '.') }}
                                    </td>
                                    <td x-cloak x-show="showDetail" class="px-6 py-5 text-right text-red-500 bg-red-50/20">
                                        {{ number_format($item->resep_pulang, 0, ',', '.') }}
                                    </td>
                                    <td x-cloak x-show="showDetail" class="px-6 py-5 text-right text-red-500 bg-red-50/20">
                                        {{ number_format($item->detail_jual, 0, ',', '.') }}
                                    </td>
                                    <td x-cloak x-show="showDetail" class="px-6 py-5 text-right text-red-500 bg-red-50/20">
                                        {{ number_format($item->stok_keluar, 0, ',', '.') }}
                                    </td>
                                    <td x-cloak x-show="showDetail" class="px-6 py-5 text-right text-red-500 bg-red-50/20">
                                        {{ number_format($item->mutasi_keluar, 0, ',', '.') }}
                                    </td>
                                    <td x-cloak x-show="showDetail" class="px-6 py-5 text-right text-red-500 bg-red-50/20">
                                        {{ number_format($item->hibah, 0, ',', '.') }}
                                    </td>
                                    <td x-cloak x-show="showDetail" class="px-6 py-5 text-right text-red-500 bg-red-50/20">
                                        {{ number_format($item->retur_supplier, 0, ',', '.') }}
                                    </td>
                                    <td x-cloak x-show="showDetail" class="px-6 py-5 text-right text-red-500 bg-red-50/20">
                                        {{ number_format($item->opname_kurang, 0, ',', '.') }}
                                    </td>
                                    <td x-cloak x-show="showDetail" class="px-6 py-5 text-right text-red-500 bg-red-50/20">
                                        {{ number_format($item->pengambilan_medis, 0, ',', '.') }}
                                    </td>
                                    <td x-cloak x-show="showDetail" class="px-6 py-5 text-right text-red-500 bg-red-50/20">
                                        {{ number_format($item->lain_lain_keluar, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-5 text-right font-semibold text-gray-700">
                                        {{ number_format($item->stok_akhir, 0, ',', '.') }}
                                    </td>
                                    @php
                                        $expectedStokAkhir = $item->stok_awal + $item->penerimaan - $item->distribusi;
                                        $isBalance = (int)$item->stok_akhir === (int)$expectedStokAkhir;
                                    @endphp
                                    <td class="px-6 py-5 text-center">
                                        @if($isBalance)
                                            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-[10px] font-black bg-green-50 text-green-700 tracking-tight uppercase">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                Balance
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-[10px] font-black bg-red-50 text-red-600 tracking-tight uppercase" title="Seharusnya: {{ number_format($expectedStokAkhir, 0, ',', '.') }} | Selisih: {{ number_format($item->stok_akhir - $expectedStokAkhir, 0, ',', '.') }}">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                Selisih {{ number_format(abs($item->stok_akhir - $expectedStokAkhir), 0, ',', '.') }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td :colspan="showDetail ? 25 : 11" class="px-6 py-20 text-center text-gray-400 italic">Data tidak ditemukan.</td>
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
                    <h2 class="text-2xl font-black text-gray-800 mb-2">Silakan Pilih Periode</h2>
                    <p class="text-gray-500 max-w-md mx-auto">Gunakan filter di atas untuk menarik data perputaran/sirkulasi obat dalam rentang tanggal tertentu.</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Modal Informasi Perputaran Obat -->
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
                            Menu <strong>Perputaran Obat (Sirkulasi)</strong> dirancang untuk melacak pergerakan inventaris farmasi (stok awal, penerimaan, total distribusi, dan stok akhir) pada suatu periode tertentu secara terperinci.
                        </p>
                    </div>

                    <div>
                        <h4 class="text-sm font-black text-gray-800 uppercase tracking-wider mb-2 border-b pb-1">2. Aturan & Rumus Kalkulasi</h4>
                        <ul class="list-disc pl-5 space-y-4 text-sm text-gray-600">
                            <li>
                                <strong>Harga Barang:</strong> Harga beli satuan terupdate untuk barang terkait.
                                <div class="mt-1.5 bg-gray-50 p-2.5 rounded-xl border border-gray-100 font-mono text-[10px] text-gray-700 shadow-inner">
                                    <strong class="text-primary text-[11px]">Keterangan:</strong> Harga beli satuan terdaftar di master data<br>
                                    <strong class="text-primary text-[11px]">Format Excel:</strong> Terformat Rupiah (<code class="text-red-600 font-bold">"Rp" #,##0</code>)
                                </div>
                            </li>
                            <li>
                                <strong>Stok Awal:</strong> Kuantitas fisik inventaris pada awal periode yang difilter (berdasarkan data historis murni).
                                <div class="mt-1.5 bg-gray-50 p-2.5 rounded-xl border border-gray-100 font-mono text-[10px] text-gray-700 shadow-inner">
                                    <strong class="text-primary text-[11px]">Formula Matematika:</strong> Stok Fisik Terlama pada Jangka Waktu Filter<br>
                                    <strong class="text-primary text-[11px]">Formula Excel:</strong> Nilai numerik standar (Qty)
                                </div>
                            </li>
                            <li>
                                <strong>Penerimaan:</strong> Total kenaikan stok (barang masuk) selama periode &mdash; mencakup semua jenis transaksi yang menambah stok (faktur, mutasi masuk, opname naik, retur pasien, dll).
                                <div class="mt-1.5 bg-gray-50 p-2.5 rounded-xl border border-gray-100 font-mono text-[10px] text-gray-700 shadow-inner">
                                    <strong class="text-primary text-[11px]">Formula:</strong> <code class="text-red-600 font-bold">SUM(GREATEST(stok_akhir - stok_awal, 0))</code> per baris transaksi<br>
                                    <strong class="text-primary text-[11px]">Catatan:</strong> Dihitung dari selisih stok per transaksi, bukan dari kolom masuk/keluar (yang tidak selalu berisi delta kuantitas murni di SIMRS Khanza).
                                </div>
                            </li>
                            <li>
                                <strong>Resep Dokter:</strong> Total kuantitas obat yang diresepkan oleh dokter untuk pasien rawat jalan/inap.
                                <div class="mt-1.5 bg-gray-50 p-2.5 rounded-xl border border-gray-100 font-mono text-[10px] text-gray-700 shadow-inner">
                                    <strong class="text-primary text-[11px]">Formula:</strong> <code class="text-red-600 font-bold">SUM(resep_dokter.jml)</code><br>
                                    <strong class="text-primary text-[11px]">Sumber Data:</strong> Tabel <code class="text-red-600 font-bold">resep_dokter</code> yang direferensikan ke tanggal perawatan di <code class="text-red-600 font-bold">resep_obat</code>.
                                </div>
                            </li>
                            <li>
                                <strong>Detail Jual:</strong> Total kuantitas obat yang terjual secara bebas/retail kepada pelanggan umum.
                                <div class="mt-1.5 bg-gray-50 p-2.5 rounded-xl border border-gray-100 font-mono text-[10px] text-gray-700 shadow-inner">
                                    <strong class="text-primary text-[11px]">Formula:</strong> <code class="text-red-600 font-bold">SUM(detailjual.jumlah)</code><br>
                                    <strong class="text-primary text-[11px]">Sumber Data:</strong> Tabel <code class="text-red-600 font-bold">detailjual</code> yang direferensikan ke tanggal penjualan di <code class="text-red-600 font-bold">penjualan</code>.
                                </div>
                            </li>
                            <li>
                                <strong>Pemberian:</strong> Total penurunan stok (barang keluar) selama periode &mdash; mencakup semua jenis transaksi yang mengurangi stok (resep pasien, mutasi keluar, retur suplier, opname turun, dll).
                                <div class="mt-1.5 bg-gray-50 p-2.5 rounded-xl border border-gray-100 font-mono text-[10px] text-gray-700 shadow-inner">
                                    <strong class="text-primary text-[11px]">Formula:</strong> <code class="text-red-600 font-bold">SUM(GREATEST(stok_awal - stok_akhir, 0))</code> per baris transaksi<br>
                                    <strong class="text-primary text-[11px]">Catatan:</strong> Menggunakan metode yang sama dengan Penerimaan, hanya arahnya terbalik.
                                </div>
                            </li>
                            <li>
                                <strong>Stok Akhir:</strong> Saldo stok setelah transaksi TERAKHIR dalam periode (dari kolom stok_akhir baris terakhir).
                                <div class="mt-1.5 bg-green-50 p-2.5 rounded-xl border border-green-200 font-mono text-[10px] text-gray-700 shadow-inner">
                                    <strong class="text-green-700 text-[11px]">&#x2705; DIJAMIN BALANCE:</strong> <code class="text-red-600 font-bold">=E2+F2-I2</code> (Stok Awal + Penerimaan - Pemberian = Stok Akhir)<br>
                                    <strong class="text-green-700 text-[11px]">Logika Farmasi:</strong> Stok Akhir = Stok Awal + Penerimaan - Pemberian &mdash; formula ini selalu tepat karena penerimaan dan pemberian dihitung dari pergerakan nyata per transaksi.
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
                                        <td class="p-2 font-semibold text-gray-700">Kode Barang</td>
                                        <td class="p-2 font-mono text-primary">databarang</td>
                                        <td class="p-2 font-mono text-primary">kode_brng</td>
                                        <td class="p-2 text-gray-600">Kode unik identifikasi barang medis.</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold text-gray-700">Nama Barang</td>
                                        <td class="p-2 font-mono text-primary">databarang</td>
                                        <td class="p-2 font-mono text-primary">nm_brng</td>
                                        <td class="p-2 text-gray-600">Nama barang medis.</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold text-gray-700">Satuan</td>
                                        <td class="p-2 font-mono text-primary">kodesatuan</td>
                                        <td class="p-2 font-mono text-primary">satuan</td>
                                        <td class="p-2 text-gray-600">Satuan kemasan terkecil barang.</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold text-gray-700">Harga Barang</td>
                                        <td class="p-2 font-mono text-primary">databarang</td>
                                        <td class="p-2 font-mono text-primary">h_beli</td>
                                        <td class="p-2 text-gray-600">Harga beli master obat terupdate.</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold text-gray-700">Stok Awal</td>
                                        <td class="p-2 font-mono text-primary">riwayat_barang_medis</td>
                                        <td class="p-2 font-mono text-primary">stok_awal</td>
                                        <td class="p-2 text-gray-600">Transaksi pertama pada periode filter (<code class="font-mono text-[10px]">ORDER BY tanggal ASC, jam ASC LIMIT 1</code>) dengan status 'Simpan'.</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold text-gray-700">Penerimaan</td>
                                        <td class="p-2 font-mono text-primary">riwayat_barang_medis</td>
                                        <td class="p-2 font-mono text-primary">SUM(GREATEST(stok_akhir - stok_awal, 0))</td>
                                        <td class="p-2 text-gray-600">Total kenaikan stok per transaksi. Dihitung dari selisih stok, bukan kolom masuk. Filter: status = &lsquo;Simpan&rsquo;.</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold text-gray-700">Resep Dokter</td>
                                        <td class="p-2 font-mono text-primary">resep_dokter, resep_obat</td>
                                        <td class="p-2 font-mono text-primary">SUM(jml)</td>
                                        <td class="p-2 text-gray-600">Total obat diresepkan dokter. Di-join ke resep_obat.tgl_perawatan.</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold text-gray-700">Detail Jual</td>
                                        <td class="p-2 font-mono text-primary">detailjual, penjualan</td>
                                        <td class="p-2 font-mono text-primary">SUM(jumlah)</td>
                                        <td class="p-2 text-gray-600">Total penjualan bebas apotek. Di-join ke penjualan.tgl_jual.</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold text-gray-700">Pemberian</td>
                                        <td class="p-2 font-mono text-primary">riwayat_barang_medis</td>
                                        <td class="p-2 font-mono text-primary">SUM(GREATEST(stok_awal - stok_akhir, 0))</td>
                                        <td class="p-2 text-gray-600">Total penurunan stok per transaksi. Dihitung dari selisih stok, bukan kolom keluar. Filter: status = &lsquo;Simpan&rsquo;.</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold text-gray-700">Stok Akhir</td>
                                        <td class="p-2 font-mono text-primary">riwayat_barang_medis</td>
                                        <td class="p-2 font-mono text-primary">stok_akhir</td>
                                        <td class="p-2 text-gray-600">Transaksi terakhir pada periode filter (<code class="font-mono text-[10px]">ORDER BY tanggal DESC, jam DESC LIMIT 1</code>) dengan status 'Simpan'.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- Sub-section: Sumber Penerimaan --}}
                        <h5 class="text-xs font-black text-green-700 uppercase tracking-wider mt-4 mb-1 border-b border-green-100 pb-1">&#x2B06; Rincian Sumber PENERIMAAN (Obat Masuk) di riwayat_barang_medis</h5>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs border border-gray-100">
                                <thead>
                                    <tr class="bg-green-50 border-b border-green-100">
                                        <th class="p-2 font-bold text-gray-500 uppercase">Tipe Transaksi</th>
                                        <th class="p-2 font-bold text-gray-500 uppercase">Tabel Sumber</th>
                                        <th class="p-2 font-bold text-gray-500 uppercase">Kolom Qty</th>
                                        <th class="p-2 font-bold text-gray-500 uppercase">Kolom Tanggal Filter</th>
                                        <th class="p-2 font-bold text-gray-500 uppercase">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 text-[10px]">
                                    <tr>
                                        <td class="p-2 font-semibold text-green-700">Pengadaan / Penerimaan</td>
                                        <td class="p-2 font-mono text-primary">detailpembelian, nota_beli</td>
                                        <td class="p-2 font-mono">jml</td>
                                        <td class="p-2 font-mono">nota_beli.tgl_beli</td>
                                        <td class="p-2 text-gray-600">Penerimaan obat/BHP dari supplier/distributor berdasarkan faktur pembelian.</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold text-green-700">Mutasi Masuk</td>
                                        <td class="p-2 font-mono text-primary">mutasibarang</td>
                                        <td class="p-2 font-mono">jml</td>
                                        <td class="p-2 font-mono">tanggal</td>
                                        <td class="p-2 text-gray-600">Penerimaan stok dari depo/gudang lain (kd_bangsalke). Stok bertambah di depo penerima.</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold text-green-700">Retur Jual</td>
                                        <td class="p-2 font-mono text-primary">detreturjual, returjual</td>
                                        <td class="p-2 font-mono">jml_retur</td>
                                        <td class="p-2 font-mono">returjual.tgl_retur</td>
                                        <td class="p-2 text-gray-600">Obat dikembalikan oleh pelanggan retail ke apotek. Stok kembali masuk.</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold text-green-700">Retur Pasien</td>
                                        <td class="p-2 font-mono text-primary">returpasien</td>
                                        <td class="p-2 font-mono">jml</td>
                                        <td class="p-2 font-mono">tanggal</td>
                                        <td class="p-2 text-gray-600">Obat dikembalikan oleh pasien rawat jalan/inap ke farmasi (misal: sisa obat, obat tidak jadi ditebus).</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold text-green-700">Opname Lebih</td>
                                        <td class="p-2 font-mono text-primary">opname</td>
                                        <td class="p-2 font-mono">lebih</td>
                                        <td class="p-2 font-mono">tanggal</td>
                                        <td class="p-2 text-gray-600">Selisih positif saat stock opname fisik (stok fisik lebih banyak dari stok sistem).</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- Sub-section: Sumber Pemberian --}}
                        <h5 class="text-xs font-black text-red-700 uppercase tracking-wider mt-4 mb-1 border-b border-red-100 pb-1">&#x2B07; Rincian Sumber PEMBERIAN (Obat Keluar) di riwayat_barang_medis</h5>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs border border-gray-100">
                                <thead>
                                    <tr class="bg-red-50 border-b border-red-100">
                                        <th class="p-2 font-bold text-gray-500 uppercase">Tipe Transaksi</th>
                                        <th class="p-2 font-bold text-gray-500 uppercase">Tabel Sumber</th>
                                        <th class="p-2 font-bold text-gray-500 uppercase">Kolom Qty</th>
                                        <th class="p-2 font-bold text-gray-500 uppercase">Kolom Tanggal Filter</th>
                                        <th class="p-2 font-bold text-gray-500 uppercase">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 text-[10px]">
                                    <tr>
                                        <td class="p-2 font-semibold text-red-700">Pemberian Obat Pasien</td>
                                        <td class="p-2 font-mono text-primary">detail_pemberian_obat</td>
                                        <td class="p-2 font-mono">jml</td>
                                        <td class="p-2 font-mono">tgl_perawatan</td>
                                        <td class="p-2 text-gray-600">Obat diserahkan ke pasien rawat jalan / rawat inap berdasarkan resep yang divalidasi apoteker.</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold text-red-700">Penjualan Bebas (Retail)</td>
                                        <td class="p-2 font-mono text-primary">detailjual, penjualan</td>
                                        <td class="p-2 font-mono">jumlah</td>
                                        <td class="p-2 font-mono">penjualan.tgl_jual</td>
                                        <td class="p-2 text-gray-600">Penjualan tunai langsung di kasir apotek kepada pembeli umum / non-pasien terdaftar.</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold text-red-700">Resep Pulang</td>
                                        <td class="p-2 font-mono text-primary">resep_pulang</td>
                                        <td class="p-2 font-mono">jml_barang</td>
                                        <td class="p-2 font-mono">tanggal</td>
                                        <td class="p-2 text-gray-600">Obat diberikan kepada pasien rawat inap untuk dibawa pulang saat keluar rumah sakit (KRS).</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold text-red-700">Pengeluaran Internal (Stok Keluar)</td>
                                        <td class="p-2 font-mono text-primary">detail_pengeluaran_obat_bhp, pengeluaran_obat_bhp</td>
                                        <td class="p-2 font-mono">jumlah</td>
                                        <td class="p-2 font-mono">pengeluaran_obat_bhp.tanggal</td>
                                        <td class="p-2 text-gray-600">Pengeluaran obat/BHP untuk penggunaan internal unit RS (misal: UGD, OK, poli) yang tidak ditagih per resep.</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold text-red-700">Mutasi Keluar</td>
                                        <td class="p-2 font-mono text-primary">mutasibarang</td>
                                        <td class="p-2 font-mono">jml</td>
                                        <td class="p-2 font-mono">tanggal</td>
                                        <td class="p-2 text-gray-600">Pengiriman stok ke depo/gudang lain (kd_bangsaldari). Stok berkurang di depo pengirim.</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold text-red-700">Hibah / Donasi</td>
                                        <td class="p-2 font-mono text-primary">detailhibah_obat_bhp, hibah_obat_bhp</td>
                                        <td class="p-2 font-mono">jumlah</td>
                                        <td class="p-2 font-mono">hibah_obat_bhp.tanggal</td>
                                        <td class="p-2 text-gray-600">Pemberian/hibah obat atau BHP ke pihak luar RS (misal: donasi sosial, bantuan bencana).</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold text-red-700">Retur ke Supplier</td>
                                        <td class="p-2 font-mono text-primary">detreturbeli, returbeli</td>
                                        <td class="p-2 font-mono">jml_retur</td>
                                        <td class="p-2 font-mono">returbeli.tgl_retur</td>
                                        <td class="p-2 text-gray-600">Pengembalian obat ke distributor/supplier (ED mendekati, rusak, atau salah kirim).</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold text-red-700">Opname Kurang</td>
                                        <td class="p-2 font-mono text-primary">opname</td>
                                        <td class="p-2 font-mono">selisih (negatif)</td>
                                        <td class="p-2 font-mono">tanggal</td>
                                        <td class="p-2 text-gray-600">Selisih negatif saat stock opname fisik (stok fisik lebih sedikit dari stok sistem — stok dikoreksi turun).</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <p class="text-[10px] text-gray-400 mt-2 italic">&#9432; Semua transaksi di atas bermuara ke tabel <code class="font-mono text-primary">riwayat_barang_medis</code> dengan kolom <code class="font-mono text-primary">posisi</code> sebagai tipe transaksi dan kolom <code class="font-mono text-primary">status = 'Simpan'</code> sebagai filter keabsahan data.</p>
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
