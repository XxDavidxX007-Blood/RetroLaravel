@extends('layouts.app')

@section('title', 'Mi Perfil | Retro Menú')

@section('header', 'PERFIL')

@section('content')

<div class="max-w-4xl mx-auto">

    <div class="mb-6">

        <h1 class="font-heading text-3xl font-bold">
            Mi perfil
        </h1>

        <p class="text-gray-500 mt-1">
            Consulta tu información personal.
        </p>

    </div>


    <div class="bg-white rounded-2xl border border-gray-200 p-8">


        {{-- AVATAR --}}

        <div class="flex items-center gap-5 mb-8">

            <div
                class="w-20 h-20 rounded-full bg-[#c5a059]
                       flex items-center justify-center"
            >

                <span class="font-heading text-3xl font-bold text-white">

                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}

                </span>

            </div>


            <div>

                <h2 class="font-heading text-2xl font-semibold">

                    {{ auth()->user()->name }}

                </h2>

                <p class="text-gray-500">

                    {{ auth()->user()->email }}

                </p>

            </div>

        </div>


        {{-- INFORMACIÓN --}}

        <div class="grid md:grid-cols-2 gap-6">


            <div>

                <label class="block text-sm font-semibold text-gray-600 mb-2">
                    Nombre
                </label>

                <input
                    type="text"
                    value="{{ auth()->user()->name }}"
                    readonly
                    class="w-full bg-gray-50 border border-gray-200
                           rounded-xl px-4 py-3"
                >

            </div>


            <div>

                <label class="block text-sm font-semibold text-gray-600 mb-2">
                    Correo electrónico
                </label>

                <input
                    type="email"
                    value="{{ auth()->user()->email }}"
                    readonly
                    class="w-full bg-gray-50 border border-gray-200
                           rounded-xl px-4 py-3"
                >

            </div>


        </div>

    </div>

</div>

@endsection