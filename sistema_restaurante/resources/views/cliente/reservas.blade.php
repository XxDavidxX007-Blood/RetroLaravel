@extends('layouts.app')

@section('title', 'Mis Reservas | Retro Menú')

@section('header', 'MIS RESERVAS')

@section('content')

<div class="max-w-6xl mx-auto">

    <div class="flex justify-between items-center mb-6">

        <div>
            <h1 class="font-heading text-3xl font-bold">
                Mis reservas
            </h1>

            <p class="text-gray-500 mt-1">
                Consulta y administra tus reservas.
            </p>
        </div>

        <button
            class="bg-[#c5a059] text-white px-5 py-3 rounded-xl
                   font-semibold hover:bg-[#b18e48] transition"
        >
            <i class="fas fa-plus mr-2"></i>
            Nueva reserva
        </button>

    </div>


    <div class="bg-white rounded-2xl border border-gray-200 p-8">

        <div class="text-center py-12">

            <i class="fas fa-calendar-check text-5xl text-[#c5a059] mb-4"></i>

            <h2 class="font-heading text-2xl font-semibold">
                No tienes reservas
            </h2>

            <p class="text-gray-500 mt-2">
                Cuando realices una reserva aparecerá aquí.
            </p>

        </div>

    </div>

</div>

@endsection