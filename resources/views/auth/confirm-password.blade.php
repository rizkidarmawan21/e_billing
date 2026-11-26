<x-guest-layout>
    <x-ui.page-header title="Confirm Password" />
    <div class="mb-4 text-muted">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <x-ui.field name="password" :label="__('Password')" :required="true">
            <x-ui.password-input id="password" name="password" required autocomplete="current-password" />
        </x-ui.field>

        <x-ui.form-actions class="mt-4 justify-content-md-end">
            <button type="submit" class="btn btn-primary">
                {{ __('Confirm') }}
            </button>
        </x-ui.form-actions>
    </form>
</x-guest-layout>
