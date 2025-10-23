
@extends('layouts.app')

@section('content')
<div class="w-full h-screen bg-cover bg-center flex items-center justify-center" 
     style="background-image: url('{{ asset('assets/welcomePageBg.jpg') }}');">
    
    <!-- <div class="absolute inset-0 bg-black bg-opacity-40"></div> -->
    
    <!-- <div class="relative z-10 text-center animate-fadeIn">
        <h1 class="text-7xl font-bold text-white mb-6 drop-shadow-2xl">
            🇮🇩 PETUALANGAN PANCASILA 🇮🇩
        </h1>
        <p class="text-3xl text-white mb-12 drop-shadow-lg">
            Game Edukasi Nilai-Nilai Luhur Bangsa
        </p> -->
        
        <a href="{{ route('game.main') }}" 
           class="inline-flex items-center mt-60 gap-8 bg-yellow-400 hover:bg-yellow-500 text-blue-900 font-bold text-3xl px-16 py-8 rounded-full shadow-2xl transform hover:scale-110 transition-all duration-300">
            <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20">
                <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/>
            </svg>
            MULAI PETUALANGAN
        </a>
       
    </div>
</div>
@endsection
