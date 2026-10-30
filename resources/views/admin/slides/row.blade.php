<div class="flex items-center gap-4">
    <img src="{{ Storage::disk('public')->url($item->image_path) }}" alt="" class="h-16 w-28 shrink-0 rounded-lg object-cover">
    <span class="font-semibold text-navy">{{ $item->title ?: 'Sin título' }}</span>
</div>
