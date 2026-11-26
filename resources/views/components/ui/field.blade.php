@props(['name', 'label', 'id' => null, 'required' => false, 'help' => null, 'errorBag' => 'default'])
@php($fieldId = $id ?? $name)
<div {{ $attributes->class(['mb-3 ui-field']) }}>
    <label for="{{ $fieldId }}" class="form-label fw-semibold">{{ $label }} @if($required)<span class="text-danger" aria-hidden="true">*</span><span class="visually-hidden">(wajib)</span>@endif</label>
    {{ $slot }}
    @if($help)<div id="{{ $fieldId }}-help" class="form-text">{{ $help }}</div>@endif
    @error($name, $errorBag)<div id="{{ $fieldId }}-error" class="text-danger mt-1" role="alert">{{ $message }}</div>@enderror
</div>
