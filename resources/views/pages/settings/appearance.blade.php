<?php

use Livewire\Component;

new class extends Component {
    //
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('general.appearance')" :subheading=" __('general.appearance_subheading')">
        <flux:radio.group x-data variant="segmented" x-model="$flux.appearance">
            <flux:radio value="light" icon="sun">{{ __('general.light') }}</flux:radio>
            <flux:radio value="dark" icon="moon">{{ __('general.dark') }}</flux:radio>
            <flux:radio value="system" icon="computer-desktop">{{ __('general.system') }}</flux:radio>
        </flux:radio.group>
    </x-settings.layout>
</section>
