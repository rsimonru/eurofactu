<?php

use App\Classes\CommercialDocuments;
use App\Models\SalesInvoice;
use App\Traits\WithSorting;
use Livewire\Component;
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
        $this->filter_name = 'sales_invoices';
        $this->getFilters();
        $this->sortByField = $this->filter['sort'] ?? 'invoice_date';
        $this->sortDirection = $this->filter['order'] ?? 'desc';
    }

    /**
     * Get filtered and sorted invoices - using Computed for caching
     */
    #[Computed]
    public function invoices()
    {
        return SalesInvoice::emtGet(
            filters: $this->getMergeFilters(),
            sort: [$this->sortByField => $this->sortDirection],
            with: ['company', 'thirdparty', 'state'],
        );
    }

    #[Computed]
    public function states()
    {
        return Select::emtGet('states', SalesInvoice::class);
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
        return SalesInvoice::emtGetSummary(
            filters: $this->getMergeFilters(),
        );
    }

    public function downloadPDFs()
    {
        if ($this->selected) {
            $invoices = SalesInvoice::emtGet(
                records_in_page: -1,
                filters: [
                    'sinvoice_ids' => $this->selected,
                ],
                with: ['company', 'thirdparty'],
            )->keyBy('number');

            $pdf = new PdfFile();
            if (length($invoices) === 1) {
                $invoice = $invoices->first();
                $pdf->zip = false;
                $pdf->file_name = $invoice->number;
                $pdf->documents = $invoice;
                $pdf->data = [
                    'company' => $invoice->company,
                    'products_summary' => $invoice->emtGetProductsSummary(),
                ];
            } else {
                $pdf->zip = true;
                $pdf->file_name = 'Factura';
                $pdf->documents = $invoices;
                $pdf->data = [
                    'company' => $invoices->first()->company,
                ];
                $pdf->documents_data = [
                    'products_summary' => CommercialDocuments::getProductsSummary($invoices, 'number'),
                ];
            }
            $data = $pdf->generateFromTemplate('pdf.sales_invoice');

            if ($pdf->zip) {
                return response()->streamDownload(
                    fn () => print($data),
                    'Facturas.zip',
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
            Flux::toast(variant: 'danger', text: __('sales.select_invoices'));
        }
    }

    public function doDelete(): void
    {
        if ($this->deleteId) {
            $invoice = SalesInvoice::emtGet($this->deleteId);
            if ($invoice) {
                $invoice->delete();
                Flux::toast(__('sales.invoice_deleted'));
                $this->dispatch('$refresh'); // Refresh computed property
            }
        }
        $this->deleteId = null;
        $this->showDeleteModal = false;
    }
}; ?>

<section class="w-full pb-15">
    <x-documents.layout
        :title="trans_choice('sales.invoices', 2)"
        :summary="$this->summary"
        :search="'filter.search'"
        :search-placeholder="__('sales.search_invoices')"
        :filter_labels="$this->filter_labels"
        :breadcrumbs="[
            ['label' => trans_choice('sales.sales', 2), 'url' => null],
            ['label' => trans_choice('sales.invoices', 2), 'url' => null],
        ]"
    >
        <x-slot:buttons>
            <flux:button type="button" size="sm" variant="primary" color="blue" icon="file-pdf" class="cursor-pointer" wire:click="downloadPDFs">
                <span class="hidden md:inline">PDF</span>
            </flux:button>
            <flux:button type="button" size="sm" variant="primary" color="blue" icon="plus" href="{{ route('sales.invoices.create') }}">
                <span class="hidden md:inline">{{ __('general.new') }}</span>
            </flux:button>
        </x-slot:buttons>
        <!-- Invoices Table -->
        <flux:checkbox.group>
            <flux:table container:class="h-[calc(100vh-22rem)] md:h-[calc(100vh-15rem)]" :paginate="$this->invoices">
                <flux:table.columns sticky class="bg-blue-50 dark:bg-zinc-900 mx-2">
                    <flux:table.column align="center">
                        <flux:checkbox.all class="pl-1 md:pl-0" />
                    </flux:table.column>
                    <flux:table.column align="center" sortable :sorted="$sortByField === 'number'" :direction="$sortDirection" wire:click="sortBy('number')">{{ __('sales.number') }}</flux:table.column>
                    <flux:table.column align="center" sortable :sorted="$sortByField === 'invoice_date'" :direction="$sortDirection" wire:click="sortBy('invoice_date')">{{ __('general.date') }}</flux:table.column>
                    <flux:table.column align="center">{{ __('sales.customer') }}</flux:table.column>
                    <flux:table.column align="center">{{ __('sales.reference') }}</flux:table.column>
                    <flux:table.column align="center" class="text-right">{{ __('sales.total') }}</flux:table.column>
                    <flux:table.column align="center">{{ __('general.state') }}</flux:table.column>
                    <flux:table.column align="center"></flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse($this->invoices as $invoice)
                        <flux:table.row wire:key="invoice-{{ $invoice->id }}">
                            <flux:table.cell>
                                <flux:checkbox wire:model="selected" value="{{ $invoice->id }}" />
                            </flux:table.cell>
                            <flux:table.cell>
                                <a href="{{ route('sales.invoices.edit', $invoice->id) }}" class="font-medium">{{ $invoice->number }}</a>
                                @if($invoice->reference)
                                    <div class="text-xs text-zinc-500">{{ $invoice->reference }}</div>
                                @endif
                            </flux:table.cell>

                            <flux:table.cell>
                                <div>
                                    {{ $invoice->created_at->format('d/m/Y') }}
                                </div>
                                @if($invoice->invoice_date)
                                        <div class="text-xs text-zinc-500">
                                            {{ __('sales.invoice_date') }}: {{ $invoice->invoice_date->format('d/m/Y') }}
                                    </div>
                                @endif
                            </flux:table.cell>

                            <flux:table.cell>
                                <div class="font-medium">{{ $invoice->thirdparty?->legal_form ?? $invoice->legal_form }}</div>
                                @if($invoice->recipient)
                                    <div class="text-xs text-zinc-500">{{ $invoice->recipient }}</div>
                                @endif
                            </flux:table.cell>

                            <flux:table.cell>
                                {{ $invoice->reference }}
                            </flux:table.cell>

                            <flux:table.cell class="text-right">
                                <div class="font-medium">{{ number_format($invoice->total_line ?? 0, 2, ',', '.') }} €</div>
                                <div class="text-xs text-zinc-500">{{ __('sales.base') }}: {{ number_format($invoice->base_line ?? 0, 2, ',', '.') }} €</div>
                            </flux:table.cell>

                            <flux:table.cell class="text-center">
                                <flux:badge :color="$invoice->state->color ?? 'zinc'" size="sm">
                                    {{ $invoice->state->description ?? __('general.unknown') }}
                                </flux:badge>
                            </flux:table.cell>

                            <flux:table.cell>
                                @if(in_array($invoice->state->id, [config('constants.states.pending'), config('constants.states.open')]))
                                <div class="flex gap-2 justify-center">
                                    <flux:button
                                        size="sm"
                                        variant="danger"
                                        icon="trash"
                                        x-on:click="$wire.showDeleteModal = true; $wire.deleteId = {{ $invoice->id }};"
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
                        <flux:heading size="lg">{{ __('sales.filter_invoices') }}</flux:heading>
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
                        <flux:select.option value="invoice_date">{{ __('sales.invoice_date') }}</flux:select.option>
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
