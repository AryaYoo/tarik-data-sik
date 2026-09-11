@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-2xl font-bold text-gray-800 uppercase tracking-tighter italic">Kunjungan Pasien RS</h2>
                        <button type="button" onclick="openInfoModal()" class="text-primary hover:text-green-800 transition duration-150 focus:outline-none" title="Informasi Formula & Sumber Data">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </button>
                    </div>
                    <p class="text-gray-500 text-sm mt-1">Ekstraksi dan rekapitulasi data kunjungan pasien rawat jalan dan rawat inap.</p>
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
            <form action="{{ route('rekam_medis.kunjungan_rs.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 items-end">
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
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Jenis Rawat</label>
                    <select name="status_lanjut" class="w-full px-4 py-3 rounded-xl bg-gray-50 border-0 focus:bg-white focus:ring-4 focus:ring-primary/10 transition outline-none text-gray-800 shadow-inner text-sm">
                        <option value="">-- Semua --</option>
                        <option value="Ralan" {{ request('status_lanjut') == 'Ralan' ? 'selected' : '' }}>Rawat Jalan</option>
                        <option value="Ranap" {{ request('status_lanjut') == 'Ranap' ? 'selected' : '' }}>Rawat Inap</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Poliklinik</label>
                    <select name="kd_poli" class="w-full px-4 py-3 rounded-xl bg-gray-50 border-0 focus:bg-white focus:ring-4 focus:ring-primary/10 transition outline-none text-gray-800 shadow-inner text-sm">
                        <option value="">-- Semua --</option>
                        @foreach($poliklinikList as $poli)
                            <option value="{{ $poli->kd_poli }}" {{ request('kd_poli') == $poli->kd_poli ? 'selected' : '' }}>
                                {{ $poli->nm_poli }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Penjamin</label>
                    <select name="kd_pj" class="w-full px-4 py-3 rounded-xl bg-gray-50 border-0 focus:bg-white focus:ring-4 focus:ring-primary/10 transition outline-none text-gray-800 shadow-inner text-sm">
                        <option value="">-- Semua --</option>
                        @foreach($penjaminList as $pj)
                            <option value="{{ $pj->kd_pj }}" {{ request('kd_pj') == $pj->kd_pj ? 'selected' : '' }}>
                                {{ $pj->png_jawab }}
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

        @if(isset($data))
            <!-- Export Section -->
            <div class="flex items-center gap-4">
                <a href="javascript:void(0)" 
                    onclick="handleDownload('{{ route('rekam_medis.kunjungan_rs.export.excel', request()->all()) }}', 'kunjungan-rs-{{ request('tgl_mulai') }}-{{ request('tgl_selesai') }}.xlsx')"
                    class="bg-white border border-gray-200 text-gray-700 font-bold px-6 py-3 rounded-xl hover:bg-gray-50 transition shadow-sm flex items-center gap-2 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    Ekspor Excel
                </a>
                <a href="javascript:void(0)" 
                    onclick="handleDownload('{{ route('rekam_medis.kunjungan_rs.export.pdf', request()->all()) }}', 'kunjungan-rs-{{ request('tgl_mulai') }}-{{ request('tgl_selesai') }}.pdf')"
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
                    <table class="w-full text-left whitespace-nowrap">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100">
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-center">No</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">No. Rawat</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Tgl Reg</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Jam</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">No. RM</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Pasien</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-center">JK</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Poliklinik</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Dokter</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Penjamin</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-center">Jns Kunjungan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($data as $index => $row)
                                <tr class="hover:bg-gray-50/30 transition-colors">
                                    <td class="px-6 py-5 text-sm font-bold text-gray-300 text-center">
                                        {{ str_pad($data->firstItem() + $index, 2, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="text-sm font-black text-gray-800 font-mono">{{ $row->no_rawat }}</div>
                                        <div class="text-[10px] font-bold text-gray-400 tracking-wider uppercase">
                                            {{ $row->status_lanjut == 'Ranap' ? 'Rawat Inap' : 'Rawat Jalan' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-sm text-gray-700 font-medium">
                                        {{ \Carbon\Carbon::parse($row->tgl_registrasi)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-5 text-sm font-bold text-gray-600 font-mono">
                                        {{ $row->jam_reg }}
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-[10px] font-black bg-gray-100 text-gray-500 tracking-tighter font-mono">
                                            {{ $row->no_rkm_medis }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="text-sm font-black text-gray-800 uppercase">{{ $row->nm_pasien }}</div>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        @if($row->jk == 'L')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black bg-blue-50 text-blue-600 tracking-tighter uppercase">
                                                L
                                            </span>
                                        @elseif($row->jk == 'P')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black bg-pink-50 text-pink-600 tracking-tighter uppercase">
                                                P
                                            </span>
                                        @else
                                            <span class="text-gray-300 text-xs">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="text-sm font-bold text-gray-700">{{ $row->nm_poli ?: '-' }}</div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="text-sm font-medium text-gray-700">{{ $row->nm_dokter ?: '-' }}</div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="inline-flex items-center px-2 py-1 rounded text-[10px] font-black bg-blue-50 text-blue-600 tracking-tighter uppercase">
                                            {{ $row->png_jawab ?: '-' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        @if($row->jns_kunjungan == 'Baru')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black bg-green-100 text-green-800 uppercase tracking-wider">
                                                Baru
                                            </span>
                                        @elseif($row->jns_kunjungan == 'Lama')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black bg-gray-100 text-gray-600 uppercase tracking-wider">
                                                Lama
                                            </span>
                                        @else
                                            <span class="text-gray-400 text-xs">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="px-6 py-20 text-center">
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
            <!-- Empty State -->
            <div class="bg-white p-20 rounded-3xl shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center">
                <div class="p-6 bg-primary/5 rounded-full mb-8">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-primary/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-gray-800 mb-3 uppercase tracking-tighter italic">Pilih Periode Tanggal</h3>
                <p class="text-gray-400 text-sm max-w-sm font-medium">Silakan tentukan rentang tanggal dan kriteria filter untuk menarik data kunjungan pasien Rumah Sakit.</p>
            </div>
        @endif
    </div>

    <!-- Modal Informasi & Pemetaan Basis Data (SOP 7) -->
    <div id="infoModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeInfoModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-100">
                <!-- Header Modal -->
                <div class="bg-primary px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-2 text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="text-lg font-black uppercase tracking-wider">Informasi Kunjungan RS</h3>
                    </div>
                    <button onclick="closeInfoModal()" class="text-white hover:text-gray-200 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Body Modal -->
                <div class="p-6 space-y-6 max-h-[80vh] overflow-y-auto">
                    <!-- Deskripsi Fitur -->
                    <div>
                        <h4 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-2">Deskripsi Fitur</h4>
                        <p class="text-gray-600 text-sm leading-relaxed">
                            Menu <strong>Kunjungan RS</strong> digunakan untuk mengekstraksi dan merekapitulasi data registrasi pasien di Rumah Sakit, baik pasien Rawat Jalan (Ralan) maupun Rawat Inap (Ranap) dalam rentang tanggal tertentu dengan opsi filter poliklinik dan penjamin.
                        </p>
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
                                        <th class="p-3">Keterangan Filter / Relasi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <tr>
                                        <td class="p-3 font-semibold text-gray-800">No. Rawat</td>
                                        <td class="p-3 text-gray-600 font-mono">reg_periksa</td>
                                        <td class="p-3 text-gray-600 font-mono">no_rawat</td>
                                        <td class="p-3 text-gray-500">Nomor registrasi unik perawatan</td>
                                    </tr>
                                    <tr>
                                        <td class="p-3 font-semibold text-gray-800">Tgl Reg & Jam</td>
                                        <td class="p-3 text-gray-600 font-mono">reg_periksa</td>
                                        <td class="p-3 text-gray-600 font-mono">tgl_registrasi, jam_reg</td>
                                        <td class="p-3 text-gray-500">Filter tanggal mulai s/d tanggal selesai</td>
                                    </tr>
                                    <tr>
                                        <td class="p-3 font-semibold text-gray-800">No. RM & Pasien</td>
                                        <td class="p-3 text-gray-600 font-mono">pasien</td>
                                        <td class="p-3 text-gray-600 font-mono">no_rkm_medis, nm_pasien, jk</td>
                                        <td class="p-3 text-gray-500">Join via <code class="bg-gray-100 px-1 py-0.5 rounded text-gray-700">reg_periksa.no_rkm_medis</code></td>
                                    </tr>
                                    <tr>
                                        <td class="p-3 font-semibold text-gray-800">Poliklinik</td>
                                        <td class="p-3 text-gray-600 font-mono">poliklinik</td>
                                        <td class="p-3 text-gray-600 font-mono">nm_poli</td>
                                        <td class="p-3 text-gray-500">Join via <code class="bg-gray-100 px-1 py-0.5 rounded text-gray-700">reg_periksa.kd_poli</code></td>
                                    </tr>
                                    <tr>
                                        <td class="p-3 font-semibold text-gray-800">Dokter</td>
                                        <td class="p-3 text-gray-600 font-mono">dokter</td>
                                        <td class="p-3 text-gray-600 font-mono">nm_dokter</td>
                                        <td class="p-3 text-gray-500">Join via <code class="bg-gray-100 px-1 py-0.5 rounded text-gray-700">reg_periksa.kd_dokter</code></td>
                                    </tr>
                                    <tr>
                                        <td class="p-3 font-semibold text-gray-800">Penjamin</td>
                                        <td class="p-3 text-gray-600 font-mono">penjab</td>
                                        <td class="p-3 text-gray-600 font-mono">png_jawab</td>
                                        <td class="p-3 text-gray-500">Join via <code class="bg-gray-100 px-1 py-0.5 rounded text-gray-700">reg_periksa.kd_pj</code></td>
                                    </tr>
                                    <tr>
                                        <td class="p-3 font-semibold text-gray-800">Jns Kunjungan</td>
                                        <td class="p-3 text-gray-600 font-mono">reg_periksa</td>
                                        <td class="p-3 text-gray-600 font-mono">stts_daftar</td>
                                        <td class="p-3 text-gray-500">Status kunjungan pendaftaran (Baru / Lama)</td>
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
