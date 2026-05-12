@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 uppercase tracking-tighter italic">Waktu Tunggu Rawat Jalan BPJS</h2>
                <p class="text-gray-500 text-sm mt-1">Monitoring durasi penyelesaian pelayanan farmasi untuk pasien BPJS rawat jalan.</p>
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
            <form action="{{ route('farmasi.waktu_tunggu_bpjs.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
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
                        Tampilkan Data
                    </button>
                </div>
            </form>
        </div>

        @if($data)
            <!-- Export Section Placeholder -->
            <div class="flex items-center gap-4">
                <button class="opacity-50 cursor-not-allowed bg-white border border-gray-200 text-gray-400 font-bold px-6 py-3 rounded-xl shadow-sm flex items-center gap-2 text-sm">
                    Ekspor Excel
                </button>
                <button class="opacity-50 cursor-not-allowed bg-white border border-gray-200 text-gray-400 font-bold px-6 py-3 rounded-xl shadow-sm flex items-center gap-2 text-sm">
                    Ekspor PDF
                </button>
            </div>
            
            <!-- Table Section Placeholder -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-20 text-center">
                    <p class="text-gray-400 italic">Halaman sedang dalam pengembangan...</p>
                </div>
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
                    <p class="text-gray-500 max-w-md mx-auto">Gunakan filter tanggal di atas untuk menarik data waktu tunggu farmasi pasien BPJS rawat jalan.</p>
                </div>
            </div>
        @endif
    </div>
@endsection
