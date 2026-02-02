<?php

use Livewire\Volt\Component;
use App\Models\Company;
use App\Models\Select;
use Illuminate\Support\Facades\Auth;

new class extends Component {

    public ?int $company_id = null;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->company_id = Auth::user()->company_id;
    }

    public function with(): array
    {
        return [
            'companies' => $this->getCompanies(),
        ];
    }

    public function getCompanies(): array
    {
        return Select::emtGet('companies');
    }

    public function updatedCompanyId(): void
    {
        $company = Company::emtGet($this->company_id);
        session(['company_id' => $this->company_id]);
        session(['company' => $company]);
        $this->redirect(route('dashboard'));
    }
}; ?>

<div>
    <flux:select wire:model="company_id" placeholder="{{ __('general.select') }}" size="sm">
        @foreach ($companies as $company)
            <flux:select.option value="{{ $company['value'] }}">{{ $company['option'] }}</flux:select.option>
        @endforeach
    </flux:select>
</div>
