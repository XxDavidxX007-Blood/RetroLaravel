<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Retro Restaurant | Registro de Nuevo Cliente</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Montserrat:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        retro: {
                            gold: '#c5a059',
                            goldlight: '#d4b773',
                            dark: '#0a0a0a',
                            light: '#ffffff',
                            gray: '#1a1a1a',
                            subtle: '#f5f5f5'
                        }
                    },
                    fontFamily: {
                        heading: ['"Playfair Display"', 'serif'],
                        body: ['"Montserrat"', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        .input-elegant {
            background-color: transparent;
            border: none;
            border-bottom: 1px solid #e5e7eb;
            border-radius: 0;
            padding-left: 0;
            padding-right: 2rem;
            transition: all 0.3s ease;
        }

        .input-elegant:focus {
            outline: none;
            box-shadow: none;
            border-bottom-color: #c5a059;
        }

        .input-elegant::placeholder {
            color: #9ca3af;
            font-size: 0.875rem;
            font-weight: 300;
        }

        .bg-lux {
            background-image: url('https://images.unsplash.com/photo-1550966871-3ed3cdb5ed0c?auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
        }

        body {
            animation: fadeIn 0.6s ease forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body class="bg-retro-dark min-h-screen flex items-center justify-center p-4 md:p-10 font-body bg-lux relative">

    <!-- OSCURECER FONDO -->
    <div class="absolute inset-0 bg-black/60"></div>

    <!-- VOLVER AL ACCESO -->
    <a
        href="{{ route('login') }}"
        class="absolute top-6 left-6 md:top-8 md:left-8 text-white/80 hover:text-retro-gold transition z-20 font-body text-xs md:text-sm tracking-widest uppercase flex items-center gap-2"
    >
        <i class="fas fa-arrow-left"></i>
        <span>Volver al acceso</span>
    </a>

    <!-- CONTENEDOR PRINCIPAL -->
    <div class="bg-white shadow-2xl w-full max-w-3xl p-8 sm:p-12 md:p-16 relative z-10 my-10">

        <!-- ICONO Y CABECERA -->
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center mb-4 text-retro-gold text-3xl">
                <i class="fas fa-wine-glass"></i>
            </div>

            <h1 class="text-2xl sm:text-3xl md:text-4xl font-heading text-retro-dark mb-3 font-semibold tracking-wide">
                Registro de Nuevo Cliente
            </h1>

            <p class="text-gray-500 font-body text-xs sm:text-sm max-w-lg mx-auto font-normal leading-relaxed">
                Cree su cuenta para realizar reservas y disfrutar de nuestra propuesta gastronómica.
            </p>
        </div>

        <!-- ERRORES GENERALES -->
        @if (isset($errors) && $errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 text-xs sm:text-sm">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- FORMULARIO -->
        <form action="{{ route('register.post') }}" method="POST" class="space-y-7">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-7">

                <!-- NOMBRES -->
                <div class="relative">
                    <label class="block text-[11px] uppercase tracking-[0.18em] text-gray-800 mb-2 font-bold">
                        Nombres
                    </label>

                    <div class="relative">
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Ingrese su nombre"
                            required
                            class="w-full py-2 input-elegant text-sm text-retro-dark"
                        >
                    </div>
                </div>

                <!-- APELLIDOS -->
                <div class="relative">
                    <label class="block text-[11px] uppercase tracking-[0.18em] text-gray-800 mb-2 font-bold">
                        Apellidos
                    </label>

                    <div class="relative">
                        <input
                            type="text"
                            name="apellidos"
                            value="{{ old('apellidos') }}"
                            placeholder="Ingrese sus apellidos"
                            required
                            class="w-full py-2 input-elegant text-sm text-retro-dark"
                        >
                    </div>
                </div>

                <!-- CORREO ELECTRÓNICO -->
                <div class="relative">
                    <label class="block text-[11px] uppercase tracking-[0.18em] text-gray-800 mb-2 font-bold">
                        Correo Electrónico
                    </label>

                    <div class="relative">
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="ejemplo@correo.com"
                            required
                            class="w-full py-2 input-elegant text-sm text-retro-dark"
                        >
                        <span class="absolute inset-y-0 right-0 flex items-center pr-1 text-gray-400 pointer-events-none text-xs">
                            <i class="far fa-envelope"></i>
                        </span>
                    </div>
                </div>

                <!-- TELÉFONO -->
                <div class="relative">
                    <label class="block text-[11px] uppercase tracking-[0.18em] text-gray-800 mb-2 font-bold">
                        Teléfono
                    </label>

                    <div class="relative">
                        <input
                            type="text"
                            name="telefono"
                            value="{{ old('telefono') }}"
                            placeholder="+1 234 567 8900"
                            class="w-full py-2 input-elegant text-sm text-retro-dark"
                        >
                        <span class="absolute inset-y-0 right-0 flex items-center pr-1 text-gray-400 pointer-events-none text-xs">
                            <i class="fas fa-phone-alt"></i>
                        </span>
                    </div>
                </div>

                <!-- CONTRASEÑA -->
                <div class="relative">
                    <label class="block text-[11px] uppercase tracking-[0.18em] text-gray-800 mb-2 font-bold">
                        Contraseña
                    </label>

                    <div class="relative">
                        <input
                            type="password"
                            name="password"
                            placeholder="••••••••"
                            required
                            class="w-full py-2 input-elegant text-sm text-retro-dark"
                        >
                        <span class="absolute inset-y-0 right-0 flex items-center pr-1 text-gray-400 pointer-events-none text-xs">
                            <i class="fas fa-lock"></i>
                        </span>
                    </div>
                </div>

                <!-- CONFIRMAR CONTRASEÑA -->
                <div class="relative">
                    <label class="block text-[11px] uppercase tracking-[0.18em] text-gray-800 mb-2 font-bold">
                        Confirmar Contraseña
                    </label>

                    <div class="relative">
                        <input
                            type="password"
                            name="password_confirmation"
                            placeholder="••••••••"
                            required
                            class="w-full py-2 input-elegant text-sm text-retro-dark"
                        >
                        <span class="absolute inset-y-0 right-0 flex items-center pr-1 text-gray-400 pointer-events-none text-xs">
                            <i class="fas fa-check"></i>
                        </span>
                    </div>
                </div>

            </div>

            <!-- TÉRMINOS Y CONDICIONES -->
            <div class="flex items-start pt-3">
                <input
                    type="checkbox"
                    id="terms"
                    required
                    class="mt-1 w-4 h-4 text-retro-dark focus:ring-retro-gold border-gray-300 rounded-sm cursor-pointer accent-retro-dark"
                >
                <label for="terms" class="ml-3 block text-xs text-gray-600 font-normal leading-relaxed cursor-pointer select-none">
                    Acepto los términos de exclusividad, políticas de privacidad y normativas de reserva de Retro Restaurant Fine Dining.
                </label>
            </div>

            <!-- BOTÓN CREAR CUENTA -->
            <div class="pt-2">
                <button
                    type="submit"
                    class="w-full bg-retro-dark text-white font-body text-xs md:text-sm font-bold tracking-[0.2em] uppercase py-4 hover:bg-retro-gold transition-all duration-300 shadow-sm"
                >
                    Crear Cuenta
                </button>
            </div>
        </form>

        <!-- FOOTER / ACCESO -->
        <div class="mt-10 text-center pt-2">
            <p class="text-xs md:text-sm text-gray-600 font-normal">
                ¿Ya tiene una cuenta?
                <a
                    href="{{ route('login') }}"
                    class="font-bold text-retro-dark border-b border-retro-dark pb-0.5 ml-1 uppercase tracking-wider hover:text-retro-gold hover:border-retro-gold transition"
                >
                    Acceder
                </a>
            </p>
        </div>

    </div>

</body>
</html>