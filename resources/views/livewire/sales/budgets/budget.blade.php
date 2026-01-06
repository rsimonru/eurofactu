<?php

use App\Models\SalesBudget;
use App\Models\SalesBudgetsProduct;
use App\Models\Select;
use App\Models\TaxType;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;
use App\Classes\PdfFile;
use App\Models\Product;
use App\Models\ProductsVariant;
use App\Notifications\SendBudget;
use Illuminate\Support\Facades\Notification;

new class extends Component {
    public ?SalesBudget $budget = null;
    public bool $isEditing = false;
    public string $activeTab = 'lines';

    // Line editing state
    public bool $editingLine = false;
    public ?int $editingLineId = null;
    public ?int $line_product_id = null;
    public ?int $line_product_variant_id = null;
    public string $line_description = '';
    public ?int $line_units = null;
    public $line_base_unit = null;
    public $line_discountp = null;
    public $line_tax_type_id = null;


    // Budget fields
    public ?int $thirdparty_id = null;
    public int $fiscal_year;
    public string $number = '';
    public ?string $date = null;
    public ?string $sent_date = null;
    public ?string $valid_until = null;
    public ?int $state_id = null;
    public ?string $recipient = null;
    public ?string $reference = null;
    public ?string $observations = null;
    public ?string $internal_note = null;
    public float $tax_retention = 0.0;

    // Thirdparty data
    public ?string $vat = null;
    public ?string $legal_form = null;
    public ?string $address = null;
    public ?string $zip = null;
    public ?string $town = null;
    public ?string $province = null;
    public ?int $country_id = null;
    public ?string $phone = null;
    public ?string $email = null;

    public array $lines = [];

    public array $budget_emails = [];
    public array $selected_budget_emails = [];
    public string $new_budget_email = '';
    public bool $openSendEmail = false;
    public ?int $deleteId = null;
    public bool $showDeleteModal = false;
    public string $product_search = '';

    /**
     * Mount the component
     */
    public function mount(?int $id = null): void
    {
        $this->fiscal_year = now()->year;
        $this->date = now()->format('Y-m-d');

        if ($id) {
            $this->budget = SalesBudget::emtGet(
                model_id: $id,
                with: ['products.product_variant.product.tax_type']
            );
            $this->budget_emails = [$this->budget->thirdparty->email];
            $this->selected_budget_emails = [$this->budget->thirdparty->email];
            $this->isEditing = true;
            $this->loadBudgetData();
        } else {
            $this->budget = new SalesBudget();
            $this->state_id = config('constants.states.pending');
        }

        $this->loadLines();
    }

    /**
     * Load budget data into properties
     */
    private function loadBudgetData(): void
    {
        $this->company_id = $this->budget->company_id;
        $this->thirdparty_id = $this->budget->thirdparty_id;
        $this->fiscal_year = $this->budget->fiscal_year;
        $this->number = $this->budget->number;
        $this->date = $this->budget->date->format('Y-m-d');
        $this->sent_date = $this->budget->sent_date?->format('Y-m-d');
        $this->valid_until = $this->budget->valid_until?->format('Y-m-d');
        $this->state_id = $this->budget->state_id;
        $this->recipient = $this->budget->recipient;
        $this->reference = $this->budget->reference;
        $this->observations = $this->budget->observations;
        $this->internal_note = $this->budget->internal_note;
        $this->vat = $this->budget->vat;
        $this->legal_form = $this->budget->legal_form;
        $this->address = $this->budget->address;
        $this->zip = $this->budget->zip;
        $this->town = $this->budget->town;
        $this->province = $this->budget->province;
        $this->country_id = $this->budget->country_id;
        $this->phone = $this->budget->phone;
        $this->email = $this->budget->email;
        $this->tax_retention = $this->budget->tax_retention ?? 0.0;
    }

    /**
     * Get component state
     */
    public function with(): array
    {
        return [
            'pageTitle' => $this->isEditing
                ? __('sales.edit_budget')
                : __('sales.create_budget'),
            'breadcrumbs' => [
                ['label' => trans_choice('sales.sales', 2), 'url' => null],
                ['label' => trans_choice('sales.budgets', 2), 'url' => route('sales.budgets.index')],
                ['label' => $this->isEditing ? $this->budget->number : __('general.new'), 'url' => null],
            ],
            'products_summary' => $this->budget ? $this->budget->emtGetProductsSummary() : [],
        ];
    }

    #[Computed]
    public function products()
    {
        return Select::emtGet(
            vcSelect: 'products',
            search: $this->product_search,
            selected: $this->line_product_id
        );
    }
    #[Computed]
    public function products_variants(): array
    {
        if ($this->line_product_id) {
            $variants = Select::emtGet(
                vcSelect: 'products_variants',
                parameter1: $this->line_product_id
            );
            return $variants;
        } else {
            return [];
        }
    }
    #[Computed]
    public function thirdparties(): array
    {
        return Select::emtGet('thirdparties');
    }
    #[Computed]
    public function tax_types(): array
    {
        return Select::emtGet('tax_types_ids');
    }
    #[Computed]
    public function states(): array
    {
        return Select::emtGet('states', SalesBudget::class);
    }

    public function updatingLineProductId($value)
    {
        $product = Product::emtGet(model_id: $value, with: ['variants']);
        if (length($product->variants) == 1) {
            $product_variant = $product->variants->first();
            $this->line_product_variant_id = $product_variant->id;
            $this->updateLineFields($product_variant);
        } else {
            $this->line_product_variant_id = null;
        }
    }
    public function updatingLineProductVariantId($value)
    {
        $product_variant = ProductsVariant::find($value);
        $this->updateLineFields($product_variant);
    }
    public function updateLineFields($product_variant)
    {
        if($product_variant) {
            $this->line_description = $product_variant->product->description;
            $this->line_base_unit = $product_variant->price;
            $this->line_tax_type_id = $product_variant->product->tax_type_id ?? null;
        }
    }

    public function downloadPDF()
    {
        $pdf = new PdfFile();
        $pdf->documents = $this->budget;
        $pdf->data = [
            'company' => $this->budget->company,
            'products_summary' => $this->budget ? $this->budget->emtGetProductsSummary() : [],
        ];
        $data = $pdf->generateFromTemplate('pdf.budget');

        return response()->streamDownload(
            fn () => print($data),
            'Presupuesto-'.$this->budget->number.'.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }
    public function sendEmail()
    {
        if (length($this->selected_budget_emails) == 0) {
            Flux::toast(variant: 'danger', text: __('general.add_email'));
            return;
        }
        $pdf = new PdfFile();
        $pdf->documents = $this->budget;
        $pdf->data = [
            'company' => $this->budget->company,
            'products_summary' => $this->budget ? $this->budget->emtGetProductsSummary() : [],
        ];
        $data = $pdf->generateFromTemplate('pdf.budget');

        Notification::route('mail', $this->selected_budget_emails)->notify(new SendBudget($this->budget, $data));
    }
    public function createBudgetEmail()
    {
        $validated = $this->validate([
            'new_budget_email' => 'required|email:rfc,dns',
        ]);
        $this->budget_emails[] = $validated['new_budget_email'];
    }

    /**
     * Load budget lines
     */
    public function loadLines(): void
    {
        if (!$this->budget || !$this->budget->id) {
            $this->lines = [];
            return;
        }

        $this->lines = $this->budget->products->toArray();
    }

    /**
     * Empty line
     */
    public function emptyLine($editingLine = false): void
    {
        $this->editingLine = $editingLine;
        $this->editingLineId = null;
        $this->line_product_id = null;
        $this->line_product_variant_id = null;
        $this->line_description = '';
        $this->line_units = 1;
        $this->line_base_unit = 0;
        $this->line_discountp = 0;
        $this->line_tax_type_id = null;
    }

    /**
     * Start editing an existing line
     */
    public function editLine(int $lineId): void
    {
        $line = collect($this->lines)->firstWhere('id', $lineId);

        if (!$line) {
            return;
        }

        $product_line = $this->budget->products->where('id', $lineId)->first();

        $this->editingLine = true;
        $this->editingLineId = $lineId;
        $this->line_product_id = $product_line->product_variant->product_id ?? null;
        $this->line_product_variant_id = $product_line->product_variant_id ?? null;
        $this->line_description = $line['description'] ?? '';
        $this->line_units = $line['units'] ?? 1;
        $this->line_base_unit = $line['base_unit'] ?? 0;
        $this->line_discountp = ($line['discountp'] ?? 0) * 100;
        $this->line_tax_type_id = $line['tax_type_id'] ?? 0;
    }

    /**
     * Save line (create or update)
     */
    public function saveLine(): void
    {
        if (!$this->budget || !$this->budget->id) {
            Flux::toast(variant: 'danger', text: __('sales.save_budget_first'));
            return;
        }

        $validated = $this->validate([
            'line_product_variant_id' => 'nullable|numeric|exists:products_variants,id',
            'line_description' => 'required|string',
            'line_units' => 'required|integer|min:0',
            'line_base_unit' => 'required|numeric|min:0',
            'line_discountp' => 'nullable|numeric|min:0|max:100',
            'line_tax_type_id' => 'required|numeric|exists:tax_types,id',
        ]);

        $this->line_discountp = $this->line_discountp / 100;

        $tax_type = TaxType::dlGet($this->line_tax_type_id);

        // Calculate amounts
        $base_result = $this->line_base_unit * (1 - $this->line_discountp);
        $base_line = $base_result * $this->line_units;
        $tax_unit = $base_result * $tax_type->value;
        $tax_line = $tax_unit * $this->line_units;
        $es_type = ($this->budget->thirdparty->equivalence_surcharge ?? 0) ? $tax_type->pes : 0;
        $es_unit = $base_result * $es_type;
        $es_line = $es_unit * $this->line_units;
        $total_line = $base_line + $tax_line + $es_line;

        $lineData = [
            'sales_budget_id' => $this->budget->id,
            'product_variant_id' => $this->line_product_variant_id,
            'description' => $this->line_description,
            'units' => $this->line_units,
            'base_unit' => $this->line_base_unit,
            'discount_type' => 'P',
            'discountp' => $this->line_discountp,
            'discounti' => 0,
            'base_result' => $base_result,
            'base_line' => $base_line,
            'tax_type_id' => $tax_type->id ?? null,
            'tax_type' => $tax_type->value,
            'tax_unit' => $tax_unit,
            'tax_line' => $tax_line,
            'es_type' => $es_type,
            'es_unit' => $es_unit,
            'es_line' => $es_line,
            'total_line' => $total_line,
        ];

        if ($this->editingLineId) {
            // Update existing line
            $line = SalesBudgetsProduct::find($this->editingLineId);
            $line->update($lineData);
            Flux::toast(variant: 'success', text: __('sales.line_updated'));
        } else {
            // Create new line
            SalesBudgetsProduct::create($lineData);
            Flux::toast(variant: 'success', text: __('sales.line_created'));
        }

        $this->emptyLine(false);
        $this->loadLines();
    }

    /**
     * Delete a line
     */
    public function doDelete(): void
    {
        if ($this->deleteId) {
            $line = SalesBudgetsProduct::find($this->deleteId);

            if ($line) {
                $line->delete();
                Flux::toast(variant: 'success', text: __('sales.line_deleted'));
                $this->loadLines();
            }
        }
        $this->deleteId = null;
        $this->showDeleteModal = false;
    }

    /**
     * Save budget (create or update)
     */
    public function save(): void
    {
        $rules = [
            'thirdparty_id' => ['required', 'exists:thirdparties,id'],
            'fiscal_year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'date' => ['required', 'date'],
            'sent_date' => ['nullable', 'date'],
            'valid_until' => ['nullable', 'date'],
            'state_id' => ['required', 'exists:states,id'],
            'recipient' => ['nullable', 'string', 'max:255'],
            'reference' => ['nullable', 'string', 'max:100'],
            'observations' => ['nullable', 'string'],
            'internal_note' => ['nullable', 'string'],
            'vat' => ['nullable', 'string', 'max:25'],
            'legal_form' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:100'],
            'zip' => ['nullable', 'string', 'max:15'],
            'town' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:75'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'phone' => ['nullable', 'string', 'max:25'],
            'email' => ['nullable', 'email', 'max:255'],
        ];

        $validated = $this->validate($rules);

        if ($this->isEditing) {
            // Update existing budget
            $this->budget->update($validated);
            $this->budget = SalesBudget::emtGet(
                model_id: $this->budget->id,
                with: ['products']
            );
            $this->loadBudgetData();
            Flux::toast(variant: 'success', text: __('sales.budget_updated'));
        } else {
            // Create new budget
            $this->budget = SalesBudget::create($validated);
            Flux::toast(variant: 'success', text: __('sales.budget_created'));
            $this->redirect(route('sales.budgets.edit', ['id' => $this->budget->id]));
        }
    }

    /**
     * Cancel and return to budgets list
     */
    public function cancel(): void
    {
        $this->redirect(route('sales.budgets.index'), navigate: true);
    }
}; ?>

<section class="w-full h-full">
    <x-document.layout :title="$pageTitle" :breadcrumbs="$breadcrumbs">
        <x-slot:buttons>
            <flux:button type="button" size="sm" variant="primary" color="blue" icon="file-pdf" class="cursor-pointer" wire:click="downloadPDF">
                <span class="hidden md:inline">PDF</span>
            </flux:button>

            <flux:button type="button" size="sm" variant="primary" color="blue" icon="envelope" class="cursor-pointer" x-on:click="$wire.openSendEmail = true">
                <span class="hidden md:inline">Email</span>
            </flux:button>

            <flux:button type="button" size="sm" variant="primary" icon="save" class="cursor-pointer" wire:click="save">
                <span class="hidden md:inline">{{ __('general.save') }}</span>
            </flux:button>
        </x-slot:buttons>
        <!-- Two-column layout: Form on left, Tabs on right (desktop), stacked on mobile -->
        <div class="flex flex-col gap-6 lg:flex-row h-full">
            <div class="w-full f-full overflow-y-auto">
                <form wire:submit="save" class="space-y-6 px-2">
                    <!-- Company & Customer -->
                    <div class="grid grid-cols-1 lg:grid-cols-6 gap-4 mb-2">
                        <flux:field class="lg:col-span-2">
                            <flux:select size="sm" wire:model="thirdparty_id" variant="listbox"
                                searchable placeholder="{{ __('general.select') }}"
                                label="{{ __('sales.customer') }}"
                                error="thirdparty_id">
                                <x-slot name="search">
                                    <flux:select.search class="px-4" placeholder="{{ __('general.search') }}" />
                                </x-slot>
                                @foreach ($this->thirdparties as $thirdparty)
                                    <flux:select.option value="{{ $thirdparty['value'] }}">{{ $thirdparty['option'] }}</flux:select.option>
                                @endforeach
                            </flux:select>
                        </flux:field>

                        <flux:date-picker label="{{ __('general.date') }}" size="sm"
                            wire:model="date" locale="es-ES" start-day="1" error="date" />

                        <flux:select size="sm" wire:model="state_id" variant="listbox"
                            label="{{ __('general.state') }}"
                            searchable placeholder="{{ __('general.select') }}"
                            error="state_id">
                            <x-slot name="search">
                                <flux:select.search class="px-4" placeholder="{{ __('general.search') }}" />
                            </x-slot>
                            @foreach ($this->states as $state)
                                <flux:select.option value="{{ $state['value'] }}">{{ $state['option'] }}</flux:select.option>
                            @endforeach
                        </flux:select>

                        <flux:date-picker size="sm" wire:model="valid_until" locale="es-ES"
                            label="{{ __('sales.valid_until') }}"
                            placeholder="{{ __('general.select') }}"
                            error="valid_until"
                            start-day="1" />

                        <flux:date-picker size="sm" wire:model="sent_date" locale="es-ES" clearable
                            label="{{ __('sales.sent_date') }}"
                            start-day="1"
                            error="sent_date"
                            placeholder="{{ __('general.select') }}" />
                    </div>

                    <flux:tab.group class="space-y-6">
                        <flux:tabs>
                            @if($budget->id)
                                <flux:tab name="lines-tab">{{ trans_choice('sales.lines', 2) }}</flux:tab>
                            @endif
                            <flux:tab name="more-data-tab">{{ __('general.more_info') }}</flux:tab>
                        </flux:tabs>
                        @if($budget->id)
                        <flux:tab.panel name="lines-tab" class="pt-1!">
                            <div class="space-y-4">
                                <div class="flex justify-end">
                                    <flux:button size="sm" variant="primary" wire:click="emptyLine(true)" icon="plus" class="cursor-pointer">
                                        {{ __('sales.add_line') }}
                                    </flux:button>
                                </div>

                                @if(count($lines) > 0)
                                    <flux:table>
                                        <flux:table.columns sticky>
                                            <flux:table.column align="center" class="w-1/2 md:w-1/4">{{ __('sales.description') }}</flux:table.column>
                                            <flux:table.column align="center">{{ __('sales.units') }}</flux:table.column>
                                            <flux:table.column align="center">{{ __('sales.price_unit') }}</flux:table.column>
                                            <flux:table.column align="center">{{ __('sales.discount') }}</flux:table.column>
                                            <flux:table.column align="center">{{ __('sales.tax') }}</flux:table.column>
                                            @if($this->budget->thirdparty->equivalence_surcharge ?? 0)
                                                <flux:table.column align="center">{{ __('general.re') }}</flux:table.column>
                                            @endif
                                            <flux:table.column align="center">{{ __('sales.total') }}</flux:table.column>
                                            <flux:table.column class="w-20"></flux:table.column>
                                        </flux:table.columns>

                                        <flux:table.rows>
                                            @foreach($lines as $line)
                                                <flux:table.row :key="$line['id']">
                                                    <flux:table.cell class="w-1/2 md:w-1/4">
                                                        <a href="#" class="text-sm font-bold block text-wrap" wire:click="editLine({{ $line['id'] }})">{{ $line['description'] }}</a>
                                                    </flux:table.cell>
                                                    <flux:table.cell class="text-right">{{ $line['units'] ? number_format($line['units'], 0) : "-" }}</flux:table.cell>
                                                    <flux:table.cell class="text-right">{{ $line['units'] ? number_format($line['base_unit'], 2, ",", ".")."€" : "-" }}</flux:table.cell>
                                                    <flux:table.cell class="text-right">{{ $line['units'] ? number_format($line['discountp']* 100, 2, ",", ".")."%" : "-" }}</flux:table.cell>
                                                    <flux:table.cell class="text-right">{{ $line['units'] ? number_format($line['tax_type'] * 100, 2, ",", ".")."%" : "-" }}</flux:table.cell>
                                                    @if($this->budget->thirdparty->equivalence_surcharge ?? 0)
                                                        <flux:table.cell class="text-right">{{ $line['units'] ? number_format($line['es_type'] * 100, 2, ",", ".")."%" : "-" }}</flux:table.cell>
                                                    @endif
                                                    <flux:table.cell class="text-right font-semibold">{{ $line['units'] ? number_format($line['total_line'], 2, ",", ".")."€" : "-" }}</flux:table.cell>
                                                    <flux:table.cell class="justify-end align-middle">
                                                        <span>
                                                            <flux:button
                                                                size="sm"
                                                                variant="ghost"
                                                                icon="pencil"
                                                                class="cursor-pointer"
                                                                wire:click="editLine({{ $line['id'] }})"
                                                                tooltip="{{ __('general.edit') }}"
                                                            />
                                                            <flux:button
                                                                size="sm"
                                                                variant="danger"
                                                                icon="trash"
                                                                class="cursor-pointer"
                                                                x-on:click="$wire.showDeleteModal = true; $wire.deleteId = {{ $line['id'] }};"
                                                                wire:confirm="{{ __('general.confirm_delete') }}"
                                                                tooltip="{{ __('general.delete') }}"
                                                            />
                                                        </span>
                                                    </flux:table.cell>
                                                </flux:table.row>
                                            @endforeach
                                        </flux:table.rows>
                                    </flux:table>

                                    <div class="border-t pt-4">
                                        <table class="flex justify-end">
                                            <tr>
                                                <td colspan="2" class="text-sm pr-4">{{ __('sales.subtotal') }}</td>
                                                <td class="text-sm font-semibold text-right w-32">{{ number_format($products_summary['base_line'] , 2, ",", ".") }}€</td>
                                            </tr>
                                            @foreach($products_summary['tax_summary'] as $tax_values)
                                                <tr>
                                                    <td class="text-sm pr-4">{{ $tax_values['tax_name'] }}</td>
                                                    <td class="text-sm pr-4">{{ $tax_values['es_line'] ? ('+ '. number_format($tax_values['es_rate'] * 100, 2, ",", ".").'% '. __('general.re')) : '' }}</td>
                                                    <td class="text-sm font-semibold text-right w-32">{{ number_format($tax_values['tax_line'] + $tax_values['es_line'], 2, ",", ".") }}€</td>
                                                </tr>
                                            @endforeach
                                            @if($tax_retention != 0)
                                                <tr>
                                                    <td class="text-sm pr-4">{{ __('general.tax_retention') }}</td>
                                                    <td class="text-sm pr-4">{{ number_format($tax_retention * 100, 2, ",", ".") }}%</td>
                                                    <td class="text-sm font-semibold text-right w-32"> - {{ number_format($products_summary['base_line'] * $tax_retention, 2, ",", ".") }}€</td>
                                                </tr>
                                            @endif
                                            <tr class="border-t pt-2">
                                                <td colspan="2" class="text-lg font-bold pr-4">{{ __('sales.total') }}</td>
                                                <td class="text-sm font-semibold text-right w-32">{{ number_format($products_summary['total_line'] - ($products_summary['base_line'] * $tax_retention), 2, ",", ".") }}€</td>
                                            </tr>
                                        </table>
                                    </div>
                                @else
                                    @if(!$editingLine)
                                        <div class="text-center py-8 text-gray-500">
                                            <flux:icon.document-text class="w-12 h-12 mx-auto mb-2 text-gray-400" />
                                            <p>{{ __('sales.no_lines') }}</p>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </flux:tab.panel>
                        @endif

                        <flux:tab.panel name="more-data-tab" class="pt-1!">
                            <!-- Valid Until, Sent Date, State -->
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-2">
                                <flux:field>
                                    <flux:label>{{ __('sales.recipient') }}</flux:label>
                                    <flux:input
                                        size="sm"
                                        wire:model="recipient"
                                        type="text"
                                        placeholder="{{ __('sales.enter_recipient') }}"
                                    />
                                    <flux:error name="recipient" />
                                </flux:field>

                                <flux:field>
                                    <flux:label>{{ __('sales.reference') }}</flux:label>
                                    <flux:input
                                        size="sm"
                                        wire:model="reference"
                                        type="text"
                                        placeholder="{{ __('sales.enter_reference') }}"
                                    />
                                    <flux:error name="reference" />
                                </flux:field>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-2">
                                <!-- Observations -->
                                <flux:field>
                                    <flux:label>{{ __('sales.observations') }}</flux:label>
                                    <flux:textarea
                                        size="sm"
                                        wire:model="observations"
                                        rows="3"
                                        placeholder="{{ __('sales.enter_observations') }}"
                                    />
                                    <flux:error name="observations" />
                                </flux:field>

                                <!-- Internal Note -->
                                <flux:field>
                                    <flux:label>{{ __('sales.internal_note') }}</flux:label>
                                    <flux:textarea
                                        size="sm"
                                        wire:model="internal_note"
                                        rows="3"
                                        placeholder="{{ __('sales.enter_internal_note') }}"
                                    />
                                    <flux:error name="internal_note" />
                                </flux:field>
                            </div>
                        </flux:tab.panel>
                    </flux:tab.group>
                </form>
            </div>
        </div>
        <x-slot:modals>
            <flux:modal class="w-[95%]! md:w-auto! md:max-w-[50vw]!" wire:model.self="editingLine">
                <div class="space-y-6">
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-2">
                            <flux:select size="sm" wire:model.live="line_product_id" variant="listbox" searchable clearable
                                label="{{ trans_choice('general.products', 1) }}"
                                empty="{{ __('general.no_products_found') }}"
                                placeholder="{{ __('general.select') }}">
                                <x-slot name="search">
                                    <flux:select.search class="px-4" placeholder="{{ __('general.search') }}" />
                                </x-slot>

                                @foreach ($this->products as $product)
                                    <flux:select.option :value="$product['value']">{{ $product['option'] }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            @if ($this->line_product_id)
                            <flux:select size="sm" wire:model.live="line_product_variant_id" variant="listbox" searchable clearable
                                label="{{ trans_choice('general.variants', 1) }}"
                                empty="{{ __('general.no_variants_found') }}"
                                placeholder="{{ __('general.select') }}">
                                <x-slot name="search">
                                    <flux:select.search class="px-4" placeholder="{{ __('general.search') }}" />
                                </x-slot>

                                @foreach ($this->products_variants as $product_variant)
                                    <flux:select.option :value="$product_variant['value']">{{ $product_variant['option'] }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            @endif
                        </div>

                        <flux:textarea wire:model="line_description" rows="2" size="sm"
                            label="{{ __('sales.description') }}"
                            error="line_description"
                        />

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <flux:input :label="__('sales.units')" mask:dynamic="$money($input, ',')"
                                wire:model="line_units" size="sm" error="line_units"
                                class:input="text-right" />

                            <flux:input label="{{ __('sales.price_unit') }} (€)" mask:dynamic="$money($input, ',')"
                                wire:model="line_base_unit" size="sm" error="line_base_unit"
                                class:input="text-right" />

                            <flux:input label="{{ __('sales.discount') }} (%)" mask:dynamic="$money($input, ',')"
                                wire:model="line_discountp" size="sm" error="line_discountp" min-value="0" max-value="100"
                                class:input="text-right" />

                            <flux:select size="sm" wire:model="line_tax_type_id" variant="listbox"
                                label="{{ __('sales.tax') }} (%)"
                                placeholder="{{ __('general.select') }}"
                                error="line_tax_type_id">
                                @foreach ($this->tax_types as $tax_types)
                                    <flux:select.option value="{{ $tax_types['value'] }}">{{ $tax_types['option'] }}</flux:select.option>
                                @endforeach
                            </flux:select>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <flux:spacer />
                        <flux:modal.close>
                            <flux:button size="sm" variant="ghost" wire:click="emptyLine(false)">{{ __('general.cancel') }}</flux:button>
                        </flux:modal.close>
                        <flux:button size="sm" variant="primary" wire:click="saveLine">
                            {{ __('general.save') }}
                        </flux:button>
                    </div>
                </div>
            </flux:modal>
            <flux:modal class="md:w-96" name="send-email" wire:model.self="openSendEmail">
                <div class="space-y-6">
                    <div>
                        <flux:heading size="lg">{{ __('sales.send_budget') }}</flux:heading>
                    </div>
                    <div>
                        <flux:pillbox size="sm" wire:model="selected_budget_emails" variant="combobox" multiple label="Email">
                            <x-slot name="input">
                                <flux:pillbox.input size="sm" wire:model="new_budget_email" placeholder="Email" />
                            </x-slot>
                            @foreach ($this->budget_emails as $budget_email)
                                <flux:pillbox.option :value="$budget_email">{{ $budget_email }}</flux:pillbox.option>
                            @endforeach
                            <flux:pillbox.option.create wire:click="createBudgetEmail" min-length="3">
                                {{ __('general.add') }} "<span wire:text="new_budget_email"></span>"
                            </flux:pillbox.option.create>
                        </flux:pillbox>
                    </div>
                    <div class="flex gap-2">
                        <flux:spacer />
                        <flux:modal.close>
                        <flux:button size="sm" variant="primary" wire:click="sendEmail" icon="envelope">
                            {{ __('general.send') }}
                        </flux:button>
                        </flux:modal.close>
                    </div>
                </div>
            </flux:modal>
            <x-delete-modal wire:model="showDeleteModal" />
        </x-slot:modals>
    </x-document.layout>
</section>
