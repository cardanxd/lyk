@props(['name', 'label', 'value' => null, 'type' => 'text', 'help' => null, 'rows' => null, 'required' => false])

<div>
    <label for="{{ $name }}" class="form-label">{{ $label }}@if ($required) <span class="text-red-700" aria-hidden="true">*</span>@endif</label>
    @if ($rows)
        <textarea id="{{ $name }}" name="{{ $name }}" rows="{{ $rows }}" class="form-input" @if ($required) required @endif
                  @if ($help) aria-describedby="{{ $name }}-help" @endif {{ $attributes }}>{{ old($name, $value) }}</textarea>
    @else
        <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ old($name, $value) }}" class="form-input" @if ($required) required @endif
               @if ($help) aria-describedby="{{ $name }}-help" @endif {{ $attributes }}>
    @endif
    @if ($help)<p id="{{ $name }}-help" class="form-help">{{ $help }}</p>@endif
    @error($name)<p class="form-error">{{ $message }}</p>@enderror
</div>
