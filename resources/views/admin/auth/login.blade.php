<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Iniciar sesión · Admin LyK</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="flex min-h-screen items-center justify-center bg-sage-100 px-4 py-10">
    <main class="w-full max-w-md rounded-3xl bg-white p-8 shadow-xl shadow-navy/10">
        <img src="{{ app(\App\Support\Settings::class)->imageUrl('logo_path', 'images/brand/logo.png') }}" alt="LyK" class="mx-auto mb-4 h-28 w-auto">
        <h1 class="mb-6 text-center text-2xl">Iniciar sesión</h1>

        <form method="POST" action="{{ route('admin.login') }}" class="space-y-5" novalidate>
            @csrf
            <div>
                <label for="email" class="form-label">Correo electrónico</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="form-input" @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                @error('email')<p id="email-error" class="form-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="password" class="form-label">Contraseña</label>
                <input id="password" name="password" type="password" required autocomplete="current-password" class="form-input">
                @error('password')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <label class="flex items-center gap-2">
                <input type="checkbox" name="remember" value="1" class="h-5 w-5 rounded accent-leaf">
                <span>Mantener la sesión iniciada</span>
            </label>
            <button class="btn-primary w-full">Entrar</button>
        </form>
        <p class="mt-6 text-center"><a href="{{ route('home') }}" class="text-leaf-700 underline">Volver al sitio</a></p>
    </main>
</body>
</html>
