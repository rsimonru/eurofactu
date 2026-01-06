<?php

use Livewire\Volt\Component;
use App\Models\Thirdparty;
use App\Models\Select;
use Livewire\Attributes\Validate;
use Flux\Flux;

new class extends Component {
    public ?int $id = null;
    public bool $isEditing = false;
    public ?Thirdparty $thirdparty = null;

    // Main fields
    #[Validate('required|string|max:75')]
    public string $legal_form;
    #[Validate('required|string|max:45')]
    public string $vat = '';
    #[Validate('required|string|max:100')]
    public string $contact = '';
    #[Validate('required|string|max:150')]
    public string $address = '';
    #[Validate('nullable|string|max:75')]
    public string $town = '';
    #[Validate('nullable|string|max:45')]
    public string $province = '';
    #[Validate('nullable|string|max:15')]
    public string $zip = '';
    #[Validate('required|integer|exists:countries,id')]
    public ?int $country_id = null;
    #[Validate('nullable|string|max:25')]
    public string $phone = '';
    #[Validate('nullable|string|max:25')]
    public string $mobile = '';
    #[Validate('nullable|email|max:200')]
    public string $email = '';
    #[Validate('nullable|email|max:200')]
    public string $invoice_email = '';
    #[Validate('nullable|string')]
    public string $observations = '';
    #[Validate('required_if:is_supplier,|boolean')]
    public bool $is_customer = false;
    #[Validate('required_if:is_customer,|boolean')]
    public bool $is_supplier = false;
    #[Validate('nullable|numeric|min:0|max:1')]
    public float $tax_retention = 0.0;

    public function mount(?int $id = null): void
    {
        $this->id = $id;

        if ($this->id) {
            $this->thirdparty = Thirdparty::emtGet($id);
            $this->loadThirdpartyData();
            $this->isEditing = true;
        } else {
            $this->thirdparty = new Thirdparty();
            $this->isEditing = false;
            $this->country_id = 160;
        }
    }

    public function with(): array
    {
        return [
            'pageTitle' => $this->isEditing
                ? __('thirdparties.edit_thirdparty')
                : __('thirdparties.create_thirdparty'),
            'breadcrumbs' => [
                ['label' => trans_choice('thirdparties.thirdparties', 2), 'url' => route('thirdparties.index')],
                ['label' => $this->isEditing ? __('general.edit') : __('general.new'), 'url' => null],
            ],
            'countries' => $this->getCountries(),
        ];
    }

    public function loadThirdpartyData(): void
    {
        $thirdparty = Thirdparty::find($this->id);

        if (!$thirdparty) {
            $this->redirect(route('thirdparties.index'));
            return;
        }

        $this->legal_form = $thirdparty->legal_form ?? '';
        $this->vat = $thirdparty->vat ?? '';
        $this->contact = $thirdparty->contact ?? '';
        $this->address = $thirdparty->address ?? '';
        $this->town = $thirdparty->town ?? '';
        $this->province = $thirdparty->province ?? '';
        $this->zip = $thirdparty->zip ?? '';
        $this->country_id = $thirdparty->country_id;
        $this->phone = $thirdparty->phone ?? '';
        $this->mobile = $thirdparty->mobile ?? '';
        $this->email = $thirdparty->email ?? '';
        $this->invoice_email = $thirdparty->invoice_email ?? '';
        $this->observations = $thirdparty->observations ?? '';
        $this->is_customer = empty($thirdparty->is_customer) ? false : $thirdparty->is_customer;
        $this->is_supplier = empty($thirdparty->is_supplier) ? false : $thirdparty->is_supplier;
        $this->tax_retention = $thirdparty->tax_retention * 100;
    }

    public function save(): void
    {
        $this->tax_retention = $this->tax_retention / 100;
        $validated = $this->validate();
        $this->tax_retention = $this->tax_retention * 100;

        if ($this->id) {
            $this->thirdparty->update($validated);

            Flux::toast(variant: 'success', text: __('thirdparties.thirdparty_updated'));
        } else {;
            $this->thirdparty = Thirdparty::create($validated + ['company_id' => session('company_id')]);

            Flux::toast(variant: 'success', text: __('thirdparties.thirdparty_created'));
            $this->redirect(route('thirdparties.edit', ['id' => $this->thirdparty->id]));
        }
    }

    public function cancel(): void
    {
        $this->redirect(route('thirdparties.index'));
    }

    public function getCountries()
    {
        return Select::emtGet('countries');
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

    <form class="space-y-6 px-2">
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Main Form - Left Column -->
            <div class="flex-1 lg:w-1/2">
                <div class="space-y-6">
                    <!-- Legal Form and VAT -->
                    <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
                        <flux:field class="col-span-2">
                            <flux:label>{{ __('thirdparties.legal_form') }} *</flux:label>
                            <flux:input wire:model="legal_form" placeholder="{{ __('thirdparties.enter_legal_form') }}" />
                            <flux:error name="legal_form" />
                        </flux:field>

                        <flux:field class="col-span-2">
                            <flux:label>{{ __('thirdparties.contact') }}</flux:label>
                            <flux:input wire:model="contact" placeholder="{{ __('thirdparties.enter_contact') }}" />
                            <flux:error name="contact" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('thirdparties.vat') }} *</flux:label>
                            <flux:input wire:model="vat" placeholder="{{ __('thirdparties.enter_vat') }}" />
                            <flux:error name="vat" />
                        </flux:field>

                        <flux:input label="{{ __('general.tax_retention') }} (%)" mask:dynamic="$money($input, ',')"
                            wire:model="tax_retention" size="sm" error="tax_retention" min-value="0" max-value="100"
                            class:input="text-right" />
                    </div>

                    <!-- Foreign and Contact -->
                    <div class="grid grid-cols-2 md:grid-cols-6 gap-4">

                        <flux:field>
                            <flux:label>{{ __('thirdparties.is_customer') }}</flux:label>
                            <flux:switch wire:model="is_customer" />
                            <flux:error name="is_customer" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('thirdparties.is_supplier') }}</flux:label>
                            <flux:switch wire:model="is_supplier" />
                            <flux:error name="is_supplier" />
                        </flux:field>

                        <!-- Address -->
                        <flux:field class="col-span-2">
                            <flux:label>{{ __('thirdparties.address') }}</flux:label>
                            <flux:input wire:model="address" placeholder="{{ __('thirdparties.enter_address') }}" />
                            <flux:error name="address" />
                        </flux:field>

                        <flux:field class="col-span-2">
                            <flux:label>{{ __('thirdparties.town') }}</flux:label>
                            <flux:input wire:model="town" placeholder="{{ __('thirdparties.enter_town') }}" />
                            <flux:error name="town" />
                        </flux:field>
                    </div>

                    <!-- Town, Province, Zip -->
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <flux:field class="col-span-2 md:col-span-1">
                            <flux:label>{{ __('thirdparties.province') }}</flux:label>
                            <flux:input wire:model="province" placeholder="{{ __('thirdparties.enter_province') }}" />
                            <flux:error name="province" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('thirdparties.zip') }}</flux:label>
                            <flux:input wire:model="zip" placeholder="{{ __('thirdparties.enter_zip') }}" />
                            <flux:error name="zip" />
                        </flux:field>

                        <!-- Country -->
                        <flux:field>
                            <flux:label>{{ __('thirdparties.country') }} *</flux:label>
                            <flux:select wire:model="country_id" variant="listbox" searchable placeholder="{{ __('thirdparties.select_country') }}">
                                <x-slot name="search">
                                    <flux:select.search class="px-4" placeholder="{{ __('general.search') }}" />
                                </x-slot>
                                @foreach ($countries as $country)
                                    <flux:select.option value="{{ $country['value'] }}">{{ $country['option'] }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="country_id" />
                        </flux:field>
                    </div>

                    <!-- Phone and Mobile -->
                    <div class="grid grid-cols-2 md:grid-cols-12 gap-4">
                        <flux:field class="md:col-span-2">
                            <flux:label>{{ __('thirdparties.phone') }}</flux:label>
                            <flux:input wire:model="phone" placeholder="{{ __('thirdparties.enter_phone') }}" />
                            <flux:error name="phone" />
                        </flux:field>

                        <flux:field class="md:col-span-2">
                            <flux:label>{{ __('thirdparties.mobile') }}</flux:label>
                            <flux:input wire:model="mobile" placeholder="{{ __('thirdparties.enter_mobile') }}" />
                            <flux:error name="mobile" />
                        </flux:field>

                        <flux:field class="col-span-2 md:col-span-4">
                            <flux:label>{{ __('thirdparties.email') }}</flux:label>
                            <flux:input type="email" wire:model="email" placeholder="{{ __('thirdparties.enter_email') }}" />
                            <flux:error name="email" />
                        </flux:field>

                        <flux:field class="col-span-2 md:col-span-4">
                            <flux:label>{{ __('thirdparties.invoice_email') }}</flux:label>
                            <flux:input type="email" wire:model="invoice_email" placeholder="{{ __('thirdparties.enter_invoice_email') }}" />
                            <flux:error name="invoice_email" />
                        </flux:field>
                    </div>

                    <!-- Observations -->
                    <flux:field>
                        <flux:label>{{ __('thirdparties.observations') }}</flux:label>
                        <flux:textarea wire:model="observations" rows="4" placeholder="{{ __('thirdparties.enter_observations') }}" />
                        <flux:error name="observations" />
                    </flux:field>
                </div>
            </div>
        </div>
    </form>
    </x-document.layout>
</section>
