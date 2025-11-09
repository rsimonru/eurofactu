@props([
    'title' => '',
    'records' => null,
    'search' => '',
    'breadcrumbs' => [],
    'buttons' => [],
])
<div class="min-h-screen flex flex-col">
    <div class="relative mb-4 w-full">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item href="{{ route('dashboard') }}" icon="home" />
                @foreach ($breadcrumbs as $breadcrumb)
                    @if (isset($breadcrumb['url']) && $breadcrumb['url'])
                        <flux:breadcrumbs.item :href="$breadcrumb['url']">
                            <flux:button href="{{ $breadcrumb['url'] }}" variant="primary" size="sm">
                                {{ $breadcrumb['label'] }}
                            </flux:button>
                        </flux:breadcrumbs.item>
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
    <!-- Main content area fills remaining viewport height on desktop -->
    <div class="flex-1 flex items-start max-md:flex-col min-h-0">
        <div class="flex-1 self-stretch max-md:pt-6 overflow-auto">
            <div class="mt-2 w-full">
                <div class="my-2 w-full max-w-full space-y-6">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</div>
