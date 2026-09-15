@extends('layouts.app')

@section('title', 'Domicilios | Retro Menú')

@section('header', 'DOMICILIOS')

@section('content')

<div class="max-w-6xl mx-auto">

    <div class="mb-6">

        <h1 class="font-heading text-3xl font-bold">
            Domicilios
        </h1>

        <p class="text-gray-500 mt-1">
            Realiza tu pedido y recíbelo directamente en tu domicilio.
        </p>

    </div>


    <div class="bg-white rounded-2xl border border-gray-200 p-8">

        <div class="text-center py-12">

            <i class="fas fa-motorcycle text-5xl text-[#c5a059] mb-4"></i>

            <h2 class="font-heading text-2xl font-semibold">
                Pide a domicilio
            </h2>

            <p class="text-gray-500 mt-2">
                Explora nuestro menú y realiza tu pedido.
            </p>

            <a
                href="{{ route('productos.index') }}"
                class="inline-block mt-5 bg-[#c5a059] text-white
                       px-5 py-3 rounded-xl font-semibold"
            >
                Explorar menú
            </a>

        </div>

    </div>

</div>

@endsection