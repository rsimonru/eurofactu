<?php

use Livewire\Volt\Component;
use App\Models\Company;
use App\Models\Select;

new class extends Component {

    public ?int $fiscal_year = null;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->fiscal_year = session('fiscal_year', date('Y'));
    }

    public function with(): array
    {
        return [
            'years' => $this->getYears(),
        ];
    }

    public function getYears(): array
    {
        return Select::emtGet('fiscal_years', '5', '1');
    }

    public function updatedFiscalYear(): void
    {
        session(['fiscal_year' => $this->fiscal_year]);
        $this->redirect(route('dashboard'));
    }

}; ?>

<div>
    <flux:select wire:model.live="fiscal_year" placeholder="{{ __('general.select') }}" size="sm">
        @foreach ($years as $year)
            <flux:select.option value="{{ $year['value'] }}">{{ $year['option'] }}</flux:select.option>
        @endforeach
    </flux:select>
</div>
