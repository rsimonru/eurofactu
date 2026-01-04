<?php

use App\Models\Permission;
use App\Models\User;
use App\Models\Company;
use Flux\Flux;
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
    public bool $active = true;
    public bool $isEditing = false;
    public string $activeTab = 'permissions';
    public array $user_permissions = [];
    public array $user_companies = [];

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
            $this->active = $this->user->active;
            $this->user_permissions = $this->user->permissions()->pluck('id')->toArray();
            $this->user_companies = $this->user->companies->pluck('id')->toArray();
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
            'companies' => $this->getCompanies(),
        ];
    }
    public function getPermissions(): array
    {
        return Permission::emtGet(
            records_in_page: -1,
        )->all();
    }
    public function getCompanies(): array
    {
        return Company::emtGet(
            records_in_page: -1,
        )->all();
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
            'active' => ['boolean'],
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
            $this->user->active = $validated['active'];

            if (!empty($validated['password'])) {
                $this->user->password = Hash::make($validated['password']);
            }

            $this->user->save();
            $permissions = Permission::whereIn('id', $this->user_permissions)->pluck('name')->toArray();
            $this->user->syncPermissions($permissions);
            $this->user->companies()->sync($this->user_companies);

            Flux::toast(variant: 'success', text: __('admin.user_updated'));
        } else {
            // Create new user
            $this->user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);
            $permissions = Permission::whereIn('id', $this->user_permissions)->pluck('name')->toArray();
            $this->user->syncPermissions($permissions);

            Flux::toast(variant: 'success', text: __('admin.user_created'));
            $this->redirect(route('admin.users.edit', ['user' => $this->user->id]));
        }
    }

    /**
     * Cancel and return to users list
     */
    public function cancel(): void
    {
        $this->redirect(route('admin.users.index'), navigate: true);
    }
}; ?>

<section class="w-full h-full">
    <x-document.layout
        :title="$pageTitle"
        :breadcrumbs="$breadcrumbs"
    >
        <x-slot:buttons>
            <flux:button type="button" size="sm" variant="filled" href="{{ route('admin.users.index') }}">
                {{ __('general.cancel') }}
            </flux:button>

            <flux:button type="button" size="sm" variant="primary" wire:click="save">
                {{ __('general.save') }}
            </flux:button>
        </x-slot:buttons>
        <!-- Two-column layout: Form on left, Tabs on right (desktop), stacked on mobile -->
        <div class="flex flex-col gap-6 lg:flex-row h-full">
            <!-- Left Column: Main Form -->
            <div class="w-full lg:w-1/2">
                <form class="space-y-6 px-2">
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
                    <flux:field>
                        <flux:label>{{ __('general.active') }}</flux:label>
                        <flux:switch wire:model="active" />
                        <flux:error name="active" />
                    </flux:field>
                </form>
            </div>

            <!-- Right Column: Tabs (desktop), Below on mobile -->
            <div class="w-full lg:w-1/2 h-full">
                <flux:tab.group class="space-y-6">
                    <flux:tabs>
                        <flux:tab name="permissions-tab">{{ trans_choice('admin.permissions', 2) }}</flux:tab>
                        <flux:tab name="companies-tab">{{ trans_choice('general.companies', 2) }}</flux:tab>
                    </flux:tabs>

                    <flux:tab.panel name="permissions-tab" class="!pt-1">
                        <!-- Permissions list with vertical scroll -->
                        <flux:checkbox.group wire:model="user_permissions" class="max-h-96 overflow-y-auto">
                            @foreach ($permissions as $permission)
                                <flux:checkbox value="{{ $permission['id'] }}" label="{{ $permission->description }}" />
                            @endforeach
                        </flux:checkbox.group>
                    </flux:tab.panel>
                    <flux:tab.panel name="companies-tab" class="!pt-1">
                        <!-- Companies list with vertical scroll -->
                        <flux:checkbox.group wire:model="user_companies" class="max-h-96 overflow-y-auto">
                            @foreach ($companies as $company)
                                <flux:checkbox value="{{ $company['id'] }}" label="{{ $company->name }}" />
                            @endforeach
                        </flux:checkbox.group>
                    </flux:tab.panel>   
                </flux:tab.group>
            </div>
        </div>
    </x-document.layout>
</section>
