@extends('layouts.app')

@section('title', 'Mis Pedidos | Retro Menú')

@section('header', 'MIS PEDIDOS')

@section('content')

<div class="max-w-6xl mx-auto">

    <div class="mb-6">

        <h1 class="font-heading text-3xl font-bold">
            Mis pedidos
        </h1>

        <p class="text-gray-500 mt-1">
            Consulta el historial de tus pedidos.
        </p>

    </div>


    <div class="bg-white rounded-2xl border border-gray-200 p-8">

        <div class="text-center py-12">

            <i class="fas fa-receipt text-5xl text-[#c5a059] mb-4"></i>

            <h2 class="font-heading text-2xl font-semibold">
                No tienes pedidos
            </h2>

            <p class="text-gray-500 mt-2">
                Tus pedidos aparecerán aquí.
            </p>

            <a
                href="{{ route('productos.index') }}"
                class="inline-block mt-5 bg-[#c5a059] text-white
                       px-5 py-3 rounded-xl font-semibold"
            >
                Ver catálogo
            </a>

        </div>

    </div>

</div>

@endsection