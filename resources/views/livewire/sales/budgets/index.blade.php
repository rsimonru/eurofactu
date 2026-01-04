<?php

use App\Models\SalesBudget;
use App\Traits\WithSorting;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use App\Classes\PdfFile;
use App\Models\Select;
use App\Models\Thirdparty;
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
        $this->filter_name = 'sales_budgets';
        $this->getFilters();
        $this->sortByField = $this->filter['sort'] ?? 'date';
        $this->sortDirection = $this->filter['order'] ?? 'desc';
    }

    /**
     * Get filtered and sorted budgets - using Computed for caching
     */
    #[Computed]
    public function budgets()
    {
        return SalesBudget::emtGet(
            filters: $this->getMergeFilters(),
            sort: [$this->sortByField => $this->sortDirection],
            with: ['company', 'thirdparty', 'state'],
        );
    }

    #[Computed]
    public function states()
    {
        return Select::emtGet('states', SalesBudget::class);
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
        return SalesBudget::emtGetSummary(
            filters: $this->getMergeFilters(),
        );
    }

    public function downloadPDFs()
    {
        if ($this->selected) {
            $budgets = SalesBudget::emtGet(
                records_in_page: -1,
                filters: [
                    'budget_ids' => $this->selected,
                ],
                with: ['company', 'thirdparty'],
            )->keyBy('number');

            $pdf = new PdfFile();
            if (length($budgets) === 1) {
                $pdf->zip = false;
                $pdf->file_name = $budgets->first()->number;
                $pdf->documents = $budgets->first();
            } else {
                $pdf->zip = true;
                $pdf->file_name = 'Presupuesto';
                $pdf->documents = $budgets;
            }
            $pdf->data = [
                'company' => $budgets->first()->company,
            ];
            $data = $pdf->generateFromTemplate('pdf.budget');

            if ($pdf->zip) {
                return response()->streamDownload(
                    fn () => print($data),
                    'Presupuestos.zip',
                    ['Content-Type' => 'application/zip']
                );
            } else {
                return response()->streamDownload(
                    fn () => print($data),
                    $pdf->file_name.'.pdf',
                    ['Content-Type' => 'application/pdf']
                );
            }
        } else {
            Flux::toast(variant: 'danger', text: __('sales.select_budgets'));
        }
    }

    public function doDelete(): void
    {
        if ($this->deleteId) {
            $budget = SalesBudget::emtGet($this->deleteId);
            if ($budget) {
                $budget->delete();
                Flux::toast(__('sales.budget_deleted'));
                $this->dispatch('$refresh'); // Refresh computed property
            }
        }
        $this->deleteId = null;
        $this->showDeleteModal = false;
    }
}; ?>

<section class="w-full pb-15">
    <x-documents.layout
        :title="trans_choice('sales.budgets', 2)"
        :summary="$this->summary"
        :search="'filter.search'"
        :search-placeholder="__('sales.search_budgets')"
        :filter_labels="$this->filter_labels"
        :breadcrumbs="[
            ['label' => trans_choice('sales.sales', 2), 'url' => null],
            ['label' => trans_choice('sales.budgets', 2), 'url' => null],
        ]"
    >
        <x-slot:buttons>
            <flux:button type="button" size="sm" variant="primary" color="blue" icon="file-pdf" class="cursor-pointer" wire:click="downloadPDFs">
                <span class="hidden md:inline">PDF</span>
            </flux:button>
            <flux:button type="button" size="sm" variant="primary" color="blue" icon="plus" href="{{ route('sales.budgets.create') }}">
                <span class="hidden md:inline">{{ __('general.new') }}</span>
            </flux:button>
        </x-slot:buttons>
        <!-- Budgets Table -->
        <flux:checkbox.group>
            <flux:table container:class="h-[calc(100vh-22rem)] md:h-[calc(100vh-15rem)]" :paginate="$this->budgets">
                <flux:table.columns sticky class="bg-white dark:bg-zinc-900 mx-2">
                    <flux:table.column align="center">
                        <flux:checkbox.all />
                    </flux:table.column>
                    <flux:table.column align="center" sortable :sorted="$sortByField === 'number'" :direction="$sortDirection" wire:click="sortBy('number')">{{ __('sales.number') }}</flux:table.column>
                    <flux:table.column align="center" sortable :sorted="$sortByField === 'date'" :direction="$sortDirection" wire:click="sortBy('date')">{{ __('general.date') }}</flux:table.column>
                    <flux:table.column align="center">{{ __('sales.customer') }}</flux:table.column>
                    <flux:table.column align="center">{{ __('sales.reference') }}</flux:table.column>
                    <flux:table.column align="center" class="text-right">{{ __('sales.total') }}</flux:table.column>
                    <flux:table.column align="center">{{ __('general.state') }}</flux:table.column>
                    <flux:table.column align="center"></flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse($this->budgets as $budget)
                        <flux:table.row wire:key="budget-{{ $budget->id }}">
                            <flux:table.cell>
                                <flux:checkbox wire:model="selected" value="{{ $budget->id }}" />
                            </flux:table.cell>
                            <flux:table.cell>
                                <a href="{{ route('sales.budgets.edit', $budget->id) }}" class="font-medium">{{ $budget->number }}</a>
                                @if($budget->reference)
                                    <div class="text-xs text-zinc-500">{{ $budget->reference }}</div>
                                @endif
                            </flux:table.cell>

                            <flux:table.cell>
                                <div>
                                    {{ $budget->date->format('d/m/Y') }}
                                </div>
                                @if($budget->valid_until)
                                    <div class="text-xs text-zinc-500">
                                        {{ __('sales.valid_until') }}: {{ $budget->valid_until->format('d/m/Y') }}
                                    </div>
                                @endif
                            </flux:table.cell>

                            <flux:table.cell>
                                <div class="font-medium">{{ $budget->thirdparty?->legal_form ?? $budget->legal_form }}</div>
                                @if($budget->recipient)
                                    <div class="text-xs text-zinc-500">{{ $budget->recipient }}</div>
                                @endif
                            </flux:table.cell>

                            <flux:table.cell>
                                {{ $budget->reference }}
                            </flux:table.cell>

                            <flux:table.cell class="text-right">
                                <div class="font-medium">{{ number_format($budget->total_line ?? 0, 2, ',', '.') }} €</div>
                                <div class="text-xs text-zinc-500">{{ __('sales.base') }}: {{ number_format($budget->base_line ?? 0, 2, ',', '.') }} €</div>
                            </flux:table.cell>

                            <flux:table.cell class="text-center">
                                <flux:badge :color="$budget->state->color ?? 'zinc'" size="sm">
                                    {{ $budget->state->description ?? __('general.unknown') }}
                                </flux:badge>
                            </flux:table.cell>

                            <flux:table.cell>
                                @if($budget->state->id == config('constants.states.pending'))
                                <div class="flex gap-2 justify-center">
                                    <flux:button
                                        size="sm"
                                        variant="danger"
                                        icon="trash"
                                        x-on:click="$wire.showDeleteModal = true; $wire.deleteId = {{ $budget->id }};"
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
                        <flux:heading size="lg">{{ __('sales.filter_budgets') }}</flux:heading>
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
                        <flux:select.option value="date">Fecha documento</flux:select.option>
                        <flux:select.option value="sent_date">Enviada</flux:select.option>
                        <flux:select.option value="valid_until">Vencimiento</flux:select.option>
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
