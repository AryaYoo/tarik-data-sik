@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-center min-h-[calc(100vh-160px)]">
        <div
            class="bg-white p-10 rounded-2xl shadow-2xl w-full max-w-md border-t-8 border-primary relative overflow-hidden">
            <!-- Decoration -->
            <div class="absolute top-0 right-0 -mr-16 -mt-16 w-32 h-32 bg-primary/5 rounded-full"></div>

            <div class="text-center mb-10 relative z-10">
                <h1 class="text-4xl font-black text-primary mb-2 tracking-tighter italic">TARIKSIS</h1>
                <p class="text-gray-400 uppercase tracking-widest text-[8px] font-bold">TARIK data Sistem Informasi rS</p>
            </div>

            <form action="{{ route('login') }}" method="POST" class="space-y-6 relative z-10">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Username</label>
                    <input type="text" name="username"
                        class="w-full px-5 py-4 rounded-2xl bg-gray-50 border-0 focus:bg-white focus:ring-4 focus:ring-primary/10 transition outline-none text-gray-800 placeholder-gray-300"
                        placeholder="Username RSIA" required autofocus>
                    @error('username')
                        <span class="text-red-500 text-[10px] uppercase font-bold mt-2 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Password</label>
                    <input type="password" name="password"
                        class="w-full px-5 py-4 rounded-2xl bg-gray-50 border-0 focus:bg-white focus:ring-4 focus:ring-primary/10 transition outline-none text-gray-800 placeholder-gray-300"
                        placeholder="••••••••" required>
                </div>

                <button type="submit"
                    class="w-full bg-primary text-white font-black py-5 rounded-2xl hover:bg-green-800 transition shadow-xl shadow-primary/20 transform hover:-translate-y-1 active:translate-y-0 text-sm tracking-widest uppercase">
                    Akses Sistem
                </button>
            </form>

            @if(isset($extractionCount))
                <div class="mt-12 pt-10 border-t border-gray-50 text-center relative z-10">
                    <div class="inline-flex items-center space-x-3 text-primary bg-primary/5 px-4 py-2 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="text-lg font-black">{{ $extractionCount }}</span>
                        <span class="text-[10px] uppercase font-bold text-gray-400">Total Penarikan</span>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection