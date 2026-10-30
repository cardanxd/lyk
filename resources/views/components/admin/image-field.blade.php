@props(['name', 'label', 'currentUrl' => null, 'required' => false])

<div>
    <label for="{{ $name }}" class="form-label">{{ $label }}</label>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
        @if ($currentUrl)
            <img src="{{ $currentUrl }}" alt="Imagen actual de {{ $label }}" class="h-24 w-auto max-w-48 rounded-lg border border-sage bg-white object-contain p-1">
        @endif
        <div class="flex-1">
            <input id="{{ $name }}" name="{{ $name }}" type="file" accept="image/jpeg,image/png,image/webp"
                   @if ($required) required @endif
                   class="block w-full cursor-pointer rounded-lg border border-gray-300 bg-white text-base file:mr-4 file:min-h-11 file:cursor-pointer file:border-0 file:bg-sage-100 file:px-4 file:font-semibold file:text-navy"
                   aria-describedby="{{ $name }}-help">
            <p id="{{ $name }}-help" class="form-help">JPG, PNG o WEBP, máx. 5 MB.{{ $currentUrl ? ' Deja vacío para conservar la actual.' : '' }}</p>
            @error($name)<p class="form-error">{{ $message }}</p>@enderror
        </div>
    </div>
</div>
