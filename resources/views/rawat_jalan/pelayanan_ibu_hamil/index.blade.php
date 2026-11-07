@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-2xl font-bold text-gray-800 uppercase tracking-tighter italic">Pelayanan Ibu Hamil (ANC)</h2>
                        <button type="button" onclick="openInfoModal()" class="text-primary hover:text-green-800 transition duration-150 focus:outline-none" title="Informasi Formula & Sumber Data">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </button>
                    </div>
                    <p class="text-gray-500 text-sm mt-1">Ekstraksi data operasional pelayanan ibu hamil, HPHT, usia kehamilan, TFU, diagnosa GPA, dan LiLA.</p>
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
            <form action="{{ route('rawat_jalan.pelayanan_ibu_hamil.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
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
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Poliklinik (Opsional)</label>
                    <select name="kd_poli" class="w-full px-5 py-4 rounded-2xl bg-gray-50 border-0 focus:bg-white focus:ring-4 focus:ring-primary/10 transition outline-none text-gray-800 shadow-inner">
                        <option value="">-- Semua Poliklinik Kebidanan --</option>
                        @foreach($poliklinikList as $poli)
                            <option value="{{ $poli->kd_poli }}" {{ request('kd_poli') == $poli->kd_poli ? 'selected' : '' }}>
                                {{ $poli->nm_poli }}
                            </option>
                        @endforeach
                    </select>
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
                <a href="#" onclick="handleDownload('{{ route('rawat_jalan.pelayanan_ibu_hamil.export.excel', request()->all()) }}', 'pelayanan_ibu_hamil_{{ date('Ymd_His') }}.xlsx'); return false;"
                    class="bg-white border border-gray-200 text-gray-700 font-bold px-6 py-3 rounded-xl hover:bg-gray-50 transition shadow-sm flex items-center gap-2 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    Ekspor Excel
                </a>
                <a href="#" onclick="handleDownload('{{ route('rawat_jalan.pelayanan_ibu_hamil.export.pdf', request()->all()) }}', 'pelayanan_ibu_hamil_{{ date('Ymd_His') }}.pdf'); return false;"
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
                    <table class="w-full text-left whitespace-nowrap text-xs">
                        <thead>
                            <tr class="bg-gray-50/70 border-b border-gray-100">
                                <th class="px-4 py-4 font-black text-gray-400 uppercase tracking-wider text-center">NO</th>
                                <th class="px-4 py-4 font-black text-gray-400 uppercase tracking-wider">NAMA PASIEN (Ny.)</th>
                                <th class="px-4 py-4 font-black text-gray-400 uppercase tracking-wider">NIK IBU HAMIL</th>
                                <th class="px-4 py-4 font-black text-gray-400 uppercase tracking-wider">NAMA SUAMI (Tn.)</th>
                                <th class="px-4 py-4 font-black text-gray-400 uppercase tracking-wider">ALAMAT KTP & DOMISILI</th>
                                <th class="px-4 py-4 font-black text-gray-400 uppercase tracking-wider text-center">UMUR IBU</th>
                                <th class="px-4 py-4 font-black text-gray-400 uppercase tracking-wider text-center">HPHT</th>
                                <th class="px-4 py-4 font-black text-gray-400 uppercase tracking-wider text-center">USIA KEHAMILAN</th>
                                <th class="px-4 py-4 font-black text-amber-900 bg-amber-100 uppercase tracking-wider text-center">Kecil Masa Kehamilan (cm)</th>
                                <th class="px-4 py-4 font-black text-amber-900 bg-amber-100 uppercase tracking-wider text-center">Besar Masa Kehamilan</th>
                                <th class="px-4 py-4 font-black text-gray-400 uppercase tracking-wider">DIAGNOSA (G..P..A..)</th>
                                <th class="px-4 py-4 font-black text-gray-400 uppercase tracking-wider text-center">Lingkar Lengan Atas (LiLA)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @php
                                $offset = ($data->currentPage() - 1) * $data->perPage();
                            @endphp
                            @forelse($data as $index => $row)
                                @php
                                    // Nama Pasien
                                    $namaPasien = trim($row->nm_pasien ?? '');
                                    if ($namaPasien !== '' && !preg_match('/^(ny\.|ny\s)/i', $namaPasien)) {
                                        $namaPasien = 'NY. ' . strtoupper($namaPasien);
                                    } else {
                                        $namaPasien = strtoupper($namaPasien);
                                    }

                                    // Nama Suami
                                    $namaSuami = '-';
                                    if (!empty($row->keluarga) && strtoupper(trim($row->keluarga)) === 'SUAMI' && !empty($row->namakeluarga)) {
                                        $namaSuami = trim($row->namakeluarga);
                                    } else if (!empty($row->hubunganpj) && strtoupper(trim($row->hubunganpj)) === 'SUAMI' && !empty($row->p_jawab)) {
                                        $namaSuami = trim($row->p_jawab);
                                    }

                                    if ($namaSuami !== '-') {
                                        $namaSuami = strtoupper($namaSuami);
                                        if (!str_starts_with($namaSuami, 'TN.') && !str_starts_with($namaSuami, 'TN ')) {
                                            $namaSuami = 'TN. ' . $namaSuami;
                                        }
                                    }

                                    // Alamat Lengkap
                                    $alamatParts = array_filter([
                                        $row->alamat,
                                        $row->nm_kel ? 'Kel. ' . $row->nm_kel : null,
                                        $row->nm_kec ? 'Kec. ' . $row->nm_kec : null,
                                        $row->nm_kab ? $row->nm_kab : null,
                                    ]);
                                    $alamatLengkap = !empty($alamatParts) ? implode(', ', $alamatParts) : ($row->alamat ?: '-');

                                    // Umur
                                    $umur = '-';
                                    if (!empty($row->umurdaftar)) {
                                        $umur = $row->umurdaftar . ' TH';
                                    } elseif (!empty($row->tgl_lahir)) {
                                        $umur = \Carbon\Carbon::parse($row->tgl_lahir)->age . ' TH';
                                    }

                                    // HPHT
                                    $hpht = '-';
                                    if (!empty($row->hpht) && $row->hpht !== '0000-00-00') {
                                        $hpht = \Carbon\Carbon::parse($row->hpht)->format('m/d/Y');
                                    }

                                    // Usia Kehamilan
                                    $uk = '-';
                                    if (!empty($row->usia_kehamilan)) {
                                        $uk = trim($row->usia_kehamilan);
                                        if (!preg_match('/mgg|minggu/i', $uk)) {
                                            $uk .= ' MGG';
                                        }
                                    } elseif (!empty($row->hpht) && $row->hpht !== '0000-00-00') {
                                        $tglDaftar = \Carbon\Carbon::parse($row->tgl_registrasi);
                                        $tglHpht = \Carbon\Carbon::parse($row->hpht);
                                        $diffWeeks = floor($tglHpht->diffInDays($tglDaftar) / 7);
                                        if ($diffWeeks > 0 && $diffWeeks <= 45) {
                                            $uk = $diffWeeks . ' MGG';
                                        }
                                    }

                                    // TFU (Kecil / Besar Masa Kehamilan)
                                    $tfuRaw = $row->tfu_kebidanan ?: ($row->tfu_dokter ?: null);
                                    $kmk = '-';
                                    $bmk = '-';
                                    if ($tfuRaw) {
                                        $cleanTfu = preg_replace('/[^0-9.]/', '', $tfuRaw);
                                        if ($cleanTfu !== '') {
                                            $tfuValue = $cleanTfu . ' CM';
                                            preg_match('/(\d+)/', $uk, $matchesUk);
                                            $numericUk = !empty($matchesUk[1]) ? floatval($matchesUk[1]) : null;
                                            $numericTfu = floatval($cleanTfu);

                                            if ($numericUk && $numericTfu < ($numericUk - 3)) {
                                                $kmk = $tfuValue;
                                            } else {
                                                $bmk = $tfuValue;
                                            }
                                        } else {
                                            $bmk = $tfuRaw;
                                        }
                                    }

                                    // Diagnosa GPA
                                    $diagnosa = '-';
                                    $hasGpa = ($row->g !== null && $row->g !== '') || ($row->p !== null && $row->p !== '') || ($row->a !== null && $row->a !== '');
                                    if ($hasGpa) {
                                        $diagnosa = 'G' . ($row->g ?? '0') . 'P' . ($row->p ?? '0') . 'A' . ($row->a ?? '0');
                                        if ($uk !== '-') {
                                            $diagnosa .= ' UK ' . $uk;
                                        }
                                    } elseif (!empty($row->diagnosa_dokter)) {
                                        $diagnosa = $row->diagnosa_dokter;
                                    }

                                    // LiLA
                                    $lila = '-';
                                    if (!empty($row->lila)) {
                                        $cleanLila = preg_replace('/[^0-9.]/', '', $row->lila);
                                        $lila = $cleanLila ? ($cleanLila . ' CM') : $row->lila;
                                    }
                                @endphp
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-4 py-4 text-center font-bold text-gray-500">{{ $offset + $index + 1 }}.</td>
                                    <td class="px-4 py-4 font-black text-gray-800">{{ $namaPasien }}</td>
                                    <td class="px-4 py-4 font-mono text-gray-600">{{ $row->no_ktp ?: '-' }}</td>
                                    <td class="px-4 py-4 font-bold text-gray-700">{{ $namaSuami }}</td>
                                    <td class="px-4 py-4 text-gray-600 max-w-xs truncate" title="{{ $alamatLengkap }}">{{ $alamatLengkap }}</td>
                                    <td class="px-4 py-4 text-center font-bold text-gray-700">{{ $umur }}</td>
                                    <td class="px-4 py-4 text-center font-mono text-gray-600">{{ $hpht }}</td>
                                    <td class="px-4 py-4 text-center font-bold text-primary">{{ $uk }}</td>
                                    <td class="px-4 py-4 text-center font-bold text-amber-900 bg-amber-50/50">{{ $kmk }}</td>
                                    <td class="px-4 py-4 text-center font-bold text-amber-900 bg-amber-50/50">{{ $bmk }}</td>
                                    <td class="px-4 py-4 font-black text-gray-800">{{ $diagnosa }}</td>
                                    <td class="px-4 py-4 text-center font-bold text-gray-700">{{ $lila }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="px-6 py-20 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="p-6 bg-gray-50 rounded-full mb-4">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                            <p class="text-gray-400 font-bold uppercase tracking-widest text-xs">Data pemeriksaan ibu hamil tidak ditemukan pada periode ini</p>
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
            <!-- Empty State Before Filter -->
            <div class="bg-white p-20 rounded-3xl shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center">
                <div class="p-6 bg-primary/5 rounded-full mb-8">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-primary/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-gray-800 mb-3 uppercase tracking-tighter italic">Pilih Periode Tanggal</h3>
                <p class="text-gray-400 text-sm max-w-sm font-medium">Silakan tentukan rentang tanggal dan poliklinik untuk menarik data otomatis laporan pelayanan ibu hamil (ANC).</p>
            </div>
        @endif
    </div>

    <!-- Modal Informasi & Dokumentasi Sumber Data Sesuai SOP -->
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
                        <h3 class="text-lg font-black uppercase tracking-wider">Informasi Modul Pelayanan Ibu Hamil</h3>
                    </div>
                    <button onclick="closeInfoModal()" class="text-white hover:text-gray-200 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Konten Modal -->
                <div class="p-6 space-y-6 max-h-[75vh] overflow-y-auto">
                    <!-- 1. Deskripsi Menu -->
                    <div>
                        <h4 class="text-sm font-black text-gray-800 uppercase tracking-wider mb-2 border-b pb-1">1. Deskripsi Fitur</h4>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            Modul ini dirancang khusus untuk memfasilitasi penarikan data register pelayanan pemeriksaan antenatal care (ANC) / ibu hamil rawat jalan secara otomatis tanpa manipulasi data. Modul ini menyajikan ringkasan identitas ibu, suami, alamat domisili lengkap, umur, HPHT, usia kehamilan, Tinggi Fundus Uteri (TFU), diagnosa kehamilan Gravida-Para-Abortus (G..P..A..), dan Lingkar Lengan Atas (LiLA).
                        </p>
                    </div>

                    <!-- 2. Formula Kalkulasi -->
                    <div>
                        <h4 class="text-sm font-black text-gray-800 uppercase tracking-wider mb-2 border-b pb-1">2. Logika & Formula Kalkulasi</h4>
                        <div class="space-y-3 text-xs text-gray-700 bg-gray-50 p-4 rounded-2xl border border-gray-100">
                            <div>
                                <span class="font-bold text-gray-900 block">Diagnosa Kehamilan (G..P..A.. UK .. MGG):</span>
                                Formula otomatis: <code class="bg-white px-2 py-1 rounded text-primary font-mono border">CONCAT('G', g, 'P', p, 'A', a, ' UK ', usia_kehamilan, ' MGG')</code>
                            </div>
                            <div>
                                <span class="font-bold text-gray-900 block">Kecil / Besar Masa Kehamilan (TFU):</span>
                                Berdasarkan evaluasi Tinggi Fundus Uteri terhadap Usia Kehamilan. Jika TFU di bawah ambang normal (&lt; UK - 3 cm) dikelompokkan pada kolom <span class="font-semibold text-amber-700">Kecil Masa Kehamilan</span>, sedangkan jika normal/sesuai/besar dikelompokkan pada <span class="font-semibold text-amber-700">Besar Masa Kehamilan</span>.
                            </div>
                            <div>
                                <span class="font-bold text-gray-900 block">Estimasi Usia Kehamilan (jika belum tercatat di form):</span>
                                Formula: <code class="bg-white px-2 py-1 rounded text-primary font-mono border">FLOOR(DATEDIFF(tgl_registrasi, hpht) / 7) MGG</code>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Pemetaan Basis Data SIMRS Khanza -->
                    <div>
                        <h4 class="text-sm font-black text-gray-800 uppercase tracking-wider mb-2 border-b pb-1">3. Pemetaan Basis Data (SIMRS Khanza)</h4>
                        <div class="overflow-x-auto rounded-xl border border-gray-200">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="bg-gray-50 border-b border-gray-200">
                                        <th class="p-2.5 font-bold text-gray-600 uppercase">Kolom Tampilan</th>
                                        <th class="p-2.5 font-bold text-gray-600 uppercase">Nama Tabel SIMRS</th>
                                        <th class="p-2.5 font-bold text-gray-600 uppercase">Kolom SQL</th>
                                        <th class="p-2.5 font-bold text-gray-600 uppercase">Keterangan Relasi / Filter</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-gray-700">
                                    <tr>
                                        <td class="p-2 font-semibold">Nama Pasien</td>
                                        <td class="p-2 font-mono text-primary">pasien</td>
                                        <td class="p-2 font-mono text-primary">nm_pasien</td>
                                        <td class="p-2 text-gray-500">Join via no_rkm_medis</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold">NIK Ibu Hamil</td>
                                        <td class="p-2 font-mono text-primary">pasien</td>
                                        <td class="p-2 font-mono text-primary">no_ktp</td>
                                        <td class="p-2 text-gray-500">Nomor KTP Ibu Hamil</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold">Nama Suami</td>
                                        <td class="p-2 font-mono text-primary">pasien / reg_periksa</td>
                                        <td class="p-2 font-mono text-primary">namakeluarga / p_jawab</td>
                                        <td class="p-2 text-gray-500">Keluarga status SUAMI / PJ Pasien</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold">Alamat Lengkap</td>
                                        <td class="p-2 font-mono text-primary">pasien, kelurahan, kecamatan</td>
                                        <td class="p-2 font-mono text-primary">alamat, nm_kel, nm_kec</td>
                                        <td class="p-2 text-gray-500">Join kd_kel & kd_kec</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold">Umur Ibu</td>
                                        <td class="p-2 font-mono text-primary">reg_periksa / pasien</td>
                                        <td class="p-2 font-mono text-primary">umurdaftar / tgl_lahir</td>
                                        <td class="p-2 text-gray-500">Umur saat registrasi kunjungan</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold">HPHT</td>
                                        <td class="p-2 font-mono text-primary">penilaian_awal_keperawatan_kebidanan</td>
                                        <td class="p-2 font-mono text-primary">hpht</td>
                                        <td class="p-2 text-gray-500">Hari Pertama Haid Terakhir</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold">Usia Kehamilan</td>
                                        <td class="p-2 font-mono text-primary">penilaian_awal_keperawatan_kebidanan</td>
                                        <td class="p-2 font-mono text-primary">usia_kehamilan</td>
                                        <td class="p-2 text-gray-500">Usia kehamilan dalam satuan minggu</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold">TFU (KMK & BMK)</td>
                                        <td class="p-2 font-mono text-primary">penilaian_awal_keperawatan_kebidanan</td>
                                        <td class="p-2 font-mono text-primary">tfu</td>
                                        <td class="p-2 text-gray-500">Tinggi Fundus Uteri dalam satuan cm</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold">Diagnosa (G..P..A..)</td>
                                        <td class="p-2 font-mono text-primary">penilaian_awal_keperawatan_kebidanan</td>
                                        <td class="p-2 font-mono text-primary">g, p, a, usia_kehamilan</td>
                                        <td class="p-2 text-gray-500">Kombinasi Gravida, Para, Abortus & UK</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 font-semibold">LiLA</td>
                                        <td class="p-2 font-mono text-primary">penilaian_awal_keperawatan_kebidanan</td>
                                        <td class="p-2 font-mono text-primary">lila</td>
                                        <td class="p-2 text-gray-500">Lingkar Lengan Atas dalam satuan cm</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Footer Modal -->
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

