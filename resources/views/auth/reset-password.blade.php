<x-guest-layout>
    <x-ui.page-header :title="__('Reset Password')" />
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <x-ui.field name="email" :label="__('Email')" :required="true">
            <input id="email" class="form-control" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username">
        </x-ui.field>

        <!-- Password -->
        <x-ui.field name="password" :label="__('Password')" :required="true">
            <x-ui.password-input id="password" name="password" required autocomplete="new-password" />
        </x-ui.field>

        <!-- Confirm Password -->
        <x-ui.field name="password_confirmation" :label="__('Confirm Password')" :required="true">
            <x-ui.password-input id="password_confirmation" name="password_confirmation" required autocomplete="new-password" />
        </x-ui.field>

        <x-ui.form-actions class="mt-4 justify-content-md-end">
            <button type="submit" class="btn btn-primary">
                {{ __('Reset Password') }}
            </button>
        </x-ui.form-actions>
    </form>
</x-guest-layout>
