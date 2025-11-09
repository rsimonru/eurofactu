@props([
    'title' => '',
    'records' => null,
    'search' => '',
    'breadcrumbs' => [],
    'buttons' => [],
])
<div>
    <div class="relative mb-4 w-full">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item href="{{ route('dashboard') }}" icon="home" />
                @foreach ($breadcrumbs as $breadcrumb)
                    @if (isset($breadcrumb['url']) && $breadcrumb['url'])
                        <flux:breadcrumbs.item :href="$breadcrumb['url']">{{ $breadcrumb['label'] }}</flux:breadcrumbs.item>
                    @else
                        <flux:breadcrumbs.item>{{ $breadcrumb['label'] }}</flux:breadcrumbs.item>
                    @endif
                @endforeach
            </flux:breadcrumbs>

            <flux:button.group class="md:shrink-0">
                @foreach ($buttons as $button)
                    <flux:button href="{{ $button['url'] }}" variant="primary" color="{{ $button['color'] ?? 'blue' }}" icon="{{ $button['icon'] }}" size="{{ $button['size'] ?? 'sm' }}">
                        {{ $button['label'] }}
                    </flux:button>
                @endforeach
            </flux:button.group>
        </div>
        <flux:separator variant="subtle" class="my-2"/>
    </div>
    <div class="flex items-start max-md:flex-col">
        <div class="flex-1 self-stretch max-md:pt-6">
            <div class="mt-2 w-full">
                <div class="my-2 w-full max-w-full space-y-6">
                    <!-- Search Bar -->
                    <div class="flex items-center gap-4">
                        <div class="ms-auto w-full md:w-1/3">
                            <flux:input
                                wire:model.live.debounce.300ms="{{ $search }}"
                                :placeholder="__('admin.search_users').'...'"
                                type="text"
                                class="w-full"
                            >
                                <x-slot name="iconTrailing">
                                    <flux:icon.magnifying-glass variant="outline" />
                                </x-slot>
                            </flux:input>
                        </div>
                    </div>

                    {{ $slot }}
                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $records->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
