<?php

use Livewire\Component;
use App\Models\Product;
use App\Models\Select;
use Livewire\Attributes\Validate;
use Flux\Flux;

new class extends Component {
    public ?int $id = null;
    public bool $isEditing = false;
    public ?Product $product = null;
    public string $activeTab = 'variants';

    #[Validate('nullable|string|max:75')]
    public string $reference = '';

    #[Validate('required|string|max:200')]
    public string $description = '';

    #[Validate('required|numeric|min:0')]
    public $price = 0;

    #[Validate('required|integer|exists:tax_types,id')]
    public ?int $tax_type_id = null;

    // #[Validate('required|integer')]
    // public int $stock = 0;

    public function mount(?int $id = null): void
    {
        $this->id = $id;

        if ($this->id) {
            $this->product = Product::emtGet($id);
            $this->loadProductData();
            $this->isEditing = true;
        } else {
            $this->product = new Product();
            $this->isEditing = false;
        }
    }

    public function with(): array
    {
        return [
            'pageTitle' => $this->isEditing
                ? __('products.edit_product')
                : __('products.create_product'),
            'breadcrumbs' => [
                ['label' => trans_choice('products.products', 2), 'url' => route('products.index')],
                ['label' => $this->isEditing ? __('general.edit') : __('general.new'), 'url' => null],
            ],
            'taxTypes' => $this->getTaxTypes(),
        ];
    }

    public function loadProductData(): void
    {
        $product = Product::find($this->id);

        if (!$product) {
            $this->redirect(route('products.index'));
            return;
        }

        // dd($product);
        $this->reference = $product->reference;
        $this->description = $product->description;
        $this->price = $product->price;
        $this->tax_type_id = $product->tax_type_id;
    }

    public function save(): void
    {
        $validated = $this->validate();

        if ($this->id) {
            $this->product->update($validated);

            Flux::toast(variant: 'success', text: __('products.product_updated'));
        } else {
            $this->product = Product::create($validated + ['company_id' => session('company_id')]);

            Flux::toast(variant: 'success', text: __('products.product_created'));
            $this->redirect(route('products.edit', ['id' => $this->product->id]));
        }
    }

    public function cancel(): void
    {
        $this->redirect(route('products.index'));
    }

    public function getTaxTypes()
    {
        return Select::emtGet('tax_types_ids');
    }
}; ?>

<section class="w-full h-full">
    <x-document.layout
        :title="$pageTitle"
        :breadcrumbs="$breadcrumbs"
    >
        <x-slot:buttons>
            <flux:button type="button" size="sm" variant="filled" wire:click="cancel">
                {{ __('general.cancel') }}
            </flux:button>

            <flux:button type="button" size="sm" variant="primary" wire:click="save">
                {{ __('general.save') }}
            </flux:button>
        </x-slot:buttons>
        <div class="flex flex-col gap-6 lg:flex-row h-full">
            <!-- Left Column: Main Form -->
            <div class="w-full lg:w-1/2">
                <form class="space-y-6 px-2">
                    <div class="space-y-6">
                        <flux:field>
                            <flux:label>{{ __('products.description') }} *</flux:label>
                            <flux:textarea wire:model="description" rows="2" placeholder="{{ __('products.enter_description') }}" />
                            <flux:error name="description" />
                        </flux:field>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                            <flux:input label="{{ __('products.reference') }}"
                                wire:model="reference" size="sm" error="reference"
                                placeholder="{{ __('products.enter_reference') }}" />

                            <flux:input label="{{ __('products.price') }} *" mask:dynamic="$money($input, ',')"
                                wire:model="price" size="sm" error="price" min-value="0"
                                class:input="text-right" />

                            <flux:field>
                                <flux:label>IVA *</flux:label>
                                <flux:select wire:model="tax_type_id" variant="listbox" size="sm" searchable placeholder="{{ __('products.select_tax_type') }}">
                                    <x-slot name="search">
                                        <flux:select.search class="px-4" placeholder="{{ __('general.search') }}" />
                                    </x-slot>
                                    @foreach ($taxTypes as $taxType)
                                        <flux:select.option value="{{ $taxType['value'] }}">{{ $taxType['option'] }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                                <flux:error name="tax_type_id" />
                            </flux:field>
                        </div>
                    </div>
                </form>
            </div>
            <div class="w-full lg:w-1/2 h-full">
                <flux:tab.group class="space-y-6">
                    <flux:tabs>
                        <flux:tab name="variants-tab">{{ trans_choice('products.variants', 2) }}</flux:tab>
                    </flux:tabs>

                    <flux:tab.panel name="variants-tab" class="!pt-1">
                        <!-- Variants list with vertical scroll -->
                        <flux:checkbox.group wire:model="user_permissions" class="max-h-96 overflow-y-auto">
                            @foreach ($product->variants as $variant)
                                <flux:checkbox value="{{ $variant['id'] }}" label="{{ $variant->description }}" />
                            @endforeach
                        </flux:checkbox.group>
                    </flux:tab.panel>
                </flux:tab.group>
            </div>
        </div>
    </x-document.layout>
</section>
