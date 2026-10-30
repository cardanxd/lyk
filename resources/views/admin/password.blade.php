@extends('layouts.admin')

@section('title', 'Mi contraseña')

@section('content')
    <form method="POST" action="{{ route('admin.password.update') }}" class="max-w-lg space-y-5 rounded-2xl bg-white p-6">
        @csrf
        @method('PUT')
        @foreach ([
            ['current_password', 'Contraseña actual', 'current-password'],
            ['password', 'Nueva contraseña', 'new-password'],
            ['password_confirmation', 'Confirma la nueva contraseña', 'new-password'],
        ] as [$name, $label, $autocomplete])
            <div>
                <label for="{{ $name }}" class="form-label">{{ $label }}</label>
                <input id="{{ $name }}" name="{{ $name }}" type="password" autocomplete="{{ $autocomplete }}" class="form-input" required>
                @error($name)<p class="form-error">{{ $message }}</p>@enderror
            </div>
        @endforeach
        <p class="form-help">Mínimo 8 caracteres.</p>
        <button class="btn-primary">Guardar contraseña</button>
    </form>
@endsection
