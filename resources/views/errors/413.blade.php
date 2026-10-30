<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Archivo demasiado pesado</title>
    @vite(['resources/css/app.css'])
</head>
<body class="flex min-h-screen items-center justify-center bg-sage-100 px-4">
    <main class="max-w-lg rounded-3xl bg-white p-8 text-center shadow-xl shadow-navy/10">
        <h1 class="mb-4 text-2xl">Archivo demasiado pesado</h1>
        <p class="mb-6 text-lg">{{ $message ?? 'El archivo es demasiado pesado. Usa imágenes JPG, PNG o WEBP de máximo 5 MB.' }}</p>
        <button type="button" onclick="history.back()" class="btn-primary">Volver al formulario</button>
    </main>
</body>
</html>
