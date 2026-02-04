<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Traits\WithSorting;
use App\Models\Product;
use App\Traits\WithFilters;
use Flux\Flux;
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
        $this->filter_name = 'products';
        $this->getFilters();
        $this->sortByField = $this->filter['sort'] ?? 'description';
        $this->sortDirection = $this->filter['order'] ?? 'asc';
    }

    /**
     * Get filtered and sorted products - using Computed for caching
     */
    #[Computed]
    public function products()
    {
        return Product::emtGet(
            filters: $this->getMergeFilters(),
            sort: [$this->sortByField => $this->sortDirection],
            with: ['tax_type']
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
            $product = Product::find($this->deleteId);
            if ($product) {
                $product->delete();
                Flux::toast(__('products.product_deleted'));
                $this->dispatch('$refresh'); // Refresh computed property
            }
        }
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }
}; ?>

<section class="w-full">
    <x-documents.layout
        :title="trans_choice('products.products', 2)"
        :search="'filter.search'"
        :search-placeholder="__('products.search_products')"
        :filter_labels="$this->filter_labels"
        :breadcrumbs="[
            ['label' => trans_choice('products.products', 2), 'url' => null],
        ]"
    >
        <x-slot:buttons>
            <flux:button type="button" size="sm" variant="primary" color="blue" icon="plus" href="{{ route('products.create') }}">
                {{ __('general.new') }}
            </flux:button>
        </x-slot:buttons>

        <flux:checkbox.group>
            <flux:table container:class="max-h-96" :paginate="$this->products">
                <flux:table.columns sticky class="bg-blue-50 dark:bg-zinc-900 mx-2">
                    <flux:table.column align="center">
                        <flux:checkbox.all />
                    </flux:table.column>
                    <flux:table.column align="center" sortable :sorted="$sortByField === 'description'" :direction="$sortDirection" wire:click="sortBy('description')">
                        {{ __('products.description') }}
                    </flux:table.column>
                    <flux:table.column align="center" sortable :sorted="$sortByField === 'reference'" :direction="$sortDirection" wire:click="sortBy('reference')">
                        {{ __('products.reference') }}
                    </flux:table.column>
                    <flux:table.column align="center" sortable :sorted="$sortByField === 'price'" :direction="$sortDirection" wire:click="sortBy('price')">
                        {{ __('products.price') }}
                    </flux:table.column>
                    <flux:table.column align="center" sortable :sorted="$sortByField === 'taxType'" :direction="$sortDirection" wire:click="sortBy('taxType')">
                        IVA
                    </flux:table.column>
                    <flux:table.column align="center"></flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($this->products as $product)
                        <flux:table.row wire:key="product-{{ $product->id }}">
                            <flux:table.cell class="text-center">
                                <flux:checkbox wire:model="selected" value="{{ $product->id }}" />
                            </flux:table.cell>

                            <flux:table.cell class="truncate">
                                <a href="{{ route('products.edit', $product->id) }}" class="font-medium">{{ $product->description }}</a>
                            </flux:table.cell>

                            <flux:table.cell class="font-semibold">
                                {{ $product->reference }}
                            </flux:table.cell>

                            <flux:table.cell class="text-center">
                                {{ number_format($product->price, 2) }} €
                            </flux:table.cell>
                            <flux:table.cell class="text-center">
                                {{ $product->tax_type->type }}
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex gap-2 justify-center">
                                    <flux:button
                                        size="sm"
                                        variant="danger"
                                        icon="trash"
                                        x-on:click="$wire.showDeleteModal = true; $wire.deleteId={{ $product->id }};"
                                        tooltip="{{ __('general.delete') }}"
                                    />
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5" class="text-center py-8">
                                <div class="flex flex-col items-center gap-2">
                                    <flux:icon.archive-box class="w-12 h-12 text-gray-400" />
                                    <p class="text-gray-500">{{ __('general.no_records_found') }}</p>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </flux:checkbox.group>
        <x-slot:modals>
            <x-delete-modal wire:model="showDeleteModal" />
        </x-slot:modals>
    </x-documents.layout>
</section>
