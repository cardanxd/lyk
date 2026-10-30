@props(['tag'])
<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full border px-2.5 py-0.5 text-sm font-semibold '.$tag->classes()]) }}>{{ $tag->name }}</span>
