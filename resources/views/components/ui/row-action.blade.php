@props(['action', 'href' => null, 'label' => null])

@php
    [$icon, $variant, $title] = match ($action) {
        'detail' => ['eye', 'primary', 'Detail'],
        'edit' => ['pencil', 'secondary', 'Edit'],
        'delete' => ['trash', 'danger', 'Hapus'],
    };
    $classes = "btn btn-sm btn-outline-{$variant} record-action";
@endphp

@if($href)
    <a href="{{ $href }}" class="{{ $classes }}" aria-label="{{ $label ?? $title }}" title="{{ $title }}">
        <i class="bi bi-{{ $icon }}" aria-hidden="true"></i>
    </a>
@else
    <button type="submit" class="{{ $classes }}" aria-label="{{ $label ?? $title }}" title="{{ $title }}">
        <i class="bi bi-{{ $icon }}" aria-hidden="true"></i>
    </button>
@endif
