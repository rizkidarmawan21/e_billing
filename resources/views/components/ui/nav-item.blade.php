@props(['href', 'active' => false, 'icon' => null])
<a href="{{ $href }}" {{ $attributes->class(['nav-link panel-nav-link rounded', 'active' => $active]) }} @if($active) aria-current="page" @endif>
    @if($icon)<i class="bi bi-{{ $icon }}" aria-hidden="true"></i>@endif
    <span>{{ $slot }}</span>
</a>
