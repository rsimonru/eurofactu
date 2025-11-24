<?php

use App\Models\SalesBudget;
use App\Traits\WithSorting;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;

new class extends Component {
    use WithPagination;
    use WithSorting;

    public string $search = '';

    public function mount() {
        $this->sortByField = 'date';
        $this->sortDirection = 'desc';
    }

    /**
     * Get filtered and sorted budgets - using Computed for caching
     */
    #[Computed]
    public function budgets()
    {
        return SalesBudget::emtGet(
            records_in_page: 10,
            filters: [
                'search' => $this->search,
            ],
            sort: [$this->sortByField => $this->sortDirection],
            with: ['company', 'thirdparty', 'state'],
        );
    }

    /**
     * Get summary totals - using Computed for caching
     */
    #[Computed]
    public function summary()
    {
        return SalesBudget::emtGetSummary(
            filters: [
                'search' => $this->search,
            ]
        );
    }

    /**
     * Reset search
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }
}; ?>

<section class="w-full pb-15">
    <x-documents.layout
        :title="trans_choice('sales.budgets', 2)"
        :summary="$this->summary"
        :search="'search'"
        :search-placeholder="__('sales.search_budgets')"
        :breadcrumbs="[
            ['label' => trans_choice('sales.sales', 2), 'url' => null],
            ['label' => trans_choice('sales.budgets', 2), 'url' => null],
        ]"
    >
        <x-slot:buttons>
            <flux:button type="button" size="sm" variant="primary" color="blue" icon="file-plus" href="{{ route('sales.budgets.create') }}">
                {{ __('general.new') }}
            </flux:button>
        </x-slot:buttons>
        <!-- Budgets Table -->
        <flux:table container:class="h-[calc(100vh-24rem)] lg:h-[calc(100vh-15rem)]" :paginate="$this->budgets">
            <flux:table.columns sticky class="bg-white dark:bg-zinc-900 mx-2">
                <flux:table.column align="center" sortable :sorted="$sortByField === 'number'" :direction="$sortDirection" wire:click="sortBy('number')">{{ __('sales.number') }}</flux:table.column>
                <flux:table.column align="center" sortable :sorted="$sortByField === 'date'" :direction="$sortDirection" wire:click="sortBy('date')">{{ __('general.date') }}</flux:table.column>
                <flux:table.column align="center">{{ __('sales.customer') }}</flux:table.column>
                <flux:table.column align="center">{{ __('sales.company') }}</flux:table.column>
                <flux:table.column align="center" class="text-right">{{ __('sales.total') }}</flux:table.column>
                <flux:table.column align="center">{{ __('general.state') }}</flux:table.column>
                <flux:table.column align="center">{{ __('general.actions') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($this->budgets as $budget)
                    <flux:table.row wire:key="budget-{{ $budget->id }}">
                        <flux:table.cell>
                            <div class="font-medium">{{ $budget->number }}</div>
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
                            {{ $budget->company?->legal_form }}
                        </flux:table.cell>

                        <flux:table.cell class="text-right">
                            <div class="font-medium">{{ number_format($budget->total_line ?? 0, 2, ',', '.') }} €</div>
                            <div class="text-xs text-zinc-500">{{ __('sales.base') }}: {{ number_format($budget->base_line ?? 0, 2, ',', '.') }} €</div>
                        </flux:table.cell>

                        <flux:table.cell>
                            <flux:badge :color="$budget->state?->color ?? 'zinc'" size="sm">
                                {{ $budget->state?->description ?? __('general.unknown') }}
                            </flux:badge>
                        </flux:table.cell>

                        <flux:table.cell>
                            <div class="flex gap-2">
                                <flux:button
                                    size="sm"
                                    variant="filled"
                                    icon="pencil"
                                    :href="route('sales.budgets.edit', $budget->id)"
                                    wire:navigate
                                />
                                <flux:button
                                    size="sm"
                                    variant="danger"
                                    icon="trash"
                                />
                            </div>
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
        
    </x-documents.layout>
</section>
