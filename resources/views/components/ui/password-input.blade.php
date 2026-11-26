@props(['id', 'name', 'autocomplete' => 'current-password'])
<div class="input-group password-input" x-data="{ showPassword: false }">
    <input id="{{ $id }}" name="{{ $name }}" type="password" :type="showPassword ? 'text' : 'password'" autocomplete="{{ $autocomplete }}" {{ $attributes->except(['value', 'type'])->class(['form-control']) }}>
    <button type="button" class="btn btn-outline-secondary password-toggle" aria-label="Tampilkan password" :aria-label="showPassword ? 'Sembunyikan password' : 'Tampilkan password'" aria-controls="{{ $id }}" @click="showPassword = !showPassword">
        <i class="bi bi-eye" :class="{ 'bi-eye': !showPassword, 'bi-eye-slash': showPassword }" aria-hidden="true"></i>
    </button>
</div>
