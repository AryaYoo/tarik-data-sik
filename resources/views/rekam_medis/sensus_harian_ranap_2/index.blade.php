@extends('layouts.app')

@section('title', 'Sensus Harian Rawat Inap 2 (Rincian per Pasien)')

@section('content')
<div class="space-y-6" x-data="{
    infoModalOpen: false,
    search: '',
    perPage: 25,
    currentPage: 1,
    patients: {{ isset($data['patients']) ? json_encode($data['patients']) : '[]' }},
    get filteredPatients() {
        if (!this.search.trim()) return this.patients;
        const q = this.search.toLowerCase();
        return this.patients.filter(p => 
            (p.no_rawat && p.no_rawat.toLowerCase().includes(q)) ||
            (p.no_rkm_medis && p.no_rkm_medis.toLowerCase().includes(q)) ||
            (p.nm_pasien && p.nm_pasien.toLowerCase().includes(q)) ||
            (p.nm_bangsal && p.nm_bangsal.toLowerCase().includes(q)) ||
            (p.jenis_pelayanan && p.jenis_pelayanan.toLowerCase().includes(q)) ||
            (p.pasien_masuk && p.pasien_masuk.toLowerCase().includes(q))
        );
    },
    get paginatedPatients() {
        if (this.perPage === 'all') return this.filteredPatients;
        const start = (this.currentPage - 1) * parseInt(this.perPage);
        return this.filteredPatients.slice(start, start + parseInt(this.perPage));
    },
    get totalPages() {
        if (this.perPage === 'all') return 1;
        return Math.ceil(this.filteredPatients.length / parseInt(this.perPage)) || 1;
    }
}">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-gray-800">Sensus Harian Rawat Inap 2</h1>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                    Rincian per Pasien
                </span>
                <!-- Tombol Info Bantuan (SOP 7) -->
                <button @click="infoModalOpen = true"
                    class="p-1.5 rounded-full text-gray-400 hover:text-primary hover:bg-gray-50 transition"
                    title="Panduan dan Dokumentasi Data">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </button>
            </div>
            <p class="text-sm text-gray-500 mt-1">
                Laporan granular pergerakan dan hari perawatan setiap pasien rawat inap (Umum & BPJS, Pasien Awal, Masuk, Pindah, Keluar, Hari Perawatan per Kelas & Alokasi TT).
            </p>
        </div>

        @if($data && count($data['patients']) > 0)
        <!-- Action Buttons (SOP 6: handleDownload) -->
        <div class="flex flex-wrap items-center gap-2">
            <button type="button"
                onclick="handleDownload('{{ route('rekam_medis.sensus_harian_ranap_2.export.excel', request()->all()) }}', 'sensus-harian-ranap-2-{{ request('bulan') }}.xlsx')"
                class="inline-flex items-center px-4 py-2 border border-emerald-600 text-emerald-600 hover:bg-emerald-50 rounded-xl text-sm font-semibold transition shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                Export Excel
            </button>
            <button type="button"
                onclick="handleDownload('{{ route('rekam_medis.sensus_harian_ranap_2.export.pdf', request()->all()) }}', 'sensus-harian-ranap-2-{{ request('bulan') }}.pdf')"
                class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white hover:bg-emerald-700 rounded-xl text-sm font-semibold transition shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                    </path>
                </svg>
                Cetak PDF
            </button>
        </div>
        @endif
    </div>

    <!-- Filter Form -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <form action="{{ route('rekam_medis.sensus_harian_ranap_2.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label for="bulan" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                    Bulan Sensus <span class="text-red-500">*</span>
                </label>
                <input type="month" id="bulan" name="bulan"
                    value="{{ request('bulan', now()->format('Y-m')) }}" required
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
            </div>

            <div>
                <label for="kd_bangsal" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                    Ruang / Bangsal
                </label>
                <select id="kd_bangsal" name="kd_bangsal"
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
                    <option value="">-- Semua Bangsal --</option>
                    @foreach($bangsalList as $b)
                        <option value="{{ $b->kd_bangsal }}" {{ request('kd_bangsal') == $b->kd_bangsal ? 'selected' : '' }}>
                            {{ $b->nm_bangsal }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="jenis_pelayanan" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                    Jenis Pelayanan
                </label>
                <select id="jenis_pelayanan" name="jenis_pelayanan"
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
                    <option value="">-- Semua (Umum & BPJS) --</option>
                    <option value="BPJS" {{ request('jenis_pelayanan') == 'BPJS' ? 'selected' : '' }}>BPJS Kesehatan</option>
                    <option value="Umum" {{ request('jenis_pelayanan') == 'Umum' ? 'selected' : '' }}>Umum / Non-BPJS</option>
                </select>
            </div>

            <div>
                <button type="submit"
                    class="w-full px-5 py-2.5 bg-emerald-600 text-white rounded-xl text-sm font-semibold hover:bg-emerald-700 transition flex items-center justify-center shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                        </path>
                    </svg>
                    Tarik Data
                </button>
            </div>
        </form>
    </div>

    @if(!$data)
        <!-- Empty State (SOP 3) -->
        <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center shadow-sm">
            <div class="w-16 h-16 mx-auto mb-4 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-800 mb-1">Data Sensus Belum Ditarik</h3>
            <p class="text-gray-500 text-sm max-w-md mx-auto mb-6">
                Silakan tentukan bulan sensus dan filter bangsal di atas, lalu klik <strong>Tarik Data</strong> untuk memuat daftar rincian pasien rawat inap.
            </p>
        </div>
    @else
        <!-- KPI Cards Summary Section -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <!-- 1. Alokasi TT Awal Bulan -->
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm relative overflow-hidden">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Alokasi TT Awal</div>
                <div class="text-2xl font-black text-gray-800 mt-1">{{ number_format($data['alokasi_tt_total'] ?? 0) }} <span class="text-xs font-medium text-gray-400">TT</span></div>
                <div class="text-[10px] text-gray-500 mt-1 truncate">
                    @if(!empty($data['tt_per_kelas']))
                        @foreach($data['tt_per_kelas'] as $kls => $cnt)
                            <span class="inline-block mr-1.5">{{ $kls }}: {{ $cnt }}</span>
                        @endforeach
                    @else
                        Semua Kelas Terdaftar
                    @endif
                </div>
                <div class="absolute top-3 right-3 text-emerald-500/20">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                </div>
            </div>

            <!-- 2. Pasien Awal Bulan -->
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm relative overflow-hidden">
                <div class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Pasien Awal Bulan</div>
                <div class="text-2xl font-black text-emerald-700 mt-1">{{ number_format($data['summary']['pasien_awal_bulan'] ?? 0) }}</div>
                <div class="text-[11px] text-gray-500 mt-1">Sisa bulan lalu</div>
                <div class="absolute top-3 right-3 text-emerald-500/20">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>

            <!-- 3. Pasien Masuk -->
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm relative overflow-hidden">
                <div class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Pasien Masuk</div>
                <div class="text-2xl font-black text-blue-700 mt-1">{{ number_format($data['summary']['pasien_masuk'] ?? 0) }}</div>
                <div class="text-[10px] text-gray-500 mt-1">
                    IGD: {{ $data['summary']['masuk_igd'] ?? 0 }} | Obgyn: {{ $data['summary']['masuk_obgyn'] ?? 0 }} | Anak: {{ $data['summary']['masuk_anak'] ?? 0 }} | Bedah: {{ $data['summary']['masuk_bedah'] ?? 0 }}
                </div>
                <div class="absolute top-3 right-3 text-blue-500/20">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                </div>
            </div>

            <!-- 4. Pindahan & Dipindahkan -->
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm relative overflow-hidden">
                <div class="text-xs font-semibold text-purple-600 uppercase tracking-wider">Mutasi Ruangan</div>
                <div class="text-2xl font-black text-purple-700 mt-1">
                    {{ $data['summary']['pasien_pindahan'] ?? 0 }} <span class="text-xs font-normal text-gray-400">/</span> {{ $data['summary']['pasien_dipindahkan'] ?? 0 }}
                </div>
                <div class="text-[11px] text-gray-500 mt-1">Pindahan / Dipindahkan</div>
                <div class="absolute top-3 right-3 text-purple-500/20">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                    </svg>
                </div>
            </div>

            <!-- 5. Pasien Keluar Hidup & Mati -->
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm relative overflow-hidden">
                <div class="text-xs font-semibold text-amber-600 uppercase tracking-wider">Keluar Hidup / Mati</div>
                <div class="text-2xl font-black text-gray-800 mt-1">
                    <span class="text-emerald-600">{{ $data['summary']['pasien_keluar_hidup'] ?? 0 }}</span>
                    <span class="text-xs font-normal text-gray-400">/</span>
                    <span class="text-rose-600">{{ ($data['summary']['mati_48_pria'] ?? 0) + ($data['summary']['mati_48_wanita'] ?? 0) }}</span>
                </div>
                <div class="text-[10px] text-gray-500 mt-1">
                    Mati ≥48j: L({{ $data['summary']['mati_48_pria'] ?? 0 }}) P({{ $data['summary']['mati_48_wanita'] ?? 0 }})
                </div>
                <div class="absolute top-3 right-3 text-amber-500/20">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                </div>
            </div>

            <!-- 6. Total Hari Perawatan -->
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm relative overflow-hidden">
                <div class="text-xs font-semibold text-teal-600 uppercase tracking-wider">Total Hari Rawat</div>
                <div class="text-2xl font-black text-teal-700 mt-1">{{ number_format($data['summary']['total_hp_all'] ?? 0) }} <span class="text-xs font-medium text-gray-400">Hari</span></div>
                <div class="text-[10px] text-gray-500 mt-1 truncate">
                    VVIP:{{ $data['summary']['total_hp_vvip'] ?? 0 }} VIP:{{ $data['summary']['total_hp_vip'] ?? 0 }} K1:{{ $data['summary']['total_hp_kelas_1'] ?? 0 }} K2:{{ $data['summary']['total_hp_kelas_2'] ?? 0 }} K3:{{ $data['summary']['total_hp_kelas_3'] ?? 0 }}
                </div>
                <div class="absolute top-3 right-3 text-teal-500/20">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Detail Table Section -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <!-- Table Header Toolbar -->
            <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h3 class="font-bold text-gray-800 text-base">Rincian Data Sensus per Pasien</h3>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Menampilkan <span class="font-semibold text-gray-700" x-text="filteredPatients.length"></span> dari total <span class="font-semibold text-gray-700">{{ count($data['patients']) }}</span> pasien rawat inap
                    </p>
                </div>
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <!-- Search Input -->
                    <div class="relative flex-1 sm:w-64">
                        <input type="text" x-model="search" @input="currentPage = 1"
                            placeholder="Cari No. Rawat / Pasien / Bangsal..."
                            class="w-full pl-9 pr-4 py-2 rounded-xl border border-gray-200 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>

                    <!-- Per Page Select -->
                    <select x-model="perPage" @change="currentPage = 1"
                        class="px-3 py-2 rounded-xl border border-gray-200 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
                        <option value="25">25 per hal</option>
                        <option value="50">50 per hal</option>
                        <option value="100">100 per hal</option>
                        <option value="all">Semua</option>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs whitespace-nowrap">
                    <thead>
                        <tr class="bg-gray-50/80 border-b border-gray-200 text-gray-600 font-bold">
                            <th class="py-3 px-3 text-center w-10">No</th>
                            <th class="py-3 px-3">No. Rawat & RM</th>
                            <th class="py-3 px-3">Nama Pasien</th>
                            <th class="py-3 px-2 text-center">L/P</th>
                            <th class="py-3 px-3">Ruang / Kamar</th>
                            <th class="py-3 px-3 text-center">Jenis Pelayanan</th>
                            <th class="py-3 px-3 text-center bg-emerald-50/50">Pasien Awal Bulan</th>
                            <th class="py-3 px-3 text-center bg-blue-50/50">Pasien Masuk</th>
                            <th class="py-3 px-3 text-center bg-purple-50/50">Pindahan</th>
                            <th class="py-3 px-3 text-center bg-purple-50/50">Dipindahkan</th>
                            <th class="py-3 px-3 text-center bg-amber-50/50">Keluar Hidup</th>
                            <th class="py-3 px-3 text-center bg-rose-50/50">Pria Mati ≥48j</th>
                            <th class="py-3 px-3 text-center bg-rose-50/50">Wanita Mati ≥48j</th>
                            <th class="py-3 px-2 text-center bg-gray-100">HP VVIP</th>
                            <th class="py-3 px-2 text-center bg-gray-100">HP VIP</th>
                            <th class="py-3 px-2 text-center bg-gray-100">HP K1</th>
                            <th class="py-3 px-2 text-center bg-gray-100">HP K2</th>
                            <th class="py-3 px-2 text-center bg-gray-100">HP K3</th>
                            <th class="py-3 px-2 text-center bg-gray-100">HP Khusus</th>
                            <th class="py-3 px-3 text-center bg-teal-50 font-black text-teal-800">Total HP</th>
                            <th class="py-3 px-3 text-center">Alokasi TT Bangsal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        <template x-for="(p, idx) in paginatedPatients" :key="p.no_rawat">
                            <tr class="hover:bg-emerald-50/30 transition">
                                <td class="py-3 px-3 text-center text-gray-400 font-medium"
                                    x-text="perPage === 'all' ? (idx + 1) : ((currentPage - 1) * parseInt(perPage) + idx + 1)"></td>
                                <td class="py-3 px-3">
                                    <div class="font-semibold text-gray-900" x-text="p.no_rawat"></div>
                                    <div class="text-[11px] text-gray-400" x-text="'RM: ' + p.no_rkm_medis"></div>
                                </td>
                                <td class="py-3 px-3">
                                    <div class="font-semibold text-gray-800" x-text="p.nm_pasien"></div>
                                    <div class="text-[10px] text-gray-400" x-text="p.png_jawab"></div>
                                </td>
                                <td class="py-3 px-2 text-center font-bold"
                                    :class="p.jk === 'L' ? 'text-blue-600' : 'text-pink-600'"
                                    x-text="p.jk"></td>
                                <td class="py-3 px-3">
                                    <div class="font-medium text-gray-800" x-text="p.nm_bangsal || '-'"></div>
                                    <div class="text-[10px] text-gray-400" x-text="(p.kd_kamar || '-') + ' (' + (p.kelas_kamar || '-') + ')'"></div>
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold"
                                        :class="p.jenis_pelayanan === 'BPJS' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-800'"
                                        x-text="p.jenis_pelayanan"></span>
                                </td>
                                <td class="py-3 px-3 text-center bg-emerald-50/30 font-bold"
                                    :class="p.pasien_awal_bulan === 'Ya' ? 'text-emerald-700' : 'text-gray-300'"
                                    x-text="p.pasien_awal_bulan"></td>
                                <td class="py-3 px-3 text-center bg-blue-50/30">
                                    <span x-show="p.pasien_masuk !== '-'"
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold"
                                        :class="{
                                            'bg-rose-100 text-rose-800': p.pasien_masuk === 'IGD',
                                            'bg-purple-100 text-purple-800': p.pasien_masuk === 'Obgyn',
                                            'bg-blue-100 text-blue-800': p.pasien_masuk === 'Anak',
                                            'bg-amber-100 text-amber-800': p.pasien_masuk === 'Bedah',
                                            'bg-gray-100 text-gray-800': ['IGD','Obgyn','Anak','Bedah'].indexOf(p.pasien_masuk) === -1
                                        }"
                                        x-text="p.pasien_masuk"></span>
                                    <span x-show="p.pasien_masuk === '-'" class="text-gray-300">-</span>
                                </td>
                                <td class="py-3 px-3 text-center bg-purple-50/30 font-bold"
                                    :class="p.pasien_pindahan === 'Ya' ? 'text-purple-700' : 'text-gray-300'"
                                    x-text="p.pasien_pindahan"></td>
                                <td class="py-3 px-3 text-center bg-purple-50/30 font-bold"
                                    :class="p.pasien_dipindahkan === 'Ya' ? 'text-purple-700' : 'text-gray-300'"
                                    x-text="p.pasien_dipindahkan"></td>
                                <td class="py-3 px-3 text-center bg-amber-50/30 font-bold">
                                    <span x-show="p.pasien_keluar_hidup === 'Ya'" class="text-emerald-600">Ya</span>
                                    <span x-show="p.pasien_keluar_hidup === 'Tidak'" class="text-rose-600">Tidak</span>
                                    <span x-show="p.pasien_keluar_hidup === '-'" class="text-gray-300">-</span>
                                </td>
                                <td class="py-3 px-3 text-center bg-rose-50/30 font-bold"
                                    :class="p.mati_48_pria === 'Ya' ? 'text-rose-700' : 'text-gray-300'"
                                    x-text="p.mati_48_pria"></td>
                                <td class="py-3 px-3 text-center bg-rose-50/30 font-bold"
                                    :class="p.mati_48_wanita === 'Ya' ? 'text-rose-700' : 'text-gray-300'"
                                    x-text="p.mati_48_wanita"></td>
                                <td class="py-3 px-2 text-center bg-gray-50/50" :class="p.hp_vvip > 0 ? 'font-bold text-gray-900' : 'text-gray-300'" x-text="p.hp_vvip"></td>
                                <td class="py-3 px-2 text-center bg-gray-50/50" :class="p.hp_vip > 0 ? 'font-bold text-gray-900' : 'text-gray-300'" x-text="p.hp_vip"></td>
                                <td class="py-3 px-2 text-center bg-gray-50/50" :class="p.hp_kelas_1 > 0 ? 'font-bold text-gray-900' : 'text-gray-300'" x-text="p.hp_kelas_1"></td>
                                <td class="py-3 px-2 text-center bg-gray-50/50" :class="p.hp_kelas_2 > 0 ? 'font-bold text-gray-900' : 'text-gray-300'" x-text="p.hp_kelas_2"></td>
                                <td class="py-3 px-2 text-center bg-gray-50/50" :class="p.hp_kelas_3 > 0 ? 'font-bold text-gray-900' : 'text-gray-300'" x-text="p.hp_kelas_3"></td>
                                <td class="py-3 px-2 text-center bg-gray-50/50" :class="p.hp_khusus > 0 ? 'font-bold text-gray-900' : 'text-gray-300'" x-text="p.hp_khusus"></td>
                                <td class="py-3 px-3 text-center bg-teal-50 font-black text-teal-800" x-text="p.total_hp"></td>
                                <td class="py-3 px-3 text-center text-gray-600 font-semibold" x-text="p.alokasi_tt"></td>
                            </tr>
                        </template>

                        <tr x-show="filteredPatients.length === 0">
                            <td colspan="21" class="py-8 text-center text-gray-400">
                                Tidak ada data pasien yang sesuai dengan filter atau kata kunci pencarian.
                            </td>
                        </tr>
                    </tbody>
                    <!-- Table Footer: Grand Totals -->
                    <tfoot class="bg-gray-900 text-white font-bold border-t border-gray-200">
                        <tr>
                            <td class="py-3 px-3 text-center">TOTAL</td>
                            <td class="py-3 px-3" colspan="4">
                                <span>{{ count($data['patients']) }} Pasien Rawat Inap</span>
                            </td>
                            <td class="py-3 px-3 text-center text-[11px]">
                                Umum: {{ $data['summary']['umum_count'] ?? 0 }}<br>
                                BPJS: {{ $data['summary']['bpjs_count'] ?? 0 }}
                            </td>
                            <td class="py-3 px-3 text-center text-emerald-400">{{ $data['summary']['pasien_awal_bulan'] ?? 0 }}</td>
                            <td class="py-3 px-3 text-center text-blue-300">{{ $data['summary']['pasien_masuk'] ?? 0 }}</td>
                            <td class="py-3 px-3 text-center text-purple-300">{{ $data['summary']['pasien_pindahan'] ?? 0 }}</td>
                            <td class="py-3 px-3 text-center text-purple-300">{{ $data['summary']['pasien_dipindahkan'] ?? 0 }}</td>
                            <td class="py-3 px-3 text-center text-amber-300">{{ $data['summary']['pasien_keluar_hidup'] ?? 0 }}</td>
                            <td class="py-3 px-3 text-center text-rose-400">{{ $data['summary']['mati_48_pria'] ?? 0 }}</td>
                            <td class="py-3 px-3 text-center text-rose-400">{{ $data['summary']['mati_48_wanita'] ?? 0 }}</td>
                            <td class="py-3 px-2 text-center text-gray-300">{{ $data['summary']['total_hp_vvip'] ?? 0 }}</td>
                            <td class="py-3 px-2 text-center text-gray-300">{{ $data['summary']['total_hp_vip'] ?? 0 }}</td>
                            <td class="py-3 px-2 text-center text-gray-300">{{ $data['summary']['total_hp_kelas_1'] ?? 0 }}</td>
                            <td class="py-3 px-2 text-center text-gray-300">{{ $data['summary']['total_hp_kelas_2'] ?? 0 }}</td>
                            <td class="py-3 px-2 text-center text-gray-300">{{ $data['summary']['total_hp_kelas_3'] ?? 0 }}</td>
                            <td class="py-3 px-2 text-center text-gray-300">{{ $data['summary']['total_hp_khusus'] ?? 0 }}</td>
                            <td class="py-3 px-3 text-center text-teal-300 text-sm font-black">{{ $data['summary']['total_hp_all'] ?? 0 }}</td>
                            <td class="py-3 px-3 text-center text-gray-300">Total RS: {{ $data['alokasi_tt_total'] ?? 0 }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Table Pagination Toolbar -->
            <div x-show="perPage !== 'all' && totalPages > 1"
                class="p-4 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-3 text-xs text-gray-500">
                <div>
                    Halaman <span class="font-bold text-gray-800" x-text="currentPage"></span> dari <span class="font-bold text-gray-800" x-text="totalPages"></span>
                </div>
                <div class="flex items-center gap-1">
                    <button type="button" @click="currentPage = Math.max(1, currentPage - 1)"
                        :disabled="currentPage === 1"
                        class="px-3 py-1.5 rounded-lg border border-gray-200 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed transition font-medium">
                        Sebelumnya
                    </button>
                    <template x-for="p in Math.min(totalPages, 5)" :key="p">
                        <button type="button" @click="currentPage = p"
                            :class="currentPage === p ? 'bg-emerald-600 text-white' : 'hover:bg-gray-50 text-gray-700 border border-gray-200'"
                            class="w-8 h-8 rounded-lg font-bold text-xs transition"
                            x-text="p"></button>
                    </template>
                    <button type="button" @click="currentPage = Math.min(totalPages, currentPage + 1)"
                        :disabled="currentPage === totalPages"
                        class="px-3 py-1.5 rounded-lg border border-gray-200 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed transition font-medium">
                        Selanjutnya
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Info Dokumentasi Bantuan (SOP 7) -->
    <div x-show="infoModalOpen" x-cloak
        class="fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="infoModalOpen = false"
            class="bg-white rounded-3xl max-w-4xl w-full max-h-[90vh] overflow-y-auto shadow-2xl border border-gray-100 p-6 md:p-8 space-y-6">

            <!-- Modal Header -->
            <div class="flex justify-between items-start border-b border-gray-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                        i
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">Panduan Sensus Harian Rawat Inap 2</h2>
                        <p class="text-xs text-gray-500">Dokumentasi fungsional, formula kalkulasi, dan pemetaan basis data SIMRS Khanza</p>
                    </div>
                </div>
                <button @click="infoModalOpen = false"
                    class="text-gray-400 hover:text-gray-600 p-2 rounded-xl hover:bg-gray-50 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- 1. Deskripsi Fitur -->
            <div class="space-y-2">
                <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> 1. Deskripsi Fitur
                </h3>
                <p class="text-xs text-gray-600 leading-relaxed bg-gray-50 p-4 rounded-2xl border border-gray-100">
                    Modul <strong>Sensus Harian Rawat Inap 2 (Rincian per Pasien)</strong> menyajikan data pergerakan pasien rawat inap dalam bentuk granular per nomor rawat / pasien. Modul ini melengkapi modul Sensus Harian Rawat Inap (agregat) dengan menampilkan daftar nama pasien, riwayat mutasi ruangan, cara masuk (IGD/Obgyn/Anak/Bedah), status keluar hidup/mati, serta akumulasi hari perawatan per kelas kamar dalam periode bulan terpilih.
                </p>
            </div>

            <!-- 2. Definisi & Logika Tiap Kolom -->
            <div class="space-y-3">
                <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span> 2. Definisi & Logika Tiap Kolom
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                    <div class="bg-gray-50 p-3.5 rounded-xl border border-gray-100 space-y-1">
                        <span class="font-bold text-emerald-800">Jenis Pelayanan (Umum / BPJS)</span>
                        <p class="text-gray-600">Dideteksi dari kode penjamin (<code class="bg-white px-1 py-0.5 rounded text-gray-800">reg_periksa.kd_pj</code>). Jika mengandung kata 'BPJ' dikategorikan <strong>BPJS</strong>, selain itu dikategorikan <strong>Umum</strong>.</p>
                    </div>
                    <div class="bg-gray-50 p-3.5 rounded-xl border border-gray-100 space-y-1">
                        <span class="font-bold text-emerald-800">Pasien Awal Bulan</span>
                        <p class="text-gray-600">Pasien yang tanggal masuk rawat inapnya sebelum tanggal 1 bulan berjalan dan belum keluar hingga pergantian bulan (<code class="bg-white px-1 py-0.5 rounded text-gray-800">tgl_masuk &lt; awal_bulan</code>).</p>
                    </div>
                    <div class="bg-gray-50 p-3.5 rounded-xl border border-gray-100 space-y-1">
                        <span class="font-bold text-blue-800">Pasien Masuk (IGD/Obgyn/Anak/Bedah)</span>
                        <p class="text-gray-600">Pasien baru masuk rawat inap dalam bulan berjalan. Unit asal diklasifikasi berdasarkan nama poliklinik perujuk awal (<code class="bg-white px-1 py-0.5 rounded text-gray-800">poliklinik.nm_poli</code>).</p>
                    </div>
                    <div class="bg-gray-50 p-3.5 rounded-xl border border-gray-100 space-y-1">
                        <span class="font-bold text-purple-800">Pasien Pindahan & Dipindahkan</span>
                        <p class="text-gray-600"><strong>Pindahan</strong>: Pasien yang pindah masuk ke kamar/bangsal baru. <strong>Dipindahkan</strong>: Pasien yang mengakhiri masa rawat di ruangan sebelumnya dengan status 'Pindah Kamar'.</p>
                    </div>
                    <div class="bg-gray-50 p-3.5 rounded-xl border border-gray-100 space-y-1">
                        <span class="font-bold text-amber-800">Pasien Keluar Hidup</span>
                        <p class="text-gray-600">Pasien yang pulang dalam bulan berjalan dengan status selain 'Meninggal' dan 'Pindah Kamar' (misal: Sembuh, Membaik, APS, Rujuk, Pulang Paksa).</p>
                    </div>
                    <div class="bg-gray-50 p-3.5 rounded-xl border border-gray-100 space-y-1">
                        <span class="font-bold text-rose-800">Keluar Mati ≥ 48 Jam (Pria & Wanita)</span>
                        <p class="text-gray-600">Pasien meninggal dunia setelah dirawat selama minimal 2 hari (≥ 48 jam) dihitung dari awal masuk s.d. tanggal keluar, dipisahkan berdasarkan jenis kelamin (L/P).</p>
                    </div>
                    <div class="bg-gray-50 p-3.5 rounded-xl border border-gray-100 space-y-1">
                        <span class="font-bold text-teal-800">Hari Perawatan per Kelas Kamar</span>
                        <p class="text-gray-600">Dihitung dari irisan durasi menginap pasien di kamar bersangkutan dalam rentang bulan sensus, dikelompokkan ke VVIP, VIP, Kelas 1, 2, 3, dan Khusus (Utama/ICU).</p>
                    </div>
                    <div class="bg-gray-50 p-3.5 rounded-xl border border-gray-100 space-y-1">
                        <span class="font-bold text-gray-800">Alokasi TT Awal Bulan</span>
                        <p class="text-gray-600">Jumlah kapasitas Tempat Tidur aktif (<code class="bg-white px-1 py-0.5 rounded text-gray-800">kamar.statusdata = '1'</code>) pada bangsal terkait serta total kapasitas TT rumah sakit.</p>
                    </div>
                </div>
            </div>

            <!-- 3. Formula Excel Ramah Pengguna -->
            <div class="space-y-2">
                <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span> 3. Contoh Formula Kalkulasi Excel
                </h3>
                <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 text-xs font-mono space-y-1.5 text-gray-700">
                    <div><span class="text-gray-400 font-sans">Menghitung Total Pasien Awal:</span> =COUNTIF(H2:H500, "Ya")</div>
                    <div><span class="text-gray-400 font-sans">Menghitung Pasien Masuk IGD:</span> =COUNTIF(I2:I500, "IGD")</div>
                    <div><span class="text-gray-400 font-sans">Menghitung Keluar Hidup:</span> =COUNTIF(L2:L500, "Ya")</div>
                    <div><span class="text-gray-400 font-sans">Menghitung Total Hari Rawat:</span> =SUM(U2:U500)</div>
                </div>
            </div>

            <!-- 4. Pemetaan Basis Data SIMRS Khanza -->
            <div class="space-y-2">
                <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-teal-500"></span> 4. Pemetaan Basis Data (SIMRS Khanza)
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border border-gray-200 rounded-xl overflow-hidden">
                        <thead class="bg-gray-100 font-bold text-gray-700">
                            <tr>
                                <th class="p-2 border-b">Kolom Tampilan</th>
                                <th class="p-2 border-b">Tabel Database</th>
                                <th class="p-2 border-b">Field SQL</th>
                                <th class="p-2 border-b">Keterangan / Aturan Filter</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-600">
                            <tr>
                                <td class="p-2 font-semibold">Jenis Pelayanan</td>
                                <td class="p-2"><code>reg_periksa</code>, <code>penjab</code></td>
                                <td class="p-2"><code>kd_pj</code>, <code>png_jawab</code></td>
                                <td class="p-2">Jika kd_pj LIKE '%BPJ%' maka BPJS, else Umum</td>
                            </tr>
                            <tr>
                                <td class="p-2 font-semibold">Pasien Awal Bulan</td>
                                <td class="p-2"><code>kamar_inap</code></td>
                                <td class="p-2"><code>tgl_masuk</code>, <code>tgl_keluar</code></td>
                                <td class="p-2">tgl_masuk &lt; tgl_awal_bulan AND (tgl_keluar &gt;= tgl_awal_bulan OR tgl_keluar IS NULL)</td>
                            </tr>
                            <tr>
                                <td class="p-2 font-semibold">Pasien Masuk (IGD/Obgyn/Anak/Bedah)</td>
                                <td class="p-2"><code>kamar_inap</code>, <code>reg_periksa</code>, <code>poliklinik</code></td>
                                <td class="p-2"><code>tgl_masuk</code>, <code>kd_poli</code>, <code>nm_poli</code></td>
                                <td class="p-2">tgl_masuk BETWEEN awal AND akhir bulan; nm_poli dicocokkan kata kunci IGD/OBG/ANAK/BEDAH</td>
                            </tr>
                            <tr>
                                <td class="p-2 font-semibold">Pasien Pindahan / Dipindahkan</td>
                                <td class="p-2"><code>kamar_inap</code></td>
                                <td class="p-2"><code>stts_pulang</code>, <code>tgl_masuk</code>, <code>tgl_keluar</code></td>
                                <td class="p-2">Pindahan jika ada mutasi kamar masuk; Dipindahkan jika stts_pulang = 'Pindah Kamar'</td>
                            </tr>
                            <tr>
                                <td class="p-2 font-semibold">Keluar Hidup / Mati</td>
                                <td class="p-2"><code>kamar_inap</code>, <code>pasien</code></td>
                                <td class="p-2"><code>stts_pulang</code>, <code>lama</code>, <code>jk</code></td>
                                <td class="p-2">Hidup jika stts_pulang != 'Meninggal'; Mati &gt;=48j jika 'Meninggal' AND lama &gt;= 2</td>
                            </tr>
                            <tr>
                                <td class="p-2 font-semibold">Hari Perawatan per Kelas</td>
                                <td class="p-2"><code>kamar_inap</code>, <code>kamar</code></td>
                                <td class="p-2"><code>kelas</code>, <code>tgl_masuk</code>, <code>tgl_keluar</code>, <code>lama</code></td>
                                <td class="p-2">Dihitung lama menginap dalam batas tanggal bulan yang dipilih untuk masing-masing kelas kamar</td>
                            </tr>
                            <tr>
                                <td class="p-2 font-semibold">Alokasi TT Awal Bulan</td>
                                <td class="p-2"><code>kamar</code>, <code>bangsal</code></td>
                                <td class="p-2"><code>kd_bangsal</code>, <code>statusdata</code>, <code>status</code></td>
                                <td class="p-2">COUNT(*) WHERE statusdata = '1' AND status = '1'</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="border-t border-gray-100 pt-4 flex justify-end">
                <button type="button" @click="infoModalOpen = false"
                    class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs rounded-xl transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
