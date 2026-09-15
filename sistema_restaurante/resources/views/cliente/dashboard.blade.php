@extends('layouts.App')

@section('title', 'Dashboard Cliente')

@section('header', 'DASHBOARD')

@section('content')

<div class="max-w-7xl mx-auto">

    <h1 class="text-3xl font-heading text-gray-900">
        ¡Bienvenido, {{ auth()->user()->name }}! 👋
    </h1>

    <p class="text-gray-500 mt-1">
        ¿Qué quieres disfrutar hoy?
    </p>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mt-8">

        <div class="bg-[#1a1f2e] text-white rounded-2xl p-6">
            <p class="text-gray-400">Mis Reservas</p>
            <h2 class="text-4xl font-bold mt-3">0</h2>
            <p class="text-gray-400 mt-2">próximas</p>

            <a href="#" class="text-yellow-400 mt-4 inline-block">
                Ver todas →
            </a>
        </div>

        <div class="bg-[#1a1f2e] text-white rounded-2xl p-6">
            <p class="text-gray-400">Mis Pedidos</p>
            <h2 class="text-4xl font-bold mt-3">0</h2>
            <p class="text-gray-400 mt-2">pedidos</p>

            <a href="#" class="text-yellow-400 mt-4 inline-block">
                Ver historial →
            </a>
        </div>

        <div class="bg-[#1a1f2e] text-white rounded-2xl p-6">
            <p class="text-gray-400">Domicilios</p>
            <h2 class="text-4xl font-bold mt-3">0</h2>
            <p class="text-gray-400 mt-2">
                Pide a domicilio
            </p>

            <a href="#" class="text-yellow-400 mt-4 inline-block">
                Ordenar ahora →
            </a>
        </div>

        <div class="bg-[#1a1f2e] text-white rounded-2xl p-6">
            <p class="text-gray-400">Menú</p>
            <h2 class="text-4xl font-bold mt-3">6+</h2>
            <p class="text-gray-400 mt-2">
                Explorar carta
            </p>

            <a href="#" class="text-yellow-400 mt-4 inline-block">
                Ver menú →
            </a>
        </div>

    </div>

</div>

@endsection