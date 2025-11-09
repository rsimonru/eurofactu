<?php

use App\Models\User;
use App\Traits\WithSorting;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;
    use WithSorting;

    public string $search = '';

    public function mount() {
        $this->sortByField = 'name';
        $this->sortDirection = 'asc';
    }

    /**
     * Get the users list
     */
    public function with(): array
    {
        return [
            'users' => $this->getUsers(),
        ];
    }

    /**
     * Get filtered and sorted users
     */
    public function getUsers()
    {
        $users = User::emtGet(
            records_in_page: 10,
            filters: [
                'search' => $this->search,
            ],
            sort: [$this->sortByField => $this->sortDirection],
            with: ['level'],
        );
        return $users;
    }

    /**
     * Reset search
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }
}; ?>

<section class="w-full">
    <x-documents.layout
        :title="trans_choice('admin.users', 2)"
        :records="$users"
        :search="'search'"
        :search-placeholder="__('admin.search_users')"
        :breadcrumbs="[
            ['label' => __('admin.admin'), 'url' => null],
            ['label' => trans_choice('admin.users', 2), 'url' => null],
        ]"
        :buttons="[
            ['label' => __('admin.new'), 'url' => route('admin.users.create'), 'icon' => 'user-round-plus', 'color' => 'blue', 'size' => 'sm'],
        ]"
    >
        <!-- Users Table (Flux component) -->
        <flux:table container:class="max-h-80" :paginate="$users">
            <flux:table.columns sticky >
                <flux:table.column sortable :sorted="$sortByField === 'name'" :direction="$sortDirection" wire:click="sortBy('name')">{{ __('general.name') }}</flux:table.column>
                <flux:table.column sortable :sorted="$sortByField === 'email'" :direction="$sortDirection" wire:click="sortBy('email')">{{ __('general.email') }}</flux:table.column>
                <flux:table.column sortable :sorted="$sortByField === 'created_at'" :direction="$sortDirection" wire:click="sortBy('created_at')">{{ __('general.created_at') }}</flux:table.column>
                <flux:table.column>{{ __('general.actions') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($users as $user)
                    <flux:table.row :key="$user->id">
                        <flux:table.cell>
                            <div class="flex items-center gap-3">
                                <flux:avatar
                                    size="sm"
                                    :src="'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random'"
                                />
                                <div>
                                    <div class="font-medium">{{ $user->name }}</div>
                                    @if($user->id === auth()->id())
                                        <flux:badge size="sm" color="blue">{{ __('general.you') }}</flux:badge>
                                    @endif
                                </div>
                            </div>
                        </flux:table.cell>

                        <flux:table.cell>{{ $user->email }}</flux:table.cell>

                        <flux:table.cell>
                            <flux:tooltip :content="$user->created_at->format('Y-m-d H:i:s')">
                                {{ $user->created_at->diffForHumans() }}
                            </flux:tooltip>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-2">
                                <flux:button size="sm" variant="filled" icon="pencil" :href="route('admin.users.edit', $user->id)" wire:navigate />

                                @if($user->id !== auth()->id())
                                    <flux:button size="sm" variant="danger" icon="trash" />
                                @endif
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="4">
                            <div class="flex flex-col items-center gap-2 py-8">
                                <flux:icon.users class="size-12" variant="outline" />
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ __('general.no_records_found') }}
                                </p>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </x-documents.layout>
</section>
