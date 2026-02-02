<?php

use App\Models\SalesNote;
use App\Models\SalesNotesProduct;
use App\Models\Select;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Component;
use App\Models\Thirdparty;

new class extends Component {
    public ?SalesNote $note = null;
    public bool $isEditing = false;

    // Line editing state
    public bool $editingLine = false;
    public ?int $editingLineId = null;
    public string $line_description = '';
    public ?int $line_units = null;

    // Note fields
    public ?int $thirdparty_id = null;
    public int $fiscal_year;
    public string $number = '';
    public ?string $date = null;
    public ?string $customer_date = null;
    public ?int $state_id = null;
    public ?string $recipient = null;
    public ?string $reference = null;
    public ?string $observations = null;
    public ?string $internal_note = null;

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
    public string $thirdparty_search = '';
    public ?int $deleteId = null;
    public bool $showDeleteModal = false;

    /**
     * Mount the component
     */
    public function mount(?int $id = null): void
    {
        $this->fiscal_year = now()->year;
        $this->customer_date = now()->format('Y-m-d');

        if ($id) {
            $this->note = SalesNote::emtGet(
                model_id: $id,
                with: ['products']
            );
            $this->isEditing = true;
            $this->loadNoteData();
        } else {
            $this->note = new SalesNote();
            $this->date = today();
            $this->state_id = config('constants.states.pending');
        }

        $this->loadLines();
    }

    /**
     * Load note data into properties
     */
    private function loadNoteData(): void
    {
        $this->thirdparty_id = $this->note->thirdparty_id;
        $this->fiscal_year = $this->note->fiscal_year;
        $this->number = $this->note->number;
        $this->date = $this->note->date?->format('Y-m-d');
        $this->customer_date = $this->note->customer_date?->format('Y-m-d');
        $this->state_id = $this->note->state_id;
        $this->recipient = $this->note->recipient;
        $this->reference = $this->note->reference;
        $this->observations = $this->note->observations;
        $this->internal_note = $this->note->internal_note;
        $this->vat = $this->note->vat;
        $this->legal_form = $this->note->legal_form;
        $this->address = $this->note->address;
        $this->zip = $this->note->zip;
        $this->town = $this->note->town;
        $this->province = $this->note->province;
        $this->country_id = $this->note->country_id;
        $this->phone = $this->note->phone;
        $this->email = $this->note->email;
    }

    /**
     * Get component state
     */
    public function with(): array
    {
        return [
            'pageTitle' => $this->isEditing
                ? __('sales.detail_of_note')
                : trans_choice('sales.notes', 1),
            'breadcrumbs' => [
                ['label' => trans_choice('sales.sales', 2), 'url' => null],
                ['label' => trans_choice('sales.notes', 2), 'url' => route('sales.notes.index')],
                ['label' => $this->isEditing ? ($this->note->number ?? '') : __('general.new'), 'url' => null],
            ],
        ];
    }

    #[Computed]
    public function thirdparties(): array
    {
        return Select::emtGet(
            vcSelect: 'thirdparties',
            search: $this->thirdparty_search,
            selected: $this->thirdparty_id
        );
    }
    #[Computed]
    public function states(): array
    {
        return Select::emtGet('states', SalesNote::class);
    }

    /**
     * Load note lines
     */
    public function loadLines(): void
    {
        if (!$this->note || !$this->note->id) {
            $this->lines = [];
            return;
        }

        $this->lines = $this->note->products->toArray();
    }

    /**
     * Empty line
     */
    public function emptyLine($editingLine = false): void
    {
        $this->editingLine = $editingLine;
        $this->editingLineId = null;
        $this->line_description = '';
        $this->line_units = 1;
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

        $this->editingLine = true;
        $this->editingLineId = $lineId;
        $this->line_description = $line['description'] ?? '';
        $this->line_units = $line['units'] ?? 1;
    }

    /**
     * Save line (create or update)
     */
    public function saveLine(): void
    {
        if (!$this->note || !$this->note->id) {
            Flux::toast(variant: 'danger', text: __('sales.save_budget_first'));
            return;
        }

        $validated = $this->validate([
            'line_description' => 'required|string',
            'line_units' => 'required|numeric|min:0',
        ]);

        $lineData = [
            'sales_note_id' => $this->note->id,
            'description' => $this->line_description,
            'units' => $this->line_units,
        ];

        if ($this->editingLineId) {
            // Update existing line
            $line = SalesNotesProduct::find($this->editingLineId);
            $line->update($lineData);
            Flux::toast(variant: 'success', text: __('sales.line_updated'));
        } else {
            // Create new line
            SalesNotesProduct::create($lineData);
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
            $line = SalesNotesProduct::find($this->deleteId);

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
     * Save note (create or update)
     */
    public function save(): void
    {
        $rules = [
            'thirdparty_id' => ['required', 'exists:thirdparties,id'],
            'fiscal_year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'date' => ['required', 'date'],
            'customer_date' => ['required', 'date'],
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
            // Update existing note
            $this->note->update($validated);
            $this->note = SalesNote::emtGet(
                model_id: $this->note->id,
                with: ['products']
            );
            $this->loadNoteData();
            Flux::toast(variant: 'success', text: __('general.updated'));
        } else {
            // Create new note
            $this->note = SalesNote::create($validated);
            Flux::toast(variant: 'success', text: __('general.created'));
            $this->redirect(route('sales.notes.edit', ['id' => $this->note->id]));
        }
    }

    /**
     * Cancel and return to notes list
     */
    public function cancel(): void
    {
        $this->redirect(route('sales.notes.index'), navigate: true);
    }
}; ?>

<section class="w-full h-full">
    <x-document.layout :title="$pageTitle" :breadcrumbs="$breadcrumbs">
        <x-slot:buttons>
            <flux:button type="button" size="sm" variant="primary" icon="save" class="cursor-pointer" wire:click="save">
                <span class="hidden md:inline">{{ __('general.save') }}</span>
            </flux:button>
        </x-slot:buttons>
        <!-- Two-column layout: Form and Lines -->
        <div class="flex flex-col gap-6 lg:flex-row h-full">
            <div class="w-full f-full overflow-y-auto">
                <form wire:submit="save" class="space-y-6 px-2">
                    <!-- Company & Customer -->
                    <div class="grid grid-cols-2 lg:grid-cols-6 gap-4 mb-2">
                        <flux:field class="col-span-2">
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

                        <flux:date-picker label="{{ __('sales.customer_date') }}" size="sm"
                            wire:model="customer_date" locale="es-ES" start-day="1" error="customer_date" />

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
                    </div>

                    <flux:tab.group class="space-y-6">
                        <flux:tabs>
                            @if($note->id)
                                <flux:tab name="lines-tab">{{ trans_choice('sales.lines', 2) }}</flux:tab>
                            @endif
                            <flux:tab name="more-data-tab">{{ __('general.more_info') }}</flux:tab>
                        </flux:tabs>
                        @if($note->id)
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
                                            <flux:table.column align="center" class="w-1/2 md:w-1/2">{{ __('sales.description') }}</flux:table.column>
                                            <flux:table.column align="center">{{ __('sales.units') }}</flux:table.column>
                                            <flux:table.column class="w-20"></flux:table.column>
                                        </flux:table.columns>

                                        <flux:table.rows>
                                            @foreach($lines as $line)
                                                <flux:table.row :key="$line['id']">
                                                    <flux:table.cell class="w-1/2 md:w-1/2">
                                                        <a href="#" class="text-sm font-bold block text-wrap" wire:click="editLine({{ $line['id'] }})">{{ $line['description'] }}</a>
                                                    </flux:table.cell>
                                                    <flux:table.cell class="text-right">{{ $line['units'] ? number_format($line['units'], 0) : "-" }}</flux:table.cell>
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
                            <!-- Recipient & Reference -->
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
                        <flux:textarea wire:model="line_description" rows="2" size="sm"
                            label="{{ __('sales.description') }}"
                            error="line_description"
                        />

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <flux:input :label="__('sales.units')"
                                wire:model="line_units" size="sm" error="line_units"
                                class:input="text-right" />
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
            <x-delete-modal wire:model="showDeleteModal" />
        </x-slot:modals>
    </x-document.layout>
</section>
