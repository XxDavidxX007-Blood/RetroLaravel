@extends($layout ?? 'layouts.admin')

@section('title', 'Mi Perfil | Retro Restaurant')

@section('header')
    <div class="flex items-center gap-3">
        <span class="w-1.5 h-6 bg-retro-gold rounded-full inline-block"></span>
        <span class="font-heading font-bold text-gray-900 tracking-wider">MI PERFIL</span>
    </div>
@endsection

@section('content')

<div class="max-w-3xl mx-auto space-y-7 pb-12">

    <!-- CABECERA DE LA PÁGINA -->
    <div>
        <div class="flex items-center gap-3">
            <i class="fas fa-circle-user text-3xl text-gray-900"></i>
            <h1 class="font-heading text-3xl font-bold text-gray-900 tracking-tight">
                Mi Perfil
            </h1>
        </div>
        <p class="text-sm text-gray-500 mt-1">
            Edita tu información personal y contraseña.
        </p>
    </div>

    <!-- ALERTAS -->
    @if(session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-2xl flex justify-between items-center shadow-sm">
            <div class="flex items-center gap-3">
                <i class="fas fa-circle-check text-emerald-600 text-lg"></i>
                <p class="text-sm font-medium text-emerald-800">{{ session('success') }}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded-r-2xl flex justify-between items-center shadow-sm">
            <div class="flex items-center gap-3">
                <i class="fas fa-circle-exclamation text-rose-600 text-lg"></i>
                <div>
                    @foreach ($errors->all() as $error)
                        <p class="text-sm font-medium text-rose-800">{{ $error }}</p>
                    @endforeach
                </div>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
    @endif

    <!-- TARJETA 1: PERFIL PRINCIPAL -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">

        <!-- BANNER SUPERIOR OSCURO -->
        <div class="h-28 bg-[#1f2937] relative"></div>

        <!-- FOTO DE PERFIL / AVATAR + NOMBRE Y ROL -->
        <div class="px-8 pb-4 relative">
            <div class="-mt-14 inline-block relative">
                @if($user->foto)
                    <img
                        src="{{ asset('storage/' . $user->foto) }}"
                        alt="{{ $user->name }}"
                        class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-md bg-white"
                    >
                @else
                    <div class="w-24 h-24 rounded-full border-4 border-white shadow-md bg-white flex items-center justify-center p-1">
                        <div class="w-full h-full rounded-full bg-amber-50 border border-amber-200 flex items-center justify-center text-retro-gold font-heading font-bold text-2xl">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    </div>
                @endif
            </div>

            <div class="mt-3">
                <h2 class="font-heading text-2xl font-bold text-gray-900">
                    {{ $user->name }} {{ $user->apellidos }}
                </h2>

                <div class="mt-1.5">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#fee2e2] text-[#ef4444]">
                        <i class="fas fa-user-shield text-[10px]"></i>
                        {{ $user->role->nombre ?? 'Usuario' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- FORMULARIO DE EDICIÓN -->
        <form action="{{ route('perfil.update') }}" method="POST" enctype="multipart/form-data" class="p-8 pt-4 space-y-6">
            @csrf
            @method('PUT')

            <!-- NOMBRES Y APELLIDOS -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">
                        Nombre <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', $user->name) }}"
                        required
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-xs text-gray-800 focus:outline-none focus:border-retro-gold transition bg-gray-50/30 focus:bg-white"
                    >
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">
                        Apellidos <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="apellidos"
                        value="{{ old('apellidos', $user->apellidos) }}"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-xs text-gray-800 focus:outline-none focus:border-retro-gold transition bg-gray-50/30 focus:bg-white"
                    >
                </div>
            </div>

            <!-- CORREO Y TELÉFONO -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">
                        Correo electrónico
                    </label>
                    <input
                        type="email"
                        value="{{ $user->email }}"
                        readonly
                        disabled
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-xs text-gray-400 bg-gray-50 cursor-not-allowed select-none"
                    >
                    <p class="text-[11px] text-gray-400 mt-1.5">
                        El correo no se puede cambiar.
                    </p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">
                        Teléfono
                    </label>
                    <input
                        type="text"
                        name="telefono"
                        value="{{ old('telefono', $user->telefono) }}"
                        placeholder="3204417080"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-xs text-gray-800 focus:outline-none focus:border-retro-gold transition bg-gray-50/30 focus:bg-white"
                    >
                </div>
            </div>

            <!-- SECCIÓN: CAMBIAR CONTRASEÑA -->
            <div class="pt-4 border-t border-gray-100">
                <div class="flex items-center gap-2 mb-4">
                    <i class="fas fa-lock text-gray-500 text-xs"></i>
                    <span class="text-xs font-bold text-gray-800">
                        Cambiar contraseña
                    </span>
                    <span class="text-xs text-gray-400 font-normal">
                        (dejar en blanco para no cambiar)
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="relative">
                        <label class="block text-xs font-bold text-gray-700 mb-2">
                            Nueva contraseña
                        </label>
                        <div class="relative">
                            <input
                                type="password"
                                name="password"
                                id="passwordInput"
                                placeholder="••••••••"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-xs text-gray-800 focus:outline-none focus:border-retro-gold transition bg-gray-50/30 focus:bg-white pr-10"
                            >
                            <button
                                type="button"
                                onclick="togglePass('passwordInput', this)"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 cursor-pointer"
                            >
                                <i class="far fa-eye text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <div class="relative">
                        <label class="block text-xs font-bold text-gray-700 mb-2">
                            Confirmar contraseña
                        </label>
                        <div class="relative">
                            <input
                                type="password"
                                name="password_confirmation"
                                id="passwordConfirmInput"
                                placeholder="••••••••"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-xs text-gray-800 focus:outline-none focus:border-retro-gold transition bg-gray-50/30 focus:bg-white pr-10"
                            >
                            <button
                                type="button"
                                onclick="togglePass('passwordConfirmInput', this)"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 cursor-pointer"
                            >
                                <i class="far fa-eye text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BOTONES DE ACCIÓN -->
            <div class="pt-4 flex justify-end items-center gap-4">
                <a
                    href="{{ url()->previous() }}"
                    class="text-xs text-gray-500 hover:text-gray-800 font-medium transition"
                >
                    Cancelar
                </a>

                <button
                    type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-[#0a0a0a] hover:bg-retro-gold text-white font-bold text-xs uppercase tracking-wider transition-all duration-300 shadow-sm"
                >
                    <i class="fas fa-lock text-xs"></i>
                    <span>Guardar cambios</span>
                </button>
            </div>

        </form>

    </div>

    <!-- TARJETA 2: INFORMACIÓN DE SESIÓN -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-8 space-y-4">

        <div class="flex items-center gap-2 text-gray-700 font-bold text-xs">
            <i class="fas fa-circle-info text-gray-400"></i>
            <span>Información de sesión</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- ID DE USUARIO -->
            <div class="bg-[#f9fafb] p-4 rounded-2xl border border-gray-100/70">
                <span class="text-[11px] text-gray-400 font-medium block">
                    ID de usuario
                </span>
                <span class="text-sm font-bold text-gray-900 mt-1 block">
                    #{{ $user->id }}
                </span>
            </div>

            <!-- ROL -->
            <div class="bg-[#f9fafb] p-4 rounded-2xl border border-gray-100/70">
                <span class="text-[11px] text-gray-400 font-medium block">
                    Rol
                </span>
                <span class="text-sm font-bold text-[#ef4444] mt-1 block">
                    {{ $user->role->nombre ?? 'Usuario' }}
                </span>
            </div>
        </div>

        <!-- CORREO -->
        <div class="bg-[#f9fafb] p-4 rounded-2xl border border-gray-100/70">
            <span class="text-[11px] text-gray-400 font-medium block">
                Correo
            </span>
            <span class="text-sm font-bold text-gray-900 mt-1 block">
                {{ $user->email }}
            </span>
        </div>

    </div>

</div>

@endsection

@push('scripts')
<script>
    function togglePass(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
@endpush
