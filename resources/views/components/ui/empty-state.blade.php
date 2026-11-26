@props(['title', 'description' => null])
<div {{ $attributes->class(['empty-state text-center py-4 px-3']) }}>
    <p class="fw-semibold mb-1">{{ $title }}</p>
    @if($description)<p class="text-muted mb-3">{{ $description }}</p>@endif
    {{ $slot }}
</div>
