@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-2xl font-bold text-gray-800 uppercase tracking-tighter italic">Penarikan Data Rawat Jalan - Farmasi</h2>
                        <button type="button" onclick="openInfoModal()" class="text-primary hover:text-green-800 transition duration-150 focus:outline-none" title="Informasi Formula & Sumber Data">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </button>
                    </div>
                    <p class="text-gray-500 text-sm mt-1">Ekstraksi data penggunaan obat dan BHP pasien rawat jalan.</p>
                </div>
            </div>
            <div class="flex items-center gap-2 text-sm font-medium text-primary bg-primary/10 px-4 py-2 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                </svg>
                <span>Real-time Extraction</span>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <form action="{{ route('extraction_ralan.tarik') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
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
                        Tarik Data
                    </button>
                </div>
            </form>
        </div>

        @if(isset($data))
            <!-- Export Section -->
            <div class="flex items-center gap-4">
                <a href="{{ route('extraction_ralan.export.excel', request()->all()) }}" 
                    class="bg-white border border-gray-200 text-gray-700 font-bold px-6 py-3 rounded-xl hover:bg-gray-50 transition shadow-sm flex items-center gap-2 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    Ekspor Excel
                </a>
                <a href="{{ route('extraction_ralan.export.pdf', request()->all()) }}" 
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
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">No. Rawat</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Pasien</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-center">JK</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-center">Detail Obat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($data as $row)
                                @php
                                    $rowObat = $obat[$row->no_rawat] ?? collect();
                                    $jumlah = $rowObat->count();
                                    $rowId = 'obat-' . md5($row->no_rawat);
                                @endphp
                                <tr class="hover:bg-gray-50/30 transition-colors">
                                    <td class="px-6 py-5 text-sm font-black text-primary">{{ $row->no_rawat }}</td>
                                    <td class="px-6 py-5">
                                        <div class="text-sm font-black text-gray-800 uppercase">{{ $row->nm_pasien }}</div>
                                        <div class="text-[10px] font-bold text-gray-400 mt-0.5 tracking-widest">{{ $row->umur }}</div>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-[10px] font-black uppercase {{ $row->jk == 'L' ? 'bg-blue-50 text-blue-600' : 'bg-pink-50 text-pink-600' }}">
                                            {{ $row->jk }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        @if($jumlah > 0)
                                            <button onclick="toggleObat('{{ $rowId }}')" id="btn-{{ $rowId }}"
                                                class="inline-flex items-center gap-2 bg-blue-50 text-blue-700 border border-blue-200 text-[10px] font-black px-4 py-2 rounded-xl hover:bg-blue-100 transition shadow-sm">
                                                <span id="icon-{{ $rowId }}" class="transition-transform duration-200">▶</span>
                                                <span>{{ $jumlah }} ITEM</span>
                                            </button>
                                        @else
                                            <span class="text-[10px] text-gray-300 font-bold tracking-widest">—</span>
                                        @endif
                                    </td>
                                </tr>

                                @if($jumlah > 0)
                                    <tr id="{{ $rowId }}" class="hidden bg-blue-50/20">
                                        <td colspan="4" class="px-6 py-4">
                                            <div class="rounded-2xl border border-blue-100 overflow-hidden shadow-inner bg-white/50">
                                                <table class="w-full text-left">
                                                    <thead>
                                                        <tr class="bg-blue-50/50">
                                                            <th class="px-6 py-3 text-[10px] font-black text-blue-600 uppercase tracking-widest">Nama Obat / BHP</th>
                                                            <th class="px-6 py-3 text-[10px] font-black text-blue-600 uppercase tracking-widest text-center w-32">Jumlah</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-blue-50">
                                                        @foreach($rowObat as $item)
                                                            <tr>
                                                                <td class="px-6 py-3 text-xs font-black text-gray-700">
                                                                    {{ $item->nama_brng }}
                                                                </td>
                                                                <td class="px-6 py-3 text-xs text-gray-800 font-black text-center">
                                                                    {{ (float) $item->jml }}
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
                                    <td colspan="4" class="px-6 py-20 text-center">
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-gray-800 mb-3 uppercase tracking-tighter italic">Pilih Periode Tanggal</h3>
                <p class="text-gray-400 text-sm max-w-sm font-medium">Silakan tentukan rentang tanggal untuk melakukan penarikan data rawat jalan unit farmasi.</p>
            </div>
        @endif
    </div>

    <!-- Modal Informasi Penarikan Data Rawat Jalan -->
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
                        <h3 class="text-lg font-black uppercase tracking-wider">Informasi Penarikan Data Rawat Jalan</h3>
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
                            Menu **Penarikan Data Rawat Jalan** digunakan untuk mengekstraksi seluruh data penggunaan obat, alkes, dan barang habis pakai (BHP) medis dari pasien rawat jalan pada periode registrasi yang dipilih. Hal ini sangat berguna untuk melakukan audit terapi obat dan inventarisasi farmasi.
                        </p>
                    </div>

                    <div>
                        <h4 class="text-sm font-black text-gray-800 uppercase tracking-wider mb-2 border-b pb-1">2. Aturan & Perhitungan data</h4>
                        <ul class="list-disc pl-5 space-y-3 text-sm text-gray-600">
                            <li>
                                <strong>Penyaringan Transaksi:</strong> Data ditarik berdasarkan periode **Tanggal Registrasi** pasien di unit rawat jalan.
                            </li>
                            <li>
                                <strong>Detail Item Obat:</strong> Tombol interaktif menampilkan rincian nama obat beserta kuantitas fisik yang didistribusikan kepada masing-masing pasien secara real-time.
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
                                        <td class="p-2 font-semibold text-gray-700">No. Rawat</td>
                                        <td class="p-2 font-mono text-primary">reg_periksa</td>
                                        <td class="p-2 font-mono text-primary">no_rawat</td>
                                        <td class="p-2 text-gray-600">Nomor registrasi unik perawatan pasien.</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold text-gray-700">Nama Pasien</td>
                                        <td class="p-2 font-mono text-primary">pasien</td>
                                        <td class="p-2 font-mono text-primary">nm_pasien</td>
                                        <td class="p-2 text-gray-600">Nama lengkap pasien.</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold text-gray-700">Jenis Kelamin</td>
                                        <td class="p-2 font-mono text-primary">pasien</td>
                                        <td class="p-2 font-mono text-primary">jk</td>
                                        <td class="p-2 text-gray-600">JK pasien (L = Laki-laki, P = Perempuan).</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold text-gray-700">Detail Terapi Obat</td>
                                        <td class="p-2 font-mono text-primary">detail_pemberian_obat</td>
                                        <td class="p-2 font-mono text-primary">kode_brng, jml</td>
                                        <td class="p-2 text-gray-600">Nama obat terelasi ke master `databarang` dan total kuantitas diserahkan.</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold text-gray-700">Filter Tanggal</td>
                                        <td class="p-2 font-mono text-primary">reg_periksa</td>
                                        <td class="p-2 font-mono text-primary">tgl_registrasi</td>
                                        <td class="p-2 text-gray-600">Kueri dibatasi status registrasi `status_lanjut = 'Ralan'`.</td>
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
        function toggleObat(rowId) {
            const row = document.getElementById(rowId);
            const icon = document.getElementById('icon-' + rowId);
            const isHidden = row.classList.contains('hidden');

            row.classList.toggle('hidden', !isHidden);
            icon.textContent = isHidden ? '▼' : '▶';
            icon.style.transform = isHidden ? 'rotate(90deg)' : 'rotate(0deg)';
        }

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