@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-2xl font-bold text-gray-800 uppercase tracking-tighter italic">Sensus Harian Rawat Inap</h2>
                        <button type="button" onclick="openInfoModal()" class="text-primary hover:text-green-800 transition duration-150 focus:outline-none" title="Informasi Formula & Sumber Data">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </button>
                    </div>
                    <p class="text-gray-500 text-sm mt-1">Rekapitulasi statistik pergerakan pasien rawat inap per bulan: masuk, keluar, pindah, dan hari perawatan.</p>
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
            <form action="{{ route('rekam_medis.sensus_harian_ranap.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 items-end">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Bulan Pelaporan</label>
                    <input type="month" name="bulan" value="{{ request('bulan', date('Y-m')) }}" required
                        class="w-full px-4 py-3 rounded-xl bg-gray-50 border-0 focus:bg-white focus:ring-4 focus:ring-primary/10 transition outline-none text-gray-800 shadow-inner text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Bangsal / Ruangan</label>
                    <select name="kd_bangsal" class="w-full px-4 py-3 rounded-xl bg-gray-50 border-0 focus:bg-white focus:ring-4 focus:ring-primary/10 transition outline-none text-gray-800 shadow-inner text-sm">
                        <option value="">-- Semua Bangsal --</option>
                        @foreach($bangsalList as $bangsal)
                            <option value="{{ $bangsal->kd_bangsal }}" {{ request('kd_bangsal') == $bangsal->kd_bangsal ? 'selected' : '' }}>
                                {{ $bangsal->nm_bangsal }}
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
                    onclick="handleDownload('{{ route('rekam_medis.sensus_harian_ranap.export.excel', request()->all()) }}', 'sensus-harian-ranap-{{ request('bulan') }}.xlsx')"
                    class="bg-white border border-gray-200 text-gray-700 font-bold px-6 py-3 rounded-xl hover:bg-gray-50 transition shadow-sm flex items-center gap-2 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    Ekspor Excel
                </a>
                <a href="javascript:void(0)" 
                    onclick="handleDownload('{{ route('rekam_medis.sensus_harian_ranap.export.pdf', request()->all()) }}', 'sensus-harian-ranap-{{ request('bulan') }}.pdf')"
                    class="bg-white border border-gray-200 text-gray-700 font-bold px-6 py-3 rounded-xl hover:bg-gray-50 transition shadow-sm flex items-center gap-2 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    Ekspor PDF
                </a>
            </div>

            <!-- KPI Summary Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <!-- Pasien Awal Bulan -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="p-2.5 bg-blue-50 rounded-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Awal Bulan</span>
                    </div>
                    <p class="text-3xl font-black text-gray-800">{{ $data['total']['pasien_awal_bulan'] }}</p>
                    <p class="text-[10px] text-gray-400 mt-1 font-semibold uppercase tracking-wider">Pasien Sisa Bulan Lalu</p>
                </div>

                <!-- Pasien Masuk -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="p-2.5 bg-green-50 rounded-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                        </div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Masuk</span>
                    </div>
                    <p class="text-3xl font-black text-gray-800">{{ $data['total']['pasien_masuk'] }}</p>
                    <p class="text-[10px] text-gray-400 mt-1 font-semibold uppercase tracking-wider">Pasien Masuk Bulan Ini</p>
                </div>

                <!-- Pasien Keluar Hidup -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="p-2.5 bg-emerald-50 rounded-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Keluar Hidup</span>
                    </div>
                    <p class="text-3xl font-black text-emerald-600">{{ $data['total']['pasien_keluar_hidup'] }}</p>
                    <p class="text-[10px] text-gray-400 mt-1 font-semibold uppercase tracking-wider">Pasien Pulang / Sembuh</p>
                </div>

                <!-- Alokasi TT -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="p-2.5 bg-purple-50 rounded-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Alokasi TT</span>
                    </div>
                    <p class="text-3xl font-black text-purple-600">{{ $data['alokasi_tt'] }}</p>
                    <p class="text-[10px] text-gray-400 mt-1 font-semibold uppercase tracking-wider">Tempat Tidur Tersedia</p>
                </div>
            </div>

            <!-- Main Statistics Table -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-black text-gray-800 uppercase tracking-tighter italic">Rekapitulasi Sensus</h3>
                        <p class="text-xs text-gray-400 mt-0.5 font-medium">Periode: {{ \Carbon\Carbon::parse(request('bulan') . '-01')->translatedFormat('F Y') }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-[10px] font-bold bg-blue-50 text-blue-600 uppercase tracking-wider">Umum</span>
                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-[10px] font-bold bg-green-50 text-green-600 uppercase tracking-wider">BPJS</span>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left whitespace-nowrap">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100">
                                <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest" colspan="2">Indikator</th>
                                <th class="px-6 py-4 text-xs font-black text-blue-500 uppercase tracking-widest text-center">Umum</th>
                                <th class="px-6 py-4 text-xs font-black text-green-500 uppercase tracking-widest text-center">BPJS</th>
                                <th class="px-6 py-4 text-xs font-black text-gray-800 uppercase tracking-widest text-center">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <!-- SECTION: Pergerakan Pasien -->
                            <tr class="bg-primary/5">
                                <td colspan="5" class="px-6 py-3">
                                    <span class="text-xs font-black text-primary uppercase tracking-widest flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Pergerakan Pasien
                                    </span>
                                </td>
                            </tr>

                            @php
                                $rows = [
                                    ['label' => 'Pasien Awal Bulan', 'key' => 'pasien_awal_bulan', 'icon' => 'calendar', 'sub' => false, 'desc' => 'Sisa pasien bulan sebelumnya'],
                                    ['label' => 'Pasien Masuk', 'key' => 'pasien_masuk', 'icon' => 'login', 'sub' => false, 'desc' => 'Total pasien masuk rawat inap'],
                                    ['label' => 'Via IGD', 'key' => 'pasien_masuk_igd', 'icon' => null, 'sub' => true, 'desc' => null],
                                    ['label' => 'Via Obgyn / Kandungan', 'key' => 'pasien_masuk_obgyn', 'icon' => null, 'sub' => true, 'desc' => null],
                                    ['label' => 'Via Anak', 'key' => 'pasien_masuk_anak', 'icon' => null, 'sub' => true, 'desc' => null],
                                    ['label' => 'Via Bedah', 'key' => 'pasien_masuk_bedah', 'icon' => null, 'sub' => true, 'desc' => null],
                                    ['label' => 'Via Lainnya', 'key' => 'pasien_masuk_lainnya', 'icon' => null, 'sub' => true, 'desc' => null],
                                    ['label' => 'Pasien Pindahan', 'key' => 'pasien_pindahan', 'icon' => 'switch', 'sub' => false, 'desc' => 'Masuk dari bangsal/kamar lain'],
                                    ['label' => 'Pasien Dipindahkan', 'key' => 'pasien_dipindahkan', 'icon' => 'switch-out', 'sub' => false, 'desc' => 'Keluar ke bangsal/kamar lain'],
                                ];
                            @endphp

                            @foreach($rows as $row)
                                <tr class="hover:bg-gray-50/30 transition-colors {{ $row['sub'] ? '' : 'font-semibold' }}">
                                    <td class="pl-6 pr-2 py-4 w-8">
                                        @if($row['sub'])
                                            <span class="text-gray-300 text-xs">└</span>
                                        @endif
                                    </td>
                                    <td class="pr-6 py-4 {{ $row['sub'] ? 'pl-4' : '' }}">
                                        <div class="text-sm {{ $row['sub'] ? 'text-gray-500 font-medium' : 'text-gray-800 font-bold' }}">
                                            {{ $row['label'] }}
                                        </div>
                                        @if($row['desc'])
                                            <div class="text-[10px] text-gray-400 font-medium">{{ $row['desc'] }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center justify-center min-w-[40px] px-3 py-1 rounded-lg text-sm font-bold {{ $row['sub'] ? 'bg-blue-50/50 text-blue-500' : 'bg-blue-50 text-blue-700' }}">
                                            {{ $data['umum'][$row['key']] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center justify-center min-w-[40px] px-3 py-1 rounded-lg text-sm font-bold {{ $row['sub'] ? 'bg-green-50/50 text-green-500' : 'bg-green-50 text-green-700' }}">
                                            {{ $data['bpjs'][$row['key']] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center justify-center min-w-[40px] px-3 py-1 rounded-lg text-sm font-black {{ $row['sub'] ? 'bg-gray-50 text-gray-600' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $data['total'][$row['key']] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach

                            <!-- SECTION: Pasien Keluar -->
                            <tr class="bg-amber-50/50">
                                <td colspan="5" class="px-6 py-3">
                                    <span class="text-xs font-black text-amber-600 uppercase tracking-widest flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        Pasien Keluar
                                    </span>
                                </td>
                            </tr>

                            <tr class="hover:bg-gray-50/30 transition-colors font-semibold">
                                <td class="pl-6 pr-2 py-4"></td>
                                <td class="pr-6 py-4">
                                    <div class="text-sm text-gray-800 font-bold">Pasien Keluar Hidup</div>
                                    <div class="text-[10px] text-gray-400 font-medium">Sehat / Sembuh / Membaik / APS / Lain-lain</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center min-w-[40px] px-3 py-1 rounded-lg text-sm font-bold bg-emerald-50 text-emerald-700">
                                        {{ $data['umum']['pasien_keluar_hidup'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center min-w-[40px] px-3 py-1 rounded-lg text-sm font-bold bg-emerald-50 text-emerald-700">
                                        {{ $data['bpjs']['pasien_keluar_hidup'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center min-w-[40px] px-3 py-1 rounded-lg text-sm font-black bg-emerald-100 text-emerald-800">
                                        {{ $data['total']['pasien_keluar_hidup'] }}
                                    </span>
                                </td>
                            </tr>

                            <tr class="hover:bg-gray-50/30 transition-colors font-semibold">
                                <td class="pl-6 pr-2 py-4"></td>
                                <td class="pr-6 py-4">
                                    <div class="text-sm text-gray-800 font-bold">Pasien Keluar Mati</div>
                                    <div class="text-[10px] text-gray-400 font-medium">Total meninggal</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center min-w-[40px] px-3 py-1 rounded-lg text-sm font-bold bg-red-50 text-red-700">
                                        {{ $data['umum']['pasien_keluar_mati'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center min-w-[40px] px-3 py-1 rounded-lg text-sm font-bold bg-red-50 text-red-700">
                                        {{ $data['bpjs']['pasien_keluar_mati'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center min-w-[40px] px-3 py-1 rounded-lg text-sm font-black bg-red-100 text-red-800">
                                        {{ $data['total']['pasien_keluar_mati'] }}
                                    </span>
                                </td>
                            </tr>

                            @php
                                $matiRows = [
                                    ['label' => 'Pria Mati ≥48 Jam', 'key' => 'mati_48_pria'],
                                    ['label' => 'Wanita Mati ≥48 Jam', 'key' => 'mati_48_wanita'],
                                    ['label' => 'Pria Mati <48 Jam', 'key' => 'mati_kurang_48_pria'],
                                    ['label' => 'Wanita Mati <48 Jam', 'key' => 'mati_kurang_48_wanita'],
                                ];
                            @endphp

                            @foreach($matiRows as $mr)
                                <tr class="hover:bg-gray-50/30 transition-colors">
                                    <td class="pl-6 pr-2 py-4">
                                        <span class="text-gray-300 text-xs">└</span>
                                    </td>
                                    <td class="pr-6 py-4 pl-4">
                                        <div class="text-sm text-gray-500 font-medium">{{ $mr['label'] }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center justify-center min-w-[40px] px-3 py-1 rounded-lg text-sm font-bold bg-red-50/50 text-red-500">
                                            {{ $data['umum'][$mr['key']] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center justify-center min-w-[40px] px-3 py-1 rounded-lg text-sm font-bold bg-red-50/50 text-red-500">
                                            {{ $data['bpjs'][$mr['key']] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center justify-center min-w-[40px] px-3 py-1 rounded-lg text-sm font-bold bg-gray-50 text-gray-600">
                                            {{ $data['total'][$mr['key']] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach

                            <!-- SECTION: Hari Perawatan -->
                            <tr class="bg-indigo-50/50">
                                <td colspan="5" class="px-6 py-3">
                                    <span class="text-xs font-black text-indigo-600 uppercase tracking-widest flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Hari Perawatan (per Kelas Kamar)
                                    </span>
                                </td>
                            </tr>

                            @php
                                $hpRows = [
                                    ['label' => 'HP VVIP', 'key' => 'hp_vvip', 'kelas' => 'VVIP'],
                                    ['label' => 'HP VIP', 'key' => 'hp_vip', 'kelas' => 'VIP'],
                                    ['label' => 'HP Kelas 1', 'key' => 'hp_kelas_1', 'kelas' => 'K1'],
                                    ['label' => 'HP Kelas 2', 'key' => 'hp_kelas_2', 'kelas' => 'K2'],
                                    ['label' => 'HP Kelas 3', 'key' => 'hp_kelas_3', 'kelas' => 'K3'],
                                    ['label' => 'HP Khusus (Kelas Utama)', 'key' => 'hp_khusus', 'kelas' => 'Utama'],
                                ];
                            @endphp

                            @foreach($hpRows as $hp)
                                <tr class="hover:bg-gray-50/30 transition-colors">
                                    <td class="pl-6 pr-2 py-4"></td>
                                    <td class="pr-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-black bg-indigo-100 text-indigo-700 uppercase tracking-wider">{{ $hp['kelas'] }}</span>
                                            <span class="text-sm text-gray-700 font-medium">{{ $hp['label'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center justify-center min-w-[40px] px-3 py-1 rounded-lg text-sm font-bold bg-indigo-50/50 text-indigo-500">
                                            {{ $data['umum'][$hp['key']] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center justify-center min-w-[40px] px-3 py-1 rounded-lg text-sm font-bold bg-indigo-50/50 text-indigo-500">
                                            {{ $data['bpjs'][$hp['key']] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center justify-center min-w-[40px] px-3 py-1 rounded-lg text-sm font-bold bg-gray-50 text-gray-600">
                                            {{ $data['total'][$hp['key']] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach

                            <!-- Total Hari Perawatan -->
                            <tr class="bg-indigo-50/30 font-semibold border-t-2 border-indigo-100">
                                <td class="pl-6 pr-2 py-4"></td>
                                <td class="pr-6 py-4">
                                    <div class="text-sm text-gray-800 font-black uppercase">Total Hari Perawatan</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center min-w-[40px] px-3 py-1.5 rounded-lg text-sm font-black bg-indigo-100 text-indigo-800">
                                        {{ $data['umum']['total_hari_perawatan'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center min-w-[40px] px-3 py-1.5 rounded-lg text-sm font-black bg-indigo-100 text-indigo-800">
                                        {{ $data['bpjs']['total_hari_perawatan'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center min-w-[40px] px-3 py-1.5 rounded-xl text-sm font-black bg-indigo-600 text-white shadow-lg shadow-indigo-200">
                                        {{ $data['total']['total_hari_perawatan'] }}
                                    </span>
                                </td>
                            </tr>

                            <!-- SECTION: Alokasi TT -->
                            <tr class="bg-purple-50/50">
                                <td colspan="5" class="px-6 py-3">
                                    <span class="text-xs font-black text-purple-600 uppercase tracking-widest flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        Kapasitas Tempat Tidur
                                    </span>
                                </td>
                            </tr>

                            <tr class="hover:bg-gray-50/30 transition-colors">
                                <td class="pl-6 pr-2 py-4"></td>
                                <td class="pr-6 py-4">
                                    <div class="text-sm text-gray-800 font-bold">Jumlah Alokasi TT Awal Bulan</div>
                                    <div class="text-[10px] text-gray-400 font-medium">Total tempat tidur aktif (statusdata = 1)</div>
                                </td>
                                <td colspan="3" class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center px-4 py-2 rounded-xl text-lg font-black bg-purple-100 text-purple-800 shadow-sm">
                                        {{ $data['alokasi_tt'] }}
                                    </span>
                                </td>
                            </tr>

                            @if(isset($data['alokasi_tt_detail']) && $data['alokasi_tt_detail']->count() > 0)
                                @foreach($data['alokasi_tt_detail'] as $kelas => $detail)
                                    <tr class="hover:bg-gray-50/30 transition-colors">
                                        <td class="pl-6 pr-2 py-3">
                                            <span class="text-gray-300 text-xs">└</span>
                                        </td>
                                        <td class="pr-6 py-3 pl-4">
                                            <span class="text-xs text-gray-500 font-medium">{{ $kelas }}</span>
                                        </td>
                                        <td colspan="3" class="px-6 py-3 text-center">
                                            <span class="inline-flex items-center justify-center min-w-[40px] px-3 py-1 rounded-lg text-sm font-bold bg-purple-50 text-purple-600">
                                                {{ $detail->jumlah }} TT
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Detail Pasien Table -->
            @if(isset($detailData) && $detailData->count() > 0)
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden" x-data="{ showDetail: false }">
                    <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between cursor-pointer" @click="showDetail = !showDetail">
                        <div>
                            <h3 class="text-lg font-black text-gray-800 uppercase tracking-tighter italic">Detail Pergerakan Pasien</h3>
                            <p class="text-xs text-gray-400 mt-0.5 font-medium">{{ $detailData->count() }} record ditemukan — klik untuk {{ 'membuka / menutup' }}</p>
                        </div>
                        <svg :class="{'rotate-180': showDetail}" class="w-5 h-5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                    <div x-show="showDetail" x-collapse>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left whitespace-nowrap">
                                <thead>
                                    <tr class="bg-gray-50/50 border-b border-gray-100">
                                        <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">No</th>
                                        <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">No. Rawat</th>
                                        <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Pasien</th>
                                        <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">JK</th>
                                        <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Kamar</th>
                                        <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Bangsal</th>
                                        <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Kelas</th>
                                        <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Tgl Masuk</th>
                                        <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Tgl Keluar</th>
                                        <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Lama</th>
                                        <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                                        <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Penjamin</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($detailData as $idx => $detail)
                                        <tr class="hover:bg-gray-50/30 transition-colors">
                                            <td class="px-4 py-3 text-xs font-bold text-gray-300 text-center">{{ str_pad($idx + 1, 2, '0', STR_PAD_LEFT) }}</td>
                                            <td class="px-4 py-3">
                                                <span class="text-xs font-black text-gray-700 font-mono">{{ $detail->no_rawat }}</span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="text-xs font-bold text-gray-800 uppercase">{{ $detail->nm_pasien }}</div>
                                                <div class="text-[10px] text-gray-400 font-mono">{{ $detail->no_rkm_medis }}</div>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                @if($detail->jk == 'L')
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-black bg-blue-50 text-blue-600">L</span>
                                                @else
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-black bg-pink-50 text-pink-600">P</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-xs font-mono font-bold text-gray-600">{{ $detail->kd_kamar }}</td>
                                            <td class="px-4 py-3 text-xs font-medium text-gray-600">{{ $detail->nm_bangsal ?? '-' }}</td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold bg-indigo-50 text-indigo-600 uppercase">{{ $detail->kelas ?? '-' }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-xs text-gray-600 font-medium">
                                                {{ \Carbon\Carbon::parse($detail->tgl_masuk)->format('d/m/Y') }}
                                                <span class="text-gray-400">{{ $detail->jam_masuk }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-xs text-gray-600 font-medium">
                                                @if($detail->tgl_keluar && $detail->tgl_keluar != '0000-00-00')
                                                    {{ \Carbon\Carbon::parse($detail->tgl_keluar)->format('d/m/Y') }}
                                                    <span class="text-gray-400">{{ $detail->jam_keluar }}</span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-yellow-50 text-yellow-600 uppercase">Masih Dirawat</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="text-xs font-bold text-gray-700">{{ $detail->lama ?? 0 }} hr</span>
                                            </td>
                                            <td class="px-4 py-3">
                                                @php
                                                    $statusColor = match($detail->stts_pulang) {
                                                        'Sehat', 'Sembuh', 'Membaik' => 'bg-green-50 text-green-700',
                                                        'Meninggal' => 'bg-red-50 text-red-700',
                                                        'Pindah Kamar' => 'bg-orange-50 text-orange-700',
                                                        '-' => 'bg-yellow-50 text-yellow-700',
                                                        default => 'bg-gray-50 text-gray-700',
                                                    };
                                                @endphp
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold {{ $statusColor }} uppercase tracking-wider">
                                                    {{ $detail->stts_pulang == '-' ? 'Dirawat' : $detail->stts_pulang }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3">
                                                @php
                                                    $isBpjs = stripos($detail->kd_pj ?? '', 'BPJ') !== false;
                                                @endphp
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold {{ $isBpjs ? 'bg-green-50 text-green-700' : 'bg-blue-50 text-blue-700' }} uppercase tracking-wider">
                                                    {{ $detail->png_jawab ?? '-' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

        @else
            <!-- Empty State -->
            <div class="bg-white p-20 rounded-3xl shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center">
                <div class="p-6 bg-primary/5 rounded-full mb-8">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-primary/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-gray-800 mb-3 uppercase tracking-tighter italic">Pilih Bulan Pelaporan</h3>
                <p class="text-gray-400 text-sm max-w-md font-medium">Silakan pilih bulan dan bangsal (opsional) untuk menarik data sensus harian rawat inap. Data akan ditampilkan dalam format rekapitulasi statistik pergerakan pasien.</p>
            </div>
        @endif
    </div>

    <!-- Modal Informasi & Pemetaan Basis Data (SOP 7) -->
    <div id="infoModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeInfoModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full border border-gray-100">
                <!-- Header Modal -->
                <div class="bg-primary px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-2 text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="text-lg font-black uppercase tracking-wider">Informasi Sensus Harian Rawat Inap</h3>
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
                            Menu <strong>Sensus Harian Rawat Inap</strong> digunakan untuk merekapitulasi statistik pergerakan pasien rawat inap di rumah sakit per bulan. Data mencakup jumlah pasien awal bulan (sisa bulan lalu), pasien masuk (berdasarkan asal poliklinik: IGD, Obgyn, Anak, Bedah), pasien pindah antar bangsal, pasien keluar (hidup/mati), hari perawatan per kelas kamar, serta alokasi tempat tidur.
                        </p>
                    </div>

                    <!-- Penjelasan Indikator -->
                    <div>
                        <h4 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-3">Penjelasan Indikator</h4>
                        <div class="space-y-3 text-sm text-gray-600">
                            <div class="bg-blue-50/50 p-3 rounded-xl">
                                <span class="font-bold text-blue-700">Pasien Awal Bulan:</span> Pasien yang masuk sebelum bulan pelaporan dan belum dipulangkan hingga awal bulan pelaporan (masih dirawat saat pergantian bulan).
                            </div>
                            <div class="bg-green-50/50 p-3 rounded-xl">
                                <span class="font-bold text-green-700">Pasien Masuk:</span> Pasien baru yang masuk rawat inap pada bulan pelaporan. Dikategorikan berdasarkan poliklinik asal pendaftaran (IGD, Obgyn/Kandungan, Anak, Bedah, Lainnya).
                            </div>
                            <div class="bg-orange-50/50 p-3 rounded-xl">
                                <span class="font-bold text-orange-700">Pasien Pindahan:</span> Pasien yang dipindahkan ke bangsal ini dari bangsal/kamar lain dalam bulan yang sama. Teridentifikasi dari record kamar_inap sebelumnya yang berstatus "Pindah Kamar".
                            </div>
                            <div class="bg-orange-50/50 p-3 rounded-xl">
                                <span class="font-bold text-orange-700">Pasien Dipindahkan:</span> Pasien yang keluar dari bangsal ini ke bangsal/kamar lain (bukan pulang). Teridentifikasi dari status pulang "Pindah Kamar".
                            </div>
                            <div class="bg-emerald-50/50 p-3 rounded-xl">
                                <span class="font-bold text-emerald-700">Keluar Hidup:</span> Pasien yang pulang dalam keadaan hidup (Sehat, Sembuh, Membaik, APS, Pulang Paksa, dll).
                            </div>
                            <div class="bg-red-50/50 p-3 rounded-xl">
                                <span class="font-bold text-red-700">Keluar Mati ≥48 Jam:</span> Pasien meninggal setelah dirawat ≥ 2 hari (≥48 jam). Dipecah berdasarkan jenis kelamin.
                            </div>
                            <div class="bg-indigo-50/50 p-3 rounded-xl">
                                <span class="font-bold text-indigo-700">Hari Perawatan:</span> Total hari rawat pasien yang keluar pada bulan pelaporan, dikelompokkan berdasarkan kelas kamar (VVIP, VIP, Kelas 1, 2, 3, Khusus/Utama).
                            </div>
                            <div class="bg-purple-50/50 p-3 rounded-xl">
                                <span class="font-bold text-purple-700">Alokasi TT:</span> Jumlah total tempat tidur aktif yang tersedia pada awal bulan pelaporan.
                            </div>
                        </div>
                    </div>

                    <!-- Jenis Pelayanan -->
                    <div>
                        <h4 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-3">Jenis Pelayanan</h4>
                        <div class="flex gap-4">
                            <div class="flex-1 bg-blue-50 p-3 rounded-xl text-center">
                                <span class="text-xs font-black text-blue-700 uppercase">Umum</span>
                                <p class="text-[10px] text-blue-500 mt-1">Pasien dengan penjamin selain BPJS (kd_pj tidak mengandung "BPJ")</p>
                            </div>
                            <div class="flex-1 bg-green-50 p-3 rounded-xl text-center">
                                <span class="text-xs font-black text-green-700 uppercase">BPJS</span>
                                <p class="text-[10px] text-green-500 mt-1">Pasien dengan penjamin BPJS (kd_pj mengandung "BPJ")</p>
                            </div>
                        </div>
                    </div>

                    <!-- Pemetaan Basis Data SIMRS Khanza -->
                    <div>
                        <h4 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-3">Pemetaan Basis Data (SIMRS Khanza)</h4>
                        <div class="border border-gray-100 rounded-xl overflow-hidden">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 font-bold uppercase">
                                    <tr>
                                        <th class="p-3">Indikator / Kolom</th>
                                        <th class="p-3">Tabel SQL</th>
                                        <th class="p-3">Kolom SQL</th>
                                        <th class="p-3">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <tr>
                                        <td class="p-3 font-semibold text-gray-800">Pasien Rawat Inap</td>
                                        <td class="p-3 text-gray-600 font-mono">kamar_inap</td>
                                        <td class="p-3 text-gray-600 font-mono">no_rawat, tgl_masuk, tgl_keluar</td>
                                        <td class="p-3 text-gray-500">FK ke <code class="bg-gray-100 px-1 py-0.5 rounded">reg_periksa.no_rawat</code></td>
                                    </tr>
                                    <tr>
                                        <td class="p-3 font-semibold text-gray-800">Kelas Kamar</td>
                                        <td class="p-3 text-gray-600 font-mono">kamar</td>
                                        <td class="p-3 text-gray-600 font-mono">kelas, kd_bangsal</td>
                                        <td class="p-3 text-gray-500">FK via <code class="bg-gray-100 px-1 py-0.5 rounded">kamar_inap.kd_kamar</code></td>
                                    </tr>
                                    <tr>
                                        <td class="p-3 font-semibold text-gray-800">Bangsal</td>
                                        <td class="p-3 text-gray-600 font-mono">bangsal</td>
                                        <td class="p-3 text-gray-600 font-mono">kd_bangsal, nm_bangsal</td>
                                        <td class="p-3 text-gray-500">FK via <code class="bg-gray-100 px-1 py-0.5 rounded">kamar.kd_bangsal</code></td>
                                    </tr>
                                    <tr>
                                        <td class="p-3 font-semibold text-gray-800">Status Pulang</td>
                                        <td class="p-3 text-gray-600 font-mono">kamar_inap</td>
                                        <td class="p-3 text-gray-600 font-mono">stts_pulang</td>
                                        <td class="p-3 text-gray-500">Enum: Sehat, Meninggal, Pindah Kamar, dll</td>
                                    </tr>
                                    <tr>
                                        <td class="p-3 font-semibold text-gray-800">Lama Rawat</td>
                                        <td class="p-3 text-gray-600 font-mono">kamar_inap</td>
                                        <td class="p-3 text-gray-600 font-mono">lama</td>
                                        <td class="p-3 text-gray-500">Jumlah hari rawat per kamar</td>
                                    </tr>
                                    <tr>
                                        <td class="p-3 font-semibold text-gray-800">Jenis Kelamin</td>
                                        <td class="p-3 text-gray-600 font-mono">pasien</td>
                                        <td class="p-3 text-gray-600 font-mono">jk</td>
                                        <td class="p-3 text-gray-500">L (Laki-laki) / P (Perempuan)</td>
                                    </tr>
                                    <tr>
                                        <td class="p-3 font-semibold text-gray-800">Jenis Pelayanan</td>
                                        <td class="p-3 text-gray-600 font-mono">penjab</td>
                                        <td class="p-3 text-gray-600 font-mono">kd_pj, png_jawab</td>
                                        <td class="p-3 text-gray-500">Identifikasi BPJS dari kd_pj mengandung "BPJ"</td>
                                    </tr>
                                    <tr>
                                        <td class="p-3 font-semibold text-gray-800">Asal Masuk</td>
                                        <td class="p-3 text-gray-600 font-mono">poliklinik</td>
                                        <td class="p-3 text-gray-600 font-mono">nm_poli</td>
                                        <td class="p-3 text-gray-500">Kategorisasi IGD/Obgyn/Anak/Bedah dari nama poli</td>
                                    </tr>
                                    <tr>
                                        <td class="p-3 font-semibold text-gray-800">Alokasi TT</td>
                                        <td class="p-3 text-gray-600 font-mono">kamar</td>
                                        <td class="p-3 text-gray-600 font-mono">kd_kamar, statusdata</td>
                                        <td class="p-3 text-gray-500">COUNT kamar dengan statusdata='1' dan bangsal aktif</td>
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
