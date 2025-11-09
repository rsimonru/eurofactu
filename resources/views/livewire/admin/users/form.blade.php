<?php

use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Volt\Component;

new class extends Component {
    public ?User $user = null;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $isEditing = false;
    public string $activeTab = 'roles';

    /**
     * Mount the component
     */
    public function mount(?int $id = null): void
    {
        if ($id) {
            $this->user = User::emtGet($id);
            $this->isEditing = true;
            $this->name = $this->user->name;
            $this->email = $this->user->email;
        }
    }

    /**
     * Get component state
     */
    public function with(): array
    {
        return [
            'pageTitle' => $this->isEditing
                ? __('admin.edit_user')
                : __('admin.create_user'),
            'breadcrumbs' => [
                ['label' => __('admin.admin'), 'url' => null],
                ['label' => trans_choice('admin.users', 2), 'url' => route('admin.users.index')],
                ['label' => $this->isEditing ? __('admin.edit') : __('admin.new'), 'url' => null],
            ],
            'permissions' => $this->getPermissions(),
        ];
    }
    public function getPermissions(): array
    {
        // This method would return a list of permissions.
        // For demonstration purposes, we'll return a static array.
        $permissions = Permission::emtGet(
                records_in_page: -1,
            )->all();

        return $permissions;
    }

    /**
     * Save user (create or update)
     */
    public function save(): void
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->user?->id),
            ],
        ];

        // Password is required for new users, optional for editing
        if (!$this->isEditing) {
            $rules['password'] = ['required', 'string', Password::defaults(), 'confirmed'];
        } elseif ($this->password) {
            $rules['password'] = ['string', Password::defaults(), 'confirmed'];
        }

        $validated = $this->validate($rules);

        if ($this->isEditing) {
            // Update existing user
            $this->user->name = $validated['name'];
            $this->user->email = $validated['email'];

            if (!empty($validated['password'])) {
                $this->user->password = Hash::make($validated['password']);
            }

            $this->user->save();

            session()->flash('status', __('admin.user_updated'));
        } else {
            // Create new user
            $this->user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);
            session()->flash('status', ['id' => $this->user->id], __('admin.user_created'));
        }

        $this->redirect(route('admin.users.index'), navigate: true);
    }

    /**
     * Cancel and return to users list
     */
    public function cancel(): void
    {
        $this->redirect(route('admin.users.index'), navigate: true);
    }
}; ?>

<section class="w-full">
    <x-document.layout
        :title="$pageTitle"
        :breadcrumbs="$breadcrumbs"
        :buttons="[]"
    >
        @if (session('status'))
            <flux:callout icon="bell" variant="secondary" inline x-data="{ visible: true }" x-show="visible" class="mb-6">
                <flux:callout.heading class="flex gap-2 @max-md:flex-col items-start">{{ session('status') }}</flux:callout.heading>
                <x-slot name="controls">
                    <flux:button icon="x-mark" variant="ghost" x-on:click="visible = false" />
                </x-slot>
            </flux:callout>
        @endif

        <!-- Two-column layout: Form on left, Tabs on right (desktop), stacked on mobile -->
        <div class="flex flex-col gap-6 lg:flex-row">
            <!-- Left Column: Main Form -->
            <div class="w-full lg:w-1/2">
                <form wire:submit="save" class="space-y-6">
                    <!-- Name Field -->
                    <flux:field>
                        <flux:label>{{ __('general.name') }}</flux:label>
                        <flux:input
                            wire:model="name"
                            type="text"
                            autofocus
                            autocomplete="name"
                            placeholder="{{ __('admin.enter_name') }}"
                        />
                        <flux:error name="name" />
                    </flux:field>

                    <!-- Email Field -->
                    <flux:field>
                        <flux:label>{{ __('general.email') }}</flux:label>
                        <flux:input
                            wire:model="email"
                            type="email"
                            autocomplete="email"
                            placeholder="{{ __('admin.enter_email') }}"
                        />
                        <flux:error name="email" />
                    </flux:field>

                    <!-- Password Field -->
                    <flux:field>
                        <flux:label>
                            {{ __('general.password') }}
                            @if($isEditing)
                                <span class="text-xs font-normal text-zinc-500">({{ __('admin.leave_blank_keep_current') }})</span>
                            @endif
                        </flux:label>
                        <flux:input
                            wire:model="password"
                            type="password"
                            autocomplete="new-password"
                            placeholder="{{ __('admin.enter_password') }}"
                        />
                        <flux:error name="password" />
                    </flux:field>

                    <!-- Confirm Password Field -->
                    <flux:field>
                        <flux:label>{{ __('general.confirm_password') }}</flux:label>
                        <flux:input
                            wire:model="password_confirmation"
                            type="password"
                            autocomplete="new-password"
                            placeholder="{{ __('admin.confirm_password_placeholder') }}"
                        />
                        <flux:error name="password_confirmation" />
                    </flux:field>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-3">
                        <flux:button type="button" variant="ghost" wire:click="cancel">
                            {{ __('general.cancel') }}
                        </flux:button>

                        <flux:button type="submit" variant="primary">
                            {{ $isEditing ? __('general.save') : __('admin.create') }}
                        </flux:button>
                    </div>
                </form>
            </div>

            <!-- Right Column: Tabs (desktop), Below on mobile -->
            <div class="w-full lg:w-1/2">
                <flux:tab.group wire:model="activeTab" class="space-y-6">
                    <flux:tabs>
                        <flux:tab name="permissions">{{ trans_choice('admin.permissions', 2) }}</flux:tab>
                    </flux:tabs>

                    <flux:tab.panel name="permissions" class="!pt-1">
                        <!-- Permissions list with vertical scroll -->
                        <flux:checkbox.group wire:model="permissions" class="max-h-64 overflow-y-auto">
                            @foreach ($permissions as $permission)
                                <flux:checkbox value="{{ $permission['id'] }}" label="{{ $permission->description }}" />
                            @endforeach
                        </flux:checkbox.group>
                    </flux:tab.panel>
                </flux:tabs>
            </div>
        </div>
    </x-document.layout>
</section>
