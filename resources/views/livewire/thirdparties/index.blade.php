<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Traits\WithSorting;
use App\Models\Thirdparty;
use Flux\Flux;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;

new class extends Component {
    use WithPagination;
    use WithSorting;

    public string $search = '';
    public $showConfirmModal = false;
    public $deleteId = null;

    public function mount() {
        $this->sortByField = 'legal_form';
        $this->sortDirection = 'desc';
    }

    /**
     * Get filtered and sorted thirdparties - using Computed for caching
     */
    #[Computed]
    public function thirdparties()
    {
        $filters = [
            'search' => $this->search,
        ];

        $sort = [
            $this->sortByField => $this->sortDirection,
        ];

        return Thirdparty::emtGet(
            model_id: 0,
            records_in_page: 10,
            sort: $sort,
            filters: $filters,
            with: ['country']
        );
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
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
        $this->showConfirmModal = false;
        $this->deleteId = null;
    }
}; ?>

<section class="w-full">
    <x-documents.layout
        :title="trans_choice('thirdparties.thirdparties', 2)"
        :search="'search'"
        :search-placeholder="__('thirdparties.search_thirdparties')"
        :breadcrumbs="[
            ['label' => trans_choice('thirdparties.thirdparties', 2), 'url' => null],
        ]"
    >
        <x-slot:buttons>
            <flux:button type="button" size="sm" variant="primary" color="blue" icon="file-plus" href="{{ route('thirdparties.create') }}">
                {{ __('general.new') }}
            </flux:button>
        </x-slot:buttons>

        <flux:table container:class="max-h-96" :paginate="$this->thirdparties">
            <flux:table.columns  sticky>
                <flux:table.column sortable :sorted="$sortByField === 'legal_form'" :direction="$sortDirection" wire:click="sortBy('legal_form')">
                    {{ __('thirdparties.legal_form') }}
                </flux:table.column>
                <flux:table.column sortable :sorted="$sortByField === 'vat'" :direction="$sortDirection" wire:click="sortBy('vat')">
                    {{ __('thirdparties.vat') }}
                </flux:table.column>
                <flux:table.column>{{ __('thirdparties.contact') }}</flux:table.column>
                <flux:table.column>{{ __('thirdparties.email') }}</flux:table.column>
                <flux:table.column>{{ __('thirdparties.town') }}</flux:table.column>
                <flux:table.column>{{ __('thirdparties.type') }}</flux:table.column>
                <flux:table.column class="w-20"></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->thirdparties as $thirdparty)
                    <flux:table.row wire:key="thirdparty-{{ $thirdparty->id }}">
                        <flux:table.cell class="font-semibold">
                            {{ $thirdparty->legal_form }}
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
                            <div class="flex gap-1">
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

                        <flux:table.cell class="flex gap-2 justify-end">
                            <flux:button
                                size="sm"
                                variant="ghost"
                                icon="pencil"
                                href="{{ route('thirdparties.edit', $thirdparty->id) }}"
                                tooltip="{{ __('general.edit') }}"
                            />

                            <flux:button
                                size="sm"
                                variant="danger"
                                icon="trash"
                                x-on:click="$wire.showConfirmModal = true; $wire.deleteId = {{ $thirdparty->id }};"
                                tooltip="{{ __('general.delete') }}"
                            />
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

        <flux:modal class="md:w-96" wire:model.self="showConfirmModal">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">{{ __('general.delete') }}</flux:heading>
                    <flux:text class="mt-2">{{ __('general.confirm_delete') }}</flux:text>
                </div>
                <div class="flex gap-2">
                    <flux:spacer />
                    <flux:modal.close>
                        <flux:button variant="ghost">{{ __('general.cancel') }}</flux:button>
                    </flux:modal.close>
                    <flux:button type="button" variant="danger" wire:click="delete">{{ __('general.delete') }}</flux:button>
                </div>
            </div>
        </flux:modal>
    </x-documents.layout>
</section>
