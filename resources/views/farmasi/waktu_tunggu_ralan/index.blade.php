@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 uppercase tracking-tighter italic">Waktu Tunggu Farmasi - Rawat Jalan</h2>
                <p class="text-gray-500 text-sm mt-1">Monitoring durasi penyelesaian pelayanan resep obat pasien rawat jalan.</p>
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
            <form action="{{ route('farmasi.waktu_tunggu_ralan.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
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
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Jenis Bayar</label>
                    <select name="kd_pj" class="w-full px-4 py-3 rounded-xl bg-gray-50 border-0 focus:bg-white focus:ring-4 focus:ring-primary/10 transition outline-none text-gray-800 shadow-inner text-sm">
                        <option value="">-- Semua --</option>
                        @foreach($penjabs as $pj)
                            <option value="{{ $pj->kd_pj }}" {{ $kd_pj == $pj->kd_pj ? 'selected' : '' }}>{{ $pj->png_jawab }}</option>
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
            <!-- Summary Card -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-5">
                    <div class="p-4 bg-primary/10 rounded-2xl">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Rata-rata Waktu Tunggu</p>
                        @php
                            $h = floor($avgSeconds / 3600);
                            $m = floor(($avgSeconds % 3600) / 60);
                            $s = $avgSeconds % 60;
                        @endphp
                        <h3 class="text-2xl font-black text-gray-800">
                            {{ $h > 0 ? $h.'j ' : '' }}{{ $m }}m {{ $s }}d
                        </h3>
                    </div>
                </div>
            </div>

            <!-- Export Section -->
            <div class="flex items-center gap-4">
                <a href="javascript:void(0)" 
                    onclick="handleDownload('{{ route('farmasi.waktu_tunggu_ralan.export.excel', ['tgl_mulai' => $tgl_mulai, 'tgl_selesai' => $tgl_selesai, 'kd_pj' => $kd_pj]) }}', 'farmasi-ralan-{{ $tgl_mulai }}-{{ $tgl_selesai }}.xlsx')"
                    class="bg-white border border-gray-200 text-gray-700 font-bold px-6 py-3 rounded-xl hover:bg-gray-50 transition shadow-sm flex items-center gap-2 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    Ekspor Excel
                </a>
                <a href="javascript:void(0)" 
                    onclick="handleDownload('{{ route('farmasi.waktu_tunggu_ralan.export.pdf', ['tgl_mulai' => $tgl_mulai, 'tgl_selesai' => $tgl_selesai, 'kd_pj' => $kd_pj]) }}', 'farmasi-ralan-{{ $tgl_mulai }}-{{ $tgl_selesai }}.pdf')"
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
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Nama Pasien</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">No. RM</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Jenis Bayar</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-center">Jam Validasi</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-center">Jam Penyerahan</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-center">Waktu Yang Dibutuhkan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($data as $index => $item)
                                <tr class="hover:bg-gray-50/30 transition-colors">
                                    <td class="px-6 py-5 text-sm font-bold text-gray-300">
                                        {{ str_pad($data->firstItem() + $index, 2, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="text-sm font-black text-gray-800 uppercase">{{ $item->nm_pasien }}</div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-[10px] font-black bg-gray-100 text-gray-500 tracking-tighter">
                                            {{ $item->no_rkm_medis }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="inline-flex items-center px-2 py-1 rounded text-[10px] font-black bg-blue-50 text-blue-600 tracking-tighter uppercase">
                                            {{ $item->png_jawab }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <div class="text-sm font-black text-gray-600">{{ $item->jam_validasi }}</div>
                                        <div class="text-[10px] text-gray-400 font-bold">{{ \Carbon\Carbon::parse($item->tgl_perawatan)->format('d/m/Y') }}</div>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <div class="text-sm font-black text-gray-600">{{ $item->jam_penyerahan }}</div>
                                        <div class="text-[10px] text-gray-400 font-bold">{{ \Carbon\Carbon::parse($item->tgl_penyerahan)->format('d/m/Y') }}</div>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        @php
                                            $parts = explode(':', $item->total_waktu);
                                            $hours = (int)($parts[0] ?? 0);
                                            $minutes = (int)($parts[1] ?? 0);
                                            $seconds = (int)($parts[2] ?? 0);
                                            $totalMinutes = ($hours * 60) + $minutes;

                                            $colorClass = 'bg-gray-50 text-gray-700';
                                            if ($hours > 0 || $totalMinutes >= 60) {
                                                $colorClass = 'bg-red-50 text-red-700';
                                            } elseif ($totalMinutes >= 30) {
                                                $colorClass = 'bg-yellow-50 text-yellow-700';
                                            } else {
                                                $colorClass = 'bg-green-50 text-green-700';
                                            }
                                        @endphp
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black {{ $colorClass }}">
                                            {{ $hours }}j {{ $minutes }}m {{ $seconds }}d
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-20 text-center text-gray-400 italic">Data tidak ditemukan.</td>
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
                    <p class="text-gray-500 max-w-md mx-auto">Gunakan filter di atas untuk menarik data waktu tunggu pelayanan farmasi rawat jalan.</p>
                </div>
            </div>
        @endif
    </div>
@endsection
