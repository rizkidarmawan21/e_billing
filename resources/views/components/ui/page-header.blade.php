@props(['title' => null, 'description' => null])
<div {{ $attributes->class(['page-toolbar mb-4']) }}>
    <div class="page-toolbar-copy">
        @if($title)<h2 class="h4 fw-bold mb-1">{{ $title }}</h2>@endif
        @if($description)<p class="text-muted mb-0">{{ $description }}</p>@endif
        {{ $slot }}
    </div>
    @isset($actions)<div class="page-toolbar-actions">{{ $actions }}</div>@endisset
</div>
