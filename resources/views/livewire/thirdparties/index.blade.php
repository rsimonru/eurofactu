<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Traits\WithSorting;
use App\Models\Thirdparty;
use App\Traits\WithFilters;
use Flux\Flux;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;

new class extends Component {
    use WithPagination;
    use WithSorting;
    use WithFilters;

    public string $search = '';
    public array $selected = [];
    public $showDeleteModal = false;
    public $deleteId = null;

    public function mount() {
        $this->filter_name = 'thirdparties';
        $this->getFilters();
        $this->sortByField = $this->filter['sort'] ?? 'legal_form';
        $this->sortDirection = $this->filter['order'] ?? 'asc';
    }

    /**
     * Get filtered and sorted thirdparties - using Computed for caching
     */
    #[Computed]
    public function thirdparties()
    {
        return Thirdparty::emtGet(
            filters: $this->getMergeFilters(),
            sort: [$this->sortByField => $this->sortDirection],
            with: ['country']
        );
    }

    public function with()
    {
        $this->getFilters();
        return [
            'filter' => $this->filter,
        ];
    }

    public function delete(): void
    {
        if ($this->deleteId) {
            $thirdparty = Thirdparty::find($this->deleteId);
            if ($thirdparty) {
                $thirdparty->delete();
                Flux::toast(__('thirdparties.thirdparty_deleted'));
                $this->dispatch('$refresh'); // Refresh computed property
            }
        }
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }
}; ?>

<section class="w-full">
    <x-documents.layout
        :title="trans_choice('thirdparties.thirdparties', 2)"
        :search="'filter.search'"
        :search-placeholder="__('thirdparties.search_thirdparties')"
        :filter_labels="$this->filter_labels"
        :breadcrumbs="[
            ['label' => trans_choice('thirdparties.thirdparties', 2), 'url' => null],
        ]"
    >
        <x-slot:buttons>
            <flux:button type="button" size="sm" variant="primary" color="blue" icon="file-plus" href="{{ route('thirdparties.create') }}">
                {{ __('general.new') }}
            </flux:button>
        </x-slot:buttons>

        <flux:checkbox.group>
            <flux:table container:class="max-h-96" :paginate="$this->thirdparties">
                <flux:table.columns sticky class="bg-blue-50 dark:bg-zinc-900 mx-2">
                    <flux:table.column align="center">
                        <flux:checkbox.all />
                    </flux:table.column>
                    <flux:table.column align="center" sortable :sorted="$sortByField === 'legal_form'" :direction="$sortDirection" wire:click="sortBy('legal_form')">
                        {{ __('thirdparties.legal_form') }}
                    </flux:table.column>
                    <flux:table.column align="center" sortable :sorted="$sortByField === 'vat'" :direction="$sortDirection" wire:click="sortBy('vat')">
                        {{ __('thirdparties.vat') }}
                    </flux:table.column>
                    <flux:table.column align="center">{{ __('thirdparties.contact') }}</flux:table.column>
                    <flux:table.column align="center">{{ __('thirdparties.email') }}</flux:table.column>
                    <flux:table.column align="center">{{ __('thirdparties.town') }}</flux:table.column>
                    <flux:table.column align="center">{{ __('thirdparties.type') }}</flux:table.column>
                    <flux:table.column align="center"></flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($this->thirdparties as $thirdparty)
                        <flux:table.row wire:key="thirdparty-{{ $thirdparty->id }}">
                            <flux:table.cell class="text-center">
                                <flux:checkbox wire:model="selected" value="{{ $thirdparty->id }}" />
                            </flux:table.cell>
                            <flux:table.cell class="font-semibold">
                                <a href="{{ route('thirdparties.edit', $thirdparty->id) }}" class="font-medium">{{ $thirdparty->legal_form }}</a>
                            </flux:table.cell>

                            <flux:table.cell>
                                {{ $thirdparty->vat }}
                            </flux:table.cell>

                            <flux:table.cell>
                                {{ $thirdparty->contact }}
                            </flux:table.cell>

                            <flux:table.cell>
                                {{ $thirdparty->email }}
                            </flux:table.cell>

                            <flux:table.cell>
                                {{ $thirdparty->town }}
                            </flux:table.cell>

                            <flux:table.cell>
                                <div class="flex gap-2 justify-center">
                                    @if($thirdparty->is_customer)
                                        <flux:badge size="sm" color="blue" inset="top bottom">
                                            {{ __('thirdparties.customer') }}
                                        </flux:badge>
                                    @endif
                                    @if($thirdparty->is_supplier)
                                        <flux:badge size="sm" color="green" inset="top bottom">
                                            {{ __('thirdparties.supplier') }}
                                        </flux:badge>
                                    @endif
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>
                                <div class="flex gap-2 justify-center">
                                    <flux:button
                                        size="sm"
                                        variant="danger"
                                        icon="trash"
                                        x-on:click="$wire.showDeleteModal = true; $wire.deleteId = {{ $thirdparty->id }};"
                                        tooltip="{{ __('general.delete') }}"
                                    />
                                </div>
                            </flux:table.cell>
                        </flux:row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="8" class="text-center py-8">
                                <div class="flex flex-col items-center gap-2">
                                    <flux:icon.building-office-2 class="w-12 h-12 text-gray-400" />
                                    <p class="text-gray-500">{{ __('general.no_records_found') }}</p>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </flux:checkbox.group>
        <x-slot:modals>
            <flux:modal name="filter-records" flyout>
                <div class="space-y-6">
                    <div>
                        <flux:heading size="lg">{{ __('thirdparties.filter_thirdparties') }}</flux:heading>
                    </div>

                    <flux:switch size="sm" wire:model="filter.is_customer" label="{{ __('thirdparties.is_customer') }}" />
                    <flux:switch size="sm" wire:model="filter.is_supplier" label="{{ __('thirdparties.is_supplier') }}" />  
                    
                    <div class="flex gap-2">
                        <flux:spacer />
                        <flux:modal.close>
                            <flux:button size="sm" variant="ghost" wire:click="deleteFilter">{{ __('general.delete_filter') }}</flux:button>
                        </flux:modal.close>
                        <flux:button size="sm" variant="primary" wire:click="searchRecords(true)">
                            {{ __('general.filter') }}
                        </flux:button>
                    </div>
                </div>
            </flux:modal>
            <x-delete-modal wire:model="showDeleteModal" />
        </x-slot:modals>
    </x-documents.layout>
</section>
