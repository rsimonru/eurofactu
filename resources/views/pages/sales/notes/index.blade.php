<?php

use App\Models\SalesNote;
use App\Traits\WithSorting;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use App\Models\Select;
use Flux\Flux;
use App\Traits\WithFilters;

new class extends Component {
    use WithPagination;
    use WithSorting;
    use WithFilters;

    public array $selected = [];
    public bool $filterOpen = false;
    public ?int $deleteId = null;
    public bool $showDeleteModal = false;
    public string $thirdparty_search = '';

    public function mount() {
        $this->filter_name = 'sales_notes';
        $this->getFilters();
        $this->sortByField = $this->filter['sort'] ?? 'customer_date';
        $this->sortDirection = $this->filter['order'] ?? 'desc';
    }

    /**
     * Get filtered and sorted notes - using Computed for caching
     */
    #[Computed]
    public function notes()
    {
        return SalesNote::emtGet(
            filters: $this->getMergeFilters(),
            sort: [$this->sortByField => $this->sortDirection],
            with: ['company', 'thirdparty', 'state'],
        );
    }

    #[Computed]
    public function states()
    {
        return Select::emtGet('states', SalesNote::class);
    }

    #[Computed]
    public function thirdparties()
    {
        return Select::emtGet(
            vcSelect: 'thirdparties',
            parameter1: 1,
            search: $this->thirdparty_search,
            selected: $this->filter['thirdparty_id'] ?? null
        );
    }

    public function with()
    {
        $this->getFilters();
        return [
            'filter' => $this->filter,
        ];
    }

    /**
     * Get summary totals - using Computed for caching
     */
    #[Computed]
    public function summary()
    {
        return SalesNote::emtGetSummary(
            filters: $this->getMergeFilters(),
        );
    }

    public function doDelete(): void
    {
        if ($this->deleteId) {
            $note = SalesNote::emtGet($this->deleteId);
            if ($note) {
                $note->delete();
                Flux::toast(__('general.deleted'));
                $this->dispatch('$refresh'); // Refresh computed property
            }
        }
        $this->deleteId = null;
        $this->showDeleteModal = false;
    }
}; ?>

<section class="w-full pb-15">
    <x-documents.layout
        :title="trans_choice('sales.notes', 2)"
        :summary="$this->summary"
        :search="'filter.search'"
        :search-placeholder="__('sales.search_orders')"
        :filter_labels="$this->filter_labels"
        :breadcrumbs="[
            ['label' => trans_choice('sales.sales', 2), 'url' => null],
            ['label' => trans_choice('sales.notes', 2), 'url' => null],
        ]"
    >
        <x-slot:buttons>
            <flux:button type="button" size="sm" variant="primary" color="blue" icon="plus" href="{{ route('sales.notes.create') }}">
                <span class="hidden md:inline">{{ __('general.new') }}</span>
            </flux:button>
        </x-slot:buttons>
        <!-- Notes Table -->
        <flux:checkbox.group>
            <flux:table container:class="h-[calc(100vh-22rem)] md:h-[calc(100vh-15rem)]" :paginate="$this->notes">
                <flux:table.columns sticky class="bg-blue-50 dark:bg-zinc-900 mx-2">
                    <flux:table.column align="center">
                        <flux:checkbox.all class="pl-1 md:pl-0" />
                    </flux:table.column>
                    <flux:table.column align="center" sortable :sorted="$sortByField === 'number'" :direction="$sortDirection" wire:click="sortBy('number')">{{ __('sales.number') }}</flux:table.column>
                    <flux:table.column align="center" sortable :sorted="$sortByField === 'customer_date'" :direction="$sortDirection" wire:click="sortBy('customer_date')">{{ __('general.date') }}</flux:table.column>
                    <flux:table.column align="center">{{ __('sales.customer') }}</flux:table.column>
                    <flux:table.column align="center">{{ __('sales.reference') }}</flux:table.column>
                    <flux:table.column align="center" class="text-right">{{ __('sales.total') }}</flux:table.column>
                    <flux:table.column align="center">{{ __('general.state') }}</flux:table.column>
                    <flux:table.column align="center"></flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse($this->notes as $note)
                        <flux:table.row wire:key="note-{{ $note->id }}">
                            <flux:table.cell>
                                <flux:checkbox wire:model="selected" value="{{ $note->id }}" />
                            </flux:table.cell>
                            <flux:table.cell>
                                <a href="{{ route('sales.notes.edit', $note->id) }}" class="font-medium">{{ $note->number }}</a>
                                @if($note->reference)
                                    <div class="text-xs text-zinc-500">{{ $note->reference }}</div>
                                @endif
                            </flux:table.cell>

                            <flux:table.cell>
                                <div>
                                    {{ $note->created_at->format('d/m/Y') }}
                                </div>
                                @if($note->customer_date)
                                        <div class="text-xs text-zinc-500">
                                            {{ __('sales.customer_date') }}: {{ $note->customer_date->format('d/m/Y') }}
                                    </div>
                                @endif
                            </flux:table.cell>

                            <flux:table.cell>
                                <div class="font-medium">{{ $note->thirdparty?->legal_form ?? $note->legal_form }}</div>
                                @if($note->recipient)
                                    <div class="text-xs text-zinc-500">{{ $note->recipient }}</div>
                                @endif
                            </flux:table.cell>

                            <flux:table.cell>
                                {{ $note->reference }}
                            </flux:table.cell>

                            <flux:table.cell class="text-right">
                                <div class="font-medium">{{ number_format($note->total_line ?? 0, 2, ',', '.') }} €</div>
                                <div class="text-xs text-zinc-500">{{ __('sales.base') }}: {{ number_format($note->base_line ?? 0, 2, ',', '.') }} €</div>
                            </flux:table.cell>

                            <flux:table.cell class="text-center">
                                <flux:badge :color="$note->state->color ?? 'zinc'" size="sm">
                                    {{ $note->state->description ?? __('general.unknown') }}
                                </flux:badge>
                            </flux:table.cell>

                            <flux:table.cell>
                                @if(in_array($note->state->id, [config('constants.states.pending'), config('constants.states.open')]))
                                <div class="flex gap-2 justify-center">
                                    <flux:button
                                        size="sm"
                                        variant="danger"
                                        icon="trash"
                                        x-on:click="$wire.showDeleteModal = true; $wire.deleteId = {{ $note->id }};"
                                        tooltip="{{ __('general.delete') }}"
                                    />
                                </div>
                                @endif
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="7">
                                <div class="flex flex-col items-center gap-2 py-8">
                                    <flux:icon.file-text class="size-12" variant="outline" />
                                    <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                        {{ __('general.no_records_found') }}
                                    </p>
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
                        <flux:heading size="lg">{{ __('sales.filter_orders') }}</flux:heading>
                    </div>

                    <flux:pillbox size="sm" wire:model="filter.thirdparty_id" variant="combobox" multiple :filter="false"
                        label="{{ __('sales.customer') }}"
                        placeholder="{{ __('general.select') }}">
                        <x-slot name="input">
                            <flux:pillbox.input wire:model.live="thirdparty_search" placeholder="{{ __('general.search') }}" />
                        </x-slot>
                        @foreach ($this->thirdparties as $thirdparty)
                            <flux:pillbox.option :value="$thirdparty['value']">{{ $thirdparty['option'] }}</flux:pillbox.option>
                        @endforeach
                    </flux:pillbox>

                    <flux:select size="sm" wire:model="filter.state_id" variant="listbox" clearable multiple
                        label="{{ __('general.state') }}"
                        selected-suffix="{{ __('general.selected') }}"
                        searchable placeholder="{{ __('general.select') }}">
                        <x-slot name="search">
                            <flux:select.search class="px-4" placeholder="{{ __('general.search') }}" />
                        </x-slot>
                        @foreach ($this->states as $state)
                            <flux:select.option value="{{ $state['value'] }}">{{ $state['option'] }}</flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:select size="sm" wire:model="filter.date.0" variant="listbox" clearable
                        label="{{ __('general.date') }}"
                        placeholder="{{ __('general.select') }}">
                        <flux:select.option value="created_at">{{ __('general.created_at') }}</flux:select.option>
                        <flux:select.option value="customer_date">{{ __('sales.customer_date') }}</flux:select.option>
                    </flux:select>
                    <div class="grid grid-cols-2 gap-2">
                        <flux:date-picker size="sm" start-day="1" selectable-header wire:model="filter.date.1" placeholder="{{ __('general.select') }}" clearable />
                        <flux:date-picker size="sm" start-day="1" selectable-header wire:model="filter.date.2" placeholder="{{ __('general.select') }}" clearable />
                    </div>
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
