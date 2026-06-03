<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'TARIKSIS') }}</title>
    <!-- Tailwind CSS Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#007C3C',
                        secondary: '#f3f4f6',
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        [x-cloak] {
            display: none !important;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.3;
            }
        }

        .animate-blink {
            animation: blink 0.5s ease-in-out infinite;
        }
    </style>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-gray-50 min-h-screen font-sans antialiased text-gray-900" x-data="{ sidebarOpen: false }">

    <div class="flex min-h-screen overflow-hidden">
        @auth
            <!-- Mobile Sidebar Backdrop -->
            <div x-show="sidebarOpen" x-cloak x-transition:enter="transition-opacity ease-linear duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" @click="sidebarOpen = false"
                class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 md:hidden"></div>

            <!-- Sidebar -->
            <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                class="fixed inset-y-0 left-0 z-50 w-64 bg-primary text-white transition-transform duration-300 ease-in-out transform md:relative md:translate-x-0 h-screen overflow-y-auto flex-shrink-0">
                <div class="flex flex-col h-full">
                    <!-- Sidebar Header -->
                    <div class="flex items-center justify-center h-20 border-b border-white/10 flex-shrink-0">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                            <!-- Logo Icon -->
                            <div
                                class="bg-white text-primary p-2 rounded-lg shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                                </svg>
                            </div>
                            <!-- Logo Text -->
                            <div class="flex flex-col">
                                <span
                                    class="text-2xl font-black tracking-tighter italic text-white group-hover:text-white/90 transition-colors leading-none">
                                    TARIKSIS
                                </span>
                                <span class="text-[7px] font-bold text-white/60 tracking-tight uppercase mt-1 leading-none">
                                    TARIK data Sistem Informasi rS
                                </span>
                            </div>
                        </a>
                    </div>

                    <!-- Navigation Links -->
                    <nav class="flex-grow mt-6 px-4 space-y-2 uppercase tracking-tight">
                        <a href="{{ route('dashboard') }}"
                            class="flex items-center px-4 py-3 text-sm font-semibold rounded-xl transition {{ request()->routeIs('dashboard') ? 'bg-white text-primary' : 'hover:bg-white/10' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                </path>
                            </svg>
                            Dashboard
                        </a>

                        <div class="px-4 mt-6 mb-2" x-data="{ highlighted: false }"
                            @highlight-menu.window="highlighted = true; setTimeout(() => highlighted = false, 3000)">
                            <p :class="highlighted ? 'text-yellow-400 animate-blink' : 'text-white/40'"
                                class="text-[10px] font-bold uppercase tracking-widest leading-relaxed transition-all duration-300">
                                Pilih menu penarikan data dibawah ini sesuai unit anda
                            </p>
                        </div>

                        <!-- Farmasi Menu -->
                        <div x-data="{ open: {{ request()->routeIs('extraction.*') || request()->routeIs('extraction_ralan.*') || request()->routeIs('pemberian_obat.*') || request()->routeIs('penerimaan_obat_farmasi.*') || request()->routeIs('farmasi.waktu_tunggu_ralan.*') || request()->routeIs('farmasi.harga_barang.*') || request()->routeIs('farmasi.sirkulasi.*') || request()->routeIs('farmasi.waktu_tunggu_bpjs.*') || request()->routeIs('farmasi.opname.*') || request()->routeIs('farmasi.template_bu_sugati.*') ? 'true' : 'false' }} }"
                            class="space-y-1">
                            <button @click="open = !open"
                                class="flex w-full items-center justify-between px-4 py-3 text-sm font-semibold rounded-xl hover:bg-white/10 transition text-left">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                                        </path>
                                    </svg>
                                    Farmasi
                                </div>
                                <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform duration-200"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7">
                                    </path>
                                </svg>
                            </button>
                            <div x-show="open" x-collapse
                                class="pl-11 pr-4 py-2 space-y-1 text-xs font-medium text-white/80 bg-black/10 rounded-xl mx-2">
                                <a href="{{ route('extraction.index') }}"
                                    class="block py-2 px-3 rounded-lg hover:bg-white/10 transition {{ request()->routeIs('extraction.*') ? 'text-white font-bold bg-white/20' : '' }}">
                                    Penarikan Data Rawat Inap
                                </a>
                                <a href="{{ route('extraction_ralan.index') }}"
                                    class="block py-2 px-3 rounded-lg hover:bg-white/10 transition {{ request()->routeIs('extraction_ralan.*') ? 'text-white font-bold bg-white/20' : '' }}">
                                    Penarikan Data Rawat Jalan
                                </a>
                                <a href="{{ route('penerimaan_obat_farmasi.index') }}"
                                    class="block py-2 px-3 rounded-lg hover:bg-white/10 transition {{ request()->routeIs('penerimaan_obat_farmasi.*') ? 'text-white font-bold bg-white/20' : '' }}">
                                    Penerimaan Obat dan BHP Farmasi
                                </a>
                                <a href="{{ route('pemberian_obat.index') }}"
                                    class="block py-2 px-3 rounded-lg hover:bg-white/10 transition {{ request()->routeIs('pemberian_obat.*') ? 'text-white font-bold bg-white/20' : '' }}">
                                    Pemberian Obat dan BHP
                                </a>
                                <a href="{{ route('farmasi.waktu_tunggu_ralan.index') }}"
                                    class="block py-2 px-3 rounded-lg hover:bg-white/10 transition {{ request()->routeIs('farmasi.waktu_tunggu_ralan.*') ? 'text-white font-bold bg-white/20' : '' }}">
                                    Waktu Tunggu Rawat Jalan
                                </a>
                                <a href="{{ route('farmasi.harga_barang.index') }}"
                                    class="block py-2 px-3 rounded-lg hover:bg-white/10 transition {{ request()->routeIs('farmasi.harga_barang.*') ? 'text-white font-bold bg-white/20' : '' }}">
                                    Tarik Harga Barang
                                </a>
                                <a href="{{ route('farmasi.waktu_tunggu_bpjs.index') }}"
                                    class="block py-2 px-3 rounded-lg hover:bg-white/10 transition {{ request()->routeIs('farmasi.waktu_tunggu_bpjs.*') ? 'text-white font-bold bg-white/20' : '' }}">
                                    Waktu Tunggu Rawat Jalan BPJS
                                </a>
                                <a href="{{ route('farmasi.sirkulasi.index') }}"
                                    class="block py-2 px-3 rounded-lg hover:bg-white/10 transition {{ request()->routeIs('farmasi.sirkulasi.*') ? 'text-white font-bold bg-white/20' : '' }}">
                                    Perputaran Obat
                                </a>
                                <a href="{{ route('farmasi.opname.index') }}"
                                    class="block py-2 px-3 rounded-lg hover:bg-white/10 transition {{ request()->routeIs('farmasi.opname.*') ? 'text-white font-bold bg-white/20' : '' }}">
                                    Opname
                                </a>
                                <a href="{{ route('farmasi.template_bu_sugati.index') }}"
                                    class="block py-2 px-3 rounded-lg hover:bg-white/10 transition {{ request()->routeIs('farmasi.template_bu_sugati.index') ? 'text-white font-bold bg-white/20' : '' }}">
                                    Rencana Anggaran
                                </a>
                            </div>
                        </div>

                        <!-- Rawat Jalan Menu -->
                        <div x-data="{ open: {{ request()->routeIs('rawat_jalan.*') ? 'true' : 'false' }} }" class="space-y-1">
                            <button @click="open = !open"
                                class="flex w-full items-center justify-between px-4 py-3 text-sm font-semibold rounded-xl hover:bg-white/10 transition text-left">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Rawat Jalan
                                </div>
                                <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform duration-200"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7">
                                    </path>
                                </svg>
                            </button>
                            <div x-show="open" x-collapse
                                class="pl-11 pr-4 py-2 space-y-1 text-xs font-medium text-white/80 bg-black/10 rounded-xl mx-2">
                                <a href="{{ route('rawat_jalan.alamat_dan_kontak.index') }}"
                                    class="block py-2 px-3 rounded-lg hover:bg-white/10 transition {{ request()->routeIs('rawat_jalan.alamat_dan_kontak.index') ? 'text-white font-bold bg-white/20' : '' }}">
                                    Alamat dan Kontak
                                </a>
                            </div>
                        </div>

                        <!-- Rawat Inap Menu -->
                        <div x-data="{ open: false }" class="space-y-1">
                            <button @click="open = !open"
                                class="flex w-full items-center justify-between px-4 py-3 text-sm font-semibold rounded-xl hover:bg-white/10 transition text-left">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                        </path>
                                    </svg>
                                    Rawat Inap
                                </div>
                                <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform duration-200"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7">
                                    </path>
                                </svg>
                            </button>
                            <div x-show="open" x-collapse
                                class="pl-11 pr-4 py-2 space-y-1 text-xs font-medium text-white/60 bg-black/10 rounded-xl mx-2 italic">
                                Modul belum tersedia
                            </div>
                        </div>

                        <!-- Rekam Medis Menu -->
                        <div x-data="{ open: false }" class="space-y-1">
                            <button @click="open = !open"
                                class="flex w-full items-center justify-between px-4 py-3 text-sm font-semibold rounded-xl hover:bg-white/10 transition text-left">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    Rekam Medis
                                </div>
                                <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform duration-200"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7">
                                    </path>
                                </svg>
                            </button>
                            <div x-show="open" x-collapse
                                class="pl-11 pr-4 py-2 space-y-1 text-xs font-medium text-white/60 bg-black/10 rounded-xl mx-2 italic">
                                Modul belum tersedia
                            </div>
                        </div>

                        <!-- Laboratorium Menu -->
                        <div x-data="{ open: {{ request()->routeIs('laboratorium.*') ? 'true' : 'false' }} }"
                            class="space-y-1">
                            <button @click="open = !open"
                                class="flex w-full items-center justify-between px-4 py-3 text-sm font-semibold rounded-xl hover:bg-white/10 transition text-left">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                                        </path>
                                    </svg>
                                    Laboratorium
                                </div>
                                <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform duration-200"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7">
                                    </path>
                                </svg>
                            </button>
                            <div x-show="open" x-collapse
                                class="pl-11 pr-4 py-2 space-y-1 text-xs font-medium text-white/80 bg-black/10 rounded-xl mx-2">
                                <a href="{{ route('laboratorium.index') }}"
                                    class="block py-2 px-3 rounded-lg hover:bg-white/10 transition {{ request()->routeIs('laboratorium.index') ? 'text-white font-bold bg-white/20' : '' }}">
                                    Waktu Tunggu Hasil Rawat Jalan
                                </a>
                                <a href="{{ route('laboratorium.index_ranap') }}"
                                    class="block py-2 px-3 rounded-lg hover:bg-white/10 transition {{ request()->routeIs('laboratorium.index_ranap') ? 'text-white font-bold bg-white/20' : '' }}">
                                    Waktu Tunggu Hasil Rawat Inap
                                </a>
                                <a href="{{ route('laboratorium.index_gabungan') }}"
                                    class="block py-2 px-3 rounded-lg hover:bg-white/10 transition {{ request()->routeIs('laboratorium.index_gabungan') ? 'text-white font-bold bg-white/20' : '' }}">
                                    Waktu Tunggu Hasil Gabungan
                                </a>
                            </div>
                        </div>

                        <!-- Radiologi Menu -->
                        <div x-data="{ open: false }" class="space-y-1">
                            <button @click="open = !open"
                                class="flex w-full items-center justify-between px-4 py-3 text-sm font-semibold rounded-xl hover:bg-white/10 transition text-left">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                                        </path>
                                    </svg>
                                    Radiologi
                                </div>
                                <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform duration-200"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7">
                                    </path>
                                </svg>
                            </button>
                            <div x-show="open" x-collapse
                                class="pl-11 pr-4 py-2 space-y-1 text-xs font-medium text-white/60 bg-black/10 rounded-xl mx-2 italic">
                                Modul belum tersedia
                            </div>
                        </div>
                    </nav>

                    <!-- Sidebar Footer -->
                    <div class="p-4 border-t border-white/10 flex-shrink-0">
                        <div class="flex items-center space-x-3 mb-4">
                            <div
                                class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-sm font-bold">
                                {{ strtoupper(substr(Auth::user()->username, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-bold truncate">{{ Auth::user()->username }}</p>
                                <p class="text-xs text-white/60">Administrator</p>
                            </div>
                        </div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center justify-center px-4 py-2 text-xs font-bold border border-white/20 rounded-xl hover:bg-white hover:text-primary transition group uppercase tracking-widest">
                                <svg class="w-4 h-4 mr-2 group-hover:text-primary" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                    </path>
                                </svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </aside>
        @endauth

        <!-- Main Content Wrapper -->
        <div class="flex flex-col flex-1 h-screen overflow-hidden min-w-0">
            @auth
                <!-- Mobile Header -->
                <header class="h-20 bg-white border-b flex items-center justify-between px-6 md:hidden flex-shrink-0">
                    <button @click="sidebarOpen = true" class="text-primary hover:bg-gray-100 p-2 rounded-lg transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <div class="flex flex-col items-center">
                        <span class="text-xl font-bold text-primary leading-none text-center">TARIKSIS</span>
                        <span class="text-[7px] font-black text-primary/40 uppercase tracking-tighter mt-1 leading-none">
                            TARIK data Sistem Informasi rS
                        </span>
                    </div>
                    <div class="w-10 h-10"></div> <!-- Spacer for balance -->
                </header>
            @endauth

            <!-- Scrollable Content -->
            <div class="flex-grow overflow-y-auto">
                <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-10">
                    @yield('content')
                </main>

                <footer class="py-10 text-center text-gray-400 border-t border-gray-100 bg-white">
                    <p class="text-xs font-medium uppercase tracking-widest">made by IT Staff RSIA IBI Surabaya &copy;
                        2026</p>
                </footer>
            </div>
        </div>
    </div>

    <!-- Floating Help Button -->
    <a href="http://192.168.100.177/mastolongmas/public/login" target="_blank"
        class="fixed bottom-6 right-6 bg-blue-600 text-white p-4 rounded-full shadow-lg hover:bg-blue-700 hover:scale-105 transition-all duration-300 flex items-center gap-3 group z-50 border-4 border-white">
        <span
            class="font-bold hidden group-hover:block transition-all whitespace-nowrap text-sm uppercase tracking-wider">Butuh
            Bantuan?</span>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
    </a>

    <script>
        async function handleDownload(url, filename) {
            Swal.fire({
                title: 'Tunggu sebentar...',
                text: 'Sedang menyiapkan dokumen untuk anda',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                const response = await fetch(url);
                
                if (!response.ok) {
                    // Try to get error message from response
                    let errorMessage = 'Terjadi kesalahan saat mengunduh file';
                    try {
                        const errorData = await response.json();
                        errorMessage = errorData.message || errorMessage;
                    } catch (e) {
                        errorMessage = `Error ${response.status}: ${response.statusText}`;
                    }
                    throw new Error(errorMessage);
                }

                const blob = await response.blob();
                const downloadUrl = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = downloadUrl;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(downloadUrl);
                a.remove();

                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Data telah berhasil diunduh',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            } catch (error) {
                Swal.fire({
                    title: 'Gagal Mengunduh',
                    text: error.message,
                    icon: 'error'
                });
            }
        }
    </script>
</body>

</html>