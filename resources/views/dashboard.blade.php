@extends('layouts.app')

@section('content')
    <div class="space-y-8">
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold text-gray-800">Selamat Datang, {{ Auth::user()->username }}!</h2>
                <p class="text-gray-500 mt-2">Selamat datang di sistem penarikan data TARIKSIS.</p>
            </div>
            <div class="hidden md:block">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-primary/20" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white p-8 rounded-2xl shadow-sm border-l-8 border-primary flex items-center space-x-6">
                <div class="p-4 bg-primary/10 rounded-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-primary" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-500 text-sm font-bold uppercase tracking-wider">Penarikan Bulan Ini</p>
                    <p class="text-4xl font-black text-gray-900">{{ $currentMonthCount }}</p>
                </div>
            </div>

            <div class="bg-white p-8 rounded-2xl shadow-sm border-l-8 border-gray-400 flex items-center space-x-6">
                <div class="p-4 bg-gray-100 rounded-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-500" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-500 text-sm font-bold uppercase tracking-wider">Total Transaksi</p>
                    <p class="text-4xl font-black text-gray-900">{{ $totalCount }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div
                class="bg-primary p-8 rounded-2xl shadow-lg shadow-primary/20 text-white flex flex-col justify-between items-start space-y-4 h-full">
                <div>
                    <h2 class="text-xl font-bold">Butuh data baru?</h2>
                    <p class="text-green-100 text-sm mt-2 leading-relaxed">Silakan masuk ke menu Penarikan Data untuk
                        memulai proses ekstraksi data pasien.</p>
                </div>
                <button @click="$dispatch('highlight-menu')"
                    class="w-full text-center bg-white text-primary font-bold px-6 py-3 rounded-xl hover:bg-gray-100 transition shadow-lg uppercase tracking-widest text-xs">
                    Tarik Data Sekarang
                </button>
            </div>

            <div
                class="bg-blue-600 p-8 rounded-2xl shadow-lg shadow-blue-600/20 text-white flex flex-col justify-between items-start space-y-4 h-full">
                <div>
                    <h2 class="text-xl font-bold">Butuh Bantuan?</h2>
                    <p class="text-blue-100 text-sm mt-2 leading-relaxed">Kirim laporan untuk meminta tolong kepada tim IT
                        secara cepat tanpa login!</p>
                </div>
                <a href="http://192.168.100.177/mastolongmas/public/login" target="_blank"
                    class="w-full text-center bg-white text-blue-600 font-bold px-6 py-3 rounded-xl hover:bg-gray-100 transition shadow-lg uppercase tracking-widest text-xs flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Minta Tolong Sekarang
                </a>
            </div>
        </div>
    </div>
@endsection