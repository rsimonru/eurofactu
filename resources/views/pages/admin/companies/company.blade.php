<?php

use Livewire\Component;
use App\Models\Company;
use Flux\Flux;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Spatie\Image\Image;
use Spatie\Image\Manipulations;

new class extends Component {
    use WithFileUploads;

    public Company $company;

    public $name;
    public $legal_form;
    public $vat;
    public $address;
    public $zip;
    public $town;
    public $province;
    public $phone;
    public $email;
    public $web_url;
    public $logo;

    public function mount()
    {
        $this->company = Company::find(auth()->user()->company_id);

        if (!$this->company) {
            abort(404, 'Company not found');
        }

        $this->name = $this->company->name;
        $this->legal_form = $this->company->legal_form;
        $this->vat = $this->company->vat;
        $this->address = $this->company->address;
        $this->zip = $this->company->zip;
        $this->town = $this->company->town;
        $this->province = $this->company->province;
        $this->phone = $this->company->phone;
        $this->email = $this->company->email;
        $this->web_url = $this->company->web_url;
    }

    /**
     * Get component state
     */
    public function with(): array
    {
        return [
            'pageTitle' => __('admin.edit_company'),
            'breadcrumbs' => [
                ['label' => __('admin.admin'), 'url' => null],
                ['label' => __('admin.edit_company'), 'url' => null],
            ],
        ];
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'legal_form' => 'nullable|string|max:255',
            'vat' => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
            'zip' => 'nullable|string|max:10',
            'town' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'web_url' => 'nullable|url|max:255',
            'logo' => 'nullable|image|max:4096',
        ]);

        $this->company->name = $this->name;
        $this->company->legal_form = $this->legal_form;
        $this->company->vat = $this->vat;
        $this->company->address = $this->address;
        $this->company->zip = $this->zip;
        $this->company->town = $this->town;
        $this->company->province = $this->province;
        $this->company->phone = $this->phone;
        $this->company->email = $this->email;
        $this->company->web_url = $this->web_url;

        if ($this->logo) {
            // dd($this->logo, $this->logo->getRealPath(), $this->logo->getPath());
            $root_path = 'companies/' . $this->company->id;
            if (!Storage::exists($root_path)) {
                Storage::makeDirectory($root_path, 0775, true);
            }
            $path = $root_path.'/logo.png';
            Image::load($this->logo->getRealPath())->width(400)->height(100)->save(storage_path('app/private/' . $path));

            $this->company->logo = $path;
        }
        $this->company->save();

        Flux::toast(variant: 'success', text: __('general.saved_successfully'));
    }
}; ?>

<section class="w-full h-full">
    <x-document.layout
        :title="$pageTitle"
        :breadcrumbs="$breadcrumbs"
    >
        <x-slot:buttons>
            <flux:button type="button" size="sm" variant="filled" href="{{ route('dashboard') }}">
                {{ __('general.cancel') }}
            </flux:button>

            <flux:button type="button" size="sm" variant="primary" wire:click="save">
                {{ __('general.save') }}
            </flux:button>
        </x-slot:buttons>

        <form class="grid gap-4 px-2">
            <div class="grid gap-6 md:grid-cols-3">
                <flux:file-upload wire:model="logo">
                    <!-- Custom log uploader -->
                    <div class="
                        relative flex items-center justify-center transition-colors cursor-pointer
                        border border-zinc-200 dark:border-white/10 hover:border-zinc-300 dark:hover:border-white/10
                        bg-zinc-100 hover:bg-zinc-200 dark:bg-white/10 hover:dark:bg-white/15 in-data-dragging:dark:bg-white/15
                    ">
                        <!-- Show the uploaded file if it exists -->
                        @if ($logo)
                            <img src="{{ $logo?->temporaryUrl() }}" class="size-full object-cover rounded-full" />
                        @elseif ($company->logo)
                            @php
                                $src = null;
                                if (file_exists(storage_path('app/private/' . $company->logo))) {
                                    $src =
                                        'data: ' .
                                        mime_content_type(
                                            storage_path('app/private/' . $company->logo),
                                        ) .
                                        ';base64,' .
                                        base64_encode(
                                            file_get_contents(
                                                storage_path('app/private/' . $company->logo),
                                            ),
                                        );
                                }
                            @endphp
                            <img src="{{ $src }}" class="size-full object-cover rounded-full" />
                        @else
                            <!-- Show the default icon if no file is uploaded -->
                            <flux:icon name="user" variant="solid" class="text-zinc-500 dark:text-zinc-400" />
                        @endif

                        <!-- Corner upload icon -->
                        <div class="absolute bottom-0 right-0 bg-white dark:bg-zinc-800 rounded-full">
                            <flux:icon name="arrow-up-circle" variant="solid" class="text-zinc-500 dark:text-zinc-400" />
                        </div>
                    </div>
                </flux:file-upload>
            </div>
            <div class="grid gap-4 md:grid-cols-12">
                <div class="grid md:col-span-5">
                    <flux:input wire:model="name" label="{{ __('general.name') }}" />
                </div>
                <div class="grid md:col-span-5">
                    <flux:input wire:model="legal_form" label="{{ __('admin.legal_form') }}" />
                </div>
                <div class="grid md:col-span-2">
                    <flux:input wire:model="vat" label="{{ __('admin.vat') }}" />
                </div>
            </div>

            <flux:input wire:model="address" label="{{ __('admin.address') }}" />

            <div class="grid gap-4 md:grid-cols-3">
                <flux:input wire:model="zip" label="{{ __('admin.zip') }}" />
                <flux:input wire:model="town" label="{{ __('admin.town') }}" />
                <flux:input wire:model="province" label="{{ __('admin.province') }}" />
            </div>
            <div class="grid gap-4 md:grid-cols-12">
                <div class="grid md:col-span-5">
                    <flux:input wire:model="email" label="{{ __('admin.email') }}" type="email" />
                </div>
                <div class="grid md:col-span-5">
                    <flux:input wire:model="web_url" label="{{ __('admin.web') }}" />
                </div>
                <div class="grid md:col-span-2">
                    <flux:input wire:model="phone" label="{{ __('admin.phone') }}" />
                </div>
            </div>
        </form>
    </x-document.layout>
</section>
