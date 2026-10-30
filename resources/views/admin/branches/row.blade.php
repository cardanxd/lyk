<div class="flex items-center gap-4">
    @if ($item->photo_path)
        <img src="{{ storage_url($item->photo_path) }}" alt="" class="h-16 w-24 shrink-0 rounded-lg object-cover">
    @endif
    <div class="min-w-0">
        <span class="font-semibold text-navy">{{ $item->name }}</span>
        <p class="truncate text-muted">{{ $item->address }}</p>
    </div>
</div>
