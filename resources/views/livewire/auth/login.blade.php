<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('auth.login_claim')" :description="__('auth.login_prompt')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('auth.email')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="ejemplo@correo.es"
            />

            <!-- Password -->
            <div class="relative">
                <flux:input
                    name="password"
                    :label="__('auth.password_field')"
                    type="password"
                    required
                    autocomplete="current-password"
                    :placeholder="__('auth.password_field')"
                    viewable
                />

                @if (Route::has('password.request'))
                    <flux:link class="absolute top-0 text-sm end-0" :href="route('password.request')" wire:navigate>
                        {{ __('auth.forgot_your_password') }}
                    </flux:link>
                @endif
            </div>

            <!-- Remember Me -->
            <flux:checkbox name="remember" :label="__('auth.remember_me')" :checked="old('remember')" />

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full" data-test="login-button">
                    {{ __('auth.login') }}
                </flux:button>
            </div>
        </form>

        @if (Route::has('register'))
            <div class="space-x-1 text-sm text-center rtl:space-x-reverse text-zinc-600 dark:text-zinc-400">
                <span>{{ __('auth.dont_have_account') }}</span>
                <flux:link :href="route('register')" wire:navigate>{{ __('auth.sign_up') }}</flux:link>
            </div>
        @endif
    </div>
</x-layouts.auth>
