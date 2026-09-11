@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-2xl font-bold text-gray-800 uppercase tracking-tighter italic">Kelengkapan ERM</h2>
                    </div>
                    <p class="text-gray-500 text-sm mt-1">Ekstraksi dan audit tingkat kelengkapan Elektronik Rekam Medis (ERM).</p>
                </div>
            </div>
            <div class="flex items-center gap-2 text-sm font-medium text-amber-600 bg-amber-50 px-4 py-2 rounded-lg border border-amber-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
                <span>Halaman Baru (Tahap Penyiapan)</span>
            </div>
        </div>

        <!-- Empty State / Placeholder Modul -->
        <div class="bg-white p-16 md:p-24 rounded-3xl shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center">
            <div class="p-6 bg-primary/5 rounded-3xl mb-6 ring-8 ring-primary/[0.02]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <h3 class="text-2xl font-black text-gray-800 mb-2 uppercase tracking-tighter italic">Menu Kelengkapan ERM</h3>
            <p class="text-gray-400 text-sm max-w-md font-medium leading-relaxed mb-6">
                Halaman ini disiapkan untuk modul audit <strong>Kelengkapan ERM</strong> (Elektronik Rekam Medis). Fitur kriteria verifikasi berkas dan ekspor laporan akan segera dikonfigurasikan.
            </p>
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-50 border border-gray-100 text-xs font-semibold text-gray-500">
                <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                Unit Kerja: Rekam Medis
            </div>
        </div>
    </div>
@endsection
