
@extends('layouts.app')

@section('content')
<div class="w-full h-screen flex items-center justify-center" 
     style="background-color: {{ $ending['warna'] }}">
    <div class="bg-white rounded-3xl p-12 shadow-2xl text-center max-w-2xl animate-fadeIn">
        <h1 class="text-5xl font-bold mb-6" style="color: {{ $ending['warna'] }}">
            {{ $ending['judul'] }}
        </h1>
        <p class="text-2xl mb-8 text-gray-700">{{ $ending['pesan'] }}</p>
        
        <div class="bg-gray-100 rounded-xl p-6 mb-8">
            <h2 class="text-3xl font-bold mb-4">📊 Hasil Akhir</h2>
            <div class="grid grid-cols-2 gap-4 text-xl">
                <div class="bg-white rounded-lg p-4">
                    <div class="text-gray-600">Skor Pancasila</div>
                    <div class="text-4xl font-bold text-blue-600">{{ $skorAkhir }}</div>
                </div>
                <div class="bg-white rounded-lg p-4">
                    <div class="text-gray-600">NPC Selesai</div>
                    <div class="text-4xl font-bold text-green-600">{{ $npcSelesai }}/6</div>
                </div>
            </div>
        </div>

        <div class="flex gap-4 justify-center">
            <a href="{{ route('game.main') }}" 
               class="bg-blue-500 hover:bg-blue-600 text-white font-bold text-xl px-8 py-4 rounded-full shadow-lg inline-flex items-center gap-2">
                🔄 Main Lagi
            </a>
            <a href="{{ route('game.menu') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white font-bold text-xl px-8 py-4 rounded-full shadow-lg">
                🏠 Menu Utama
            </a>
        </div>
    </div>
</div>
@endsection