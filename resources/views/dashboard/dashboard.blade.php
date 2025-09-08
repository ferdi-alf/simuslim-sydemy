@extends('layouts.dashboard-layouts')
@section('title', 'Dashboard Page')

@section('content')
<div class="p-6 space-y-6">

    <!-- Welcome Section -->
    <div class="bg-white shadow rounded-xl p-6">
        <h1 class="text-2xl font-bold">Assalamu’alaikum, {{ Auth::user()->name }} 👋</h1>
        <p class="text-gray-600 mt-2">Semoga harimu penuh keberkahan 🌸</p>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div class="p-4 bg-indigo-100 rounded-xl shadow text-center">
            <h3 class="text-lg font-bold">Total User</h3>
            <p class="text-2xl font-extrabold mt-2">{{ $totalUser }}</p>
        </div>
        <div class="p-4 bg-green-100 rounded-xl shadow text-center">
            <h3 class="text-lg font-bold">Kajian</h3>
            <p class="text-2xl font-extrabold mt-2">{{ $totalKajian }}</p>
        </div>
    </div>

    <!-- Quote Islami -->
    <div class="mt-6 p-6 bg-gray-50 rounded-xl shadow italic text-center text-gray-700">
        "Sebaik-baik manusia adalah yang paling bermanfaat bagi manusia."  
        <br><span class="text-sm">– HR. Ahmad</span>
    </div>

</div>
@endsection
