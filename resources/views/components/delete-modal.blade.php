<?php

use Livewire\Volt\Component;

new class extends Component {
    public $showDeleteModal = false;

}; ?>

<flux:modal class="md:w-96" wire:model.self="showDeleteModal">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">{{ __('general.delete') }}</flux:heading>
            <flux:text class="mt-2">{{ __('general.confirm_delete') }}</flux:text>
        </div>
        <div class="flex gap-2">
            <flux:spacer />
            <flux:modal.close>
                <flux:button variant="ghost">{{ __('general.cancel') }}</flux:button>
            </flux:modal.close>
            <flux:button type="button" variant="danger" wire:click="doDelete">{{ __('general.delete') }}</flux:button>
        </div>
    </div>
</flux:modal>