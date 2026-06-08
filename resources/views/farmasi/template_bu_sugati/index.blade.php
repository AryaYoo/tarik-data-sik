@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div
            class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-2xl font-bold text-gray-800 uppercase tracking-tighter italic">Rencana Anggaran</h2>
                        <button type="button" onclick="openInfoModal()"
                            class="text-primary hover:text-green-800 transition duration-150 focus:outline-none"
                            title="Informasi Formula & Sumber Data">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </button>
                    </div>
                    <p class="text-gray-500 text-sm mt-1">Laporan komprehensif stok awal, penerimaan, pemberian, buffer
                        stock, rencana pemakaian, pengadaan, dan anggaran obat.</p>
                </div>
            </div>
            <div class="flex items-center gap-2 text-sm font-medium text-primary bg-primary/10 px-4 py-2 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.254.675A5.002 5.002 0 0018.001 13h-3a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.675-1.254z"
                        clip-rule="evenodd" />
                </svg>
                <span>Real-time Calculations</span>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <form action="{{ route('farmasi.template_bu_sugati.index') }}" method="GET"
                class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Tanggal
                        Mulai</label>
                    <input type="date" name="tgl_mulai" value="{{ $tgl_mulai }}"
                        class="w-full px-4 py-3 rounded-xl bg-gray-50 border-0 focus:bg-white focus:ring-4 focus:ring-primary/10 transition outline-none text-gray-800 shadow-inner text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Tanggal
                        Selesai</label>
                    <input type="date" name="tgl_selesai" value="{{ $tgl_selesai }}"
                        class="w-full px-4 py-3 rounded-xl bg-gray-50 border-0 focus:bg-white focus:ring-4 focus:ring-primary/10 transition outline-none text-gray-800 shadow-inner text-sm">
                </div>
                <div>
                    <button type="submit"
                        class="w-full bg-primary hover:bg-green-800 text-white font-black px-6 py-4 rounded-xl transition shadow-xl shadow-primary/20 uppercase tracking-widest text-[10px] flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z"
                                clip-rule="evenodd" />
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
                    onclick="handleDownload('{{ route('farmasi.template_bu_sugati.export.excel', ['tgl_mulai' => $tgl_mulai, 'tgl_selesai' => $tgl_selesai]) }}', 'template-bu-sugati-{{ $tgl_mulai }}-{{ $tgl_selesai }}.xlsx')"
                    class="bg-white border border-gray-200 text-gray-700 font-bold px-6 py-3 rounded-xl hover:bg-gray-50 transition shadow-sm flex items-center gap-2 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                    Ekspor Excel
                </a>
                <a href="javascript:void(0)"
                    onclick="handleDownload('{{ route('farmasi.template_bu_sugati.export.pdf', ['tgl_mulai' => $tgl_mulai, 'tgl_selesai' => $tgl_selesai]) }}', 'template-bu-sugati-{{ $tgl_mulai }}-{{ $tgl_selesai }}.pdf')"
                    class="bg-white border border-gray-200 text-gray-700 font-bold px-6 py-3 rounded-xl hover:bg-gray-50 transition shadow-sm flex items-center gap-2 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
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
                                <th class="px-4 py-5 text-xs font-black text-gray-400 uppercase tracking-widest"
                                    title="Kode unik barang medis (SIMRS Khanza)">Kode Barang</th>
                                <th class="px-4 py-5 text-xs font-black text-gray-400 uppercase tracking-widest"
                                    title="Nama lengkap barang medis/obat">Nama Barang</th>
                                <th class="px-4 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-center"
                                    title="Satuan kemasan terkecil">Satuan</th>
                                <th class="px-4 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-right"
                                    title="Harga beli satuan terupdate dari master data obat">Harga Barang</th>
                                <th class="px-4 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-right"
                                    title="Stok awal barang: saldo sebelum transaksi pertama dalam periode ini (dari riwayat_barang_medis)">
                                    Stok Awal</th>
                                <th class="px-4 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-right"
                                    title="Total barang masuk dari pesanan/penerimaan (detailpesan & pemesanan) pada periode ini">
                                    Penerimaan</th>
                                <th class="px-4 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-right"
                                    title="Total distribusi pemberian obat (detail_pemberian_obat) pada periode ini">Pemberian
                                </th>
                                <th class="hidden px-4 py-5 text-xs font-black text-purple-500 uppercase tracking-widest text-right"
                                    title="[FITUR INI SEDANG DI-HIDE] Draft resep dari dokter — belum tentu diserahkan. Sumber: resep_dokter.jml JOIN resep_obat.tgl_perawatan">
                                    Rawat Jalan</th>
                                <th class="hidden px-4 py-5 text-xs font-black text-orange-500 uppercase tracking-widest text-right"
                                    title="[FITUR INI SEDANG DI-HIDE] Total penjualan bebas/karyawan. Sumber: SUM(detailjual.jumlah) JOIN penjualan.tgl_jual">
                                    Rawat Inap</th>
                                <th class="px-4 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-right"
                                    title="Stok Akhir = Stok Awal + Penerimaan - Pemberian">Stok Akhir</th>
                                <th class="px-4 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-right"
                                    title="Buffer Stock = 15% dari Pemberian">Buffer Stock 15%</th>
                                <th class="px-4 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-right"
                                    title="Rencana Pemakaian = Pemberian + Buffer Stock">Rencana Pemakaian</th>
                                <th class="px-4 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-right"
                                    title="Rencana Pengadaan = Jika Rencana Pemakaian > Stok Akhir maka Rencana Pemakaian - Stok Akhir, selain itu 0">
                                    Rencana Pengadaan</th>
                                <th class="px-4 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-right"
                                    title="Rencana Anggaran = Rencana Pengadaan * Harga Barang">Rencana Anggaran</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($data as $index => $item)
                                @php
                                    $harga_beli = (float) $item->harga_beli;
                                    $stok_awal = (float) $item->stok_awal;
                                    $penerimaan = (float) $item->penerimaan;
                                    $pemberian = (float) $item->pemberian;

                                    $stok_akhir = $stok_awal + $penerimaan - $pemberian;
                                    $buffer_stock = floor($pemberian * 0.15);
                                    $rencana_pemakaian = $pemberian + $buffer_stock;

                                    $rencana_pengadaan = 0;
                                    if ($rencana_pemakaian > $stok_akhir) {
                                        $rencana_pengadaan = $rencana_pemakaian - $stok_akhir;
                                    }

                                    $rencana_anggaran = $rencana_pengadaan * $harga_beli;
                                @endphp
                                <tr class="hover:bg-gray-50/30 transition-colors">
                                    <td class="px-4 py-4">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black bg-gray-100 text-gray-500 tracking-tighter">
                                            {{ $item->kode_brng }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="text-xs font-black text-gray-800 uppercase tracking-tight">
                                            {{ $item->nama_brng }}</div>
                                    </td>
                                    <td class="px-4 py-4 text-center font-bold text-[10px] text-gray-500 uppercase">
                                        {{ $item->satuan }}
                                    </td>
                                    <td class="px-4 py-4 text-right font-semibold text-gray-700 text-xs">
                                        <span
                                            class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-black bg-green-50 text-green-700 tracking-tighter uppercase">
                                            Rp {{ number_format($harga_beli, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-right font-semibold text-gray-700 text-xs">
                                        {{ number_format($stok_awal, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-4 text-right font-semibold text-gray-700 text-xs">
                                        {{ number_format($penerimaan, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-4 text-right font-semibold text-gray-700 text-xs">
                                        {{ number_format($pemberian, 0, ',', '.') }}
                                    </td>
                                    <td class="hidden px-4 py-4 text-right font-semibold text-purple-600 text-xs">
                                        {{ number_format($item->resep_dokter, 0, ',', '.') }}
                                    </td>
                                    <td class="hidden px-4 py-4 text-right font-semibold text-orange-600 text-xs">
                                        {{ number_format($item->detail_jual, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-4 text-right font-bold text-gray-800 text-xs">
                                        {{ number_format($stok_akhir, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-4 text-right font-semibold text-gray-600 text-xs">
                                        {{ number_format($buffer_stock, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-4 text-right font-semibold text-gray-600 text-xs">
                                        {{ number_format($rencana_pemakaian, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-4 text-right font-bold text-blue-600 text-xs">
                                        {{ number_format($rencana_pengadaan, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-4 text-right font-black text-gray-800 text-xs">
                                        <span
                                            class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-black {{ $rencana_anggaran > 0 ? 'bg-orange-50 text-orange-700' : 'bg-gray-50 text-gray-400' }} tracking-tighter uppercase">
                                            Rp {{ number_format($rencana_anggaran, 0, ',', '.') }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="px-6 py-20 text-center text-gray-400 italic">Data tidak ditemukan.</td>
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
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-200" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-black text-gray-800 mb-2">Silakan Pilih Periode</h2>
                    <p class="text-gray-500 max-w-md mx-auto">Gunakan filter di atas untuk menarik data persediaan dan rencana
                        pengadaan obat periode tertentu.</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Modal Informasi -->
    <div id="infoModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <!-- Overlay -->
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeInfoModal()"></div>

            <!-- Center modal content -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-100">
                <!-- Header -->
                <div class="bg-primary px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-2 text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="text-lg font-black uppercase tracking-wider">Informasi Formula & Sumber Data</h3>
                    </div>
                    <button onclick="closeInfoModal()" class="text-white hover:text-gray-200 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Content -->
                <div class="p-6 space-y-6 max-h-[70vh] overflow-y-auto">
                    <div>
                        <h4 class="text-sm font-black text-gray-800 uppercase tracking-wider mb-2 border-b pb-1">1.
                            Deskripsi Menu</h4>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            Menu <strong>Rencana Anggaran</strong> dirancang untuk kebutuhan perencanaan pengadaan
                            inventaris farmasi (rencana pemakaian, rencana pengadaan, dan estimasi anggaran) pada suatu
                            periode tertentu.
                        </p>
                    </div>

                    <div>
                        <h4 class="text-sm font-black text-gray-800 uppercase tracking-wider mb-2 border-b pb-1">2. Aturan &
                            Rumus Kalkulasi</h4>
                        <ul class="list-disc pl-5 space-y-4 text-sm text-gray-600">
                            <li>
                                <strong>Stok Akhir:</strong>
                                <div
                                    class="mt-1 bg-gray-50 p-2.5 rounded-xl border border-gray-100 font-mono text-[10px] text-gray-700 shadow-inner">
                                    <strong class="text-primary text-[11px]">Formula:</strong> Stok Awal + Penerimaan -
                                    Pemberian<br>
                                    <strong class="text-primary text-[11px]">Format Excel:</strong> <code
                                        class="text-red-600 font-bold">=E2+F2-G2</code>
                                </div>
                            </li>
                            <li>
                                <strong>Buffer Stock 15%:</strong> Kuantitas tambahan (15%) dari pemberian obat untuk
                                menjaga ketahanan stok.
                                <div
                                    class="mt-1 bg-gray-50 p-2.5 rounded-xl border border-gray-100 font-mono text-[10px] text-gray-700 shadow-inner">
                                    <strong class="text-primary text-[11px]">Formula:</strong> Pemberian * 15%<br>
                                    <strong class="text-primary text-[11px]">Format Excel:</strong> <code
                                        class="text-red-600 font-bold">=G2*0.15</code>
                                </div>
                            </li>
                            <li>
                                <strong>Rencana Pemakaian:</strong> Kebutuhan target pemakaian obat pada periode berikutnya.
                                <div
                                    class="mt-1 bg-gray-50 p-2.5 rounded-xl border border-gray-100 font-mono text-[10px] text-gray-700 shadow-inner">
                                    <strong class="text-primary text-[11px]">Formula:</strong> Pemberian + Buffer Stock
                                    15%<br>
                                    <strong class="text-primary text-[11px]">Format Excel:</strong> <code
                                        class="text-red-600 font-bold">=G2+H2</code>
                                </div>
                            </li>
                            <li>
                                <strong>Rencana Pengadaan:</strong> Estimasi unit obat yang harus dipesan.
                                <div
                                    class="mt-1 bg-gray-50 p-2.5 rounded-xl border border-gray-100 font-mono text-[10px] text-gray-700 shadow-inner">
                                    <strong class="text-primary text-[11px]">Formula:</strong> IF(Rencana Pemakaian > Stok
                                    Akhir, Rencana Pemakaian - Stok Akhir, 0)<br>
                                    <strong class="text-primary text-[11px]">Format Excel:</strong> <code
                                        class="text-red-600 font-bold">=IF(I2>H2, I2-H2, 0)</code>
                                </div>
                            </li>
                            <li>
                                <strong>Rencana Anggaran:</strong> Estimasi dana anggaran pengadaan obat.
                                <div
                                    class="mt-1 bg-gray-50 p-2.5 rounded-xl border border-gray-100 font-mono text-[10px] text-gray-700 shadow-inner">
                                    <strong class="text-primary text-[11px]">Formula:</strong> Rencana Pengadaan * Harga
                                    Barang<br>
                                    <strong class="text-primary text-[11px]">Format Excel:</strong> <code
                                        class="text-red-600 font-bold">=J2*D2</code>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="text-sm font-black text-gray-800 uppercase tracking-wider mb-2 border-b pb-1">3. Pemetaan
                            Basis Data (SIMRS Khanza)</h4>
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
                                        <td class="p-2 font-semibold text-gray-700">Harga Barang</td>
                                        <td class="p-2 font-mono text-primary">databarang</td>
                                        <td class="p-2 font-mono text-primary">h_beli</td>
                                        <td class="p-2 text-gray-600">Harga beli master obat terupdate.</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold text-gray-700">Stok Awal</td>
                                        <td class="p-2 font-mono text-primary">riwayat_barang_medis</td>
                                        <td class="p-2 font-mono text-primary">stok_awal</td>
                                        <td class="p-2 text-gray-600">Transaksi pertama pada periode filter (<code
                                                class="font-mono text-[10px]">ORDER BY tanggal ASC, jam ASC LIMIT 1</code>).
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold text-gray-700">Penerimaan</td>
                                        <td class="p-2 font-mono text-primary">detailpesan, pemesanan</td>
                                        <td class="p-2 font-mono text-primary">SUM(detailpesan.jumlah)</td>
                                        <td class="p-2 text-gray-600">Total barang masuk dari pesanan/penerimaan pada
                                            periode terpilih.</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold text-gray-700">Pemberian</td>
                                        <td class="p-2 font-mono text-primary">detail_pemberian_obat</td>
                                        <td class="p-2 font-mono text-primary">SUM(detail_pemberian_obat.jml)</td>
                                        <td class="p-2 text-gray-600">Total pemberian/distribusi obat pada periode terpilih.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold text-purple-700">Resep Dokter (Rawat Jalan)</td>
                                        <td class="p-2 font-mono text-primary">resep_dokter, resep_obat</td>
                                        <td class="p-2 font-mono text-primary">SUM(resep_dokter.jml)</td>
                                        <td class="p-2 text-gray-600">Draft resep dari dokter. Filter: <code
                                                class="font-mono text-[10px]">resep_obat.tgl_perawatan</code>. Catatan:
                                            tidak memotong stok secara langsung. <span class="text-red-500 font-bold block mt-1">[Fitur ini sedang di-hide]</span></td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold text-orange-700">Detail Jual (Rawat Inap)</td>
                                        <td class="p-2 font-mono text-primary">detailjual, penjualan</td>
                                        <td class="p-2 font-mono text-primary">SUM(detailjual.jumlah)</td>
                                        <td class="p-2 text-gray-600">Penjualan bebas/karyawan. Filter: <code
                                                class="font-mono text-[10px]">penjualan.tgl_jual</code>. <span class="text-red-500 font-bold block mt-1">[Fitur ini sedang di-hide]</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 px-6 py-4 flex justify-end">
                    <button onclick="closeInfoModal()"
                        class="bg-primary hover:bg-green-800 text-white font-bold px-6 py-2 rounded-xl text-sm transition focus:outline-none">
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