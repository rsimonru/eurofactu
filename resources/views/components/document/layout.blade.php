@props([
    'title' => '',
    'records' => null,
    'search' => '',
    'breadcrumbs' => [],
])
<div class="h-[calc(100vh-7rem)] lg:h-[calc(100vh-4rem)] flex flex-col">
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
            @if ($buttons->isEmpty())
            @else
                <div class="flex items-center justify-end gap-3">
                    {{ $buttons }}
                </div>
            @endif
        </div>
        <flux:separator variant="subtle" class="my-2"/>
    </div>
    <!-- Main content area fills remaining viewport height on desktop -->
    <div class="flex-1 flex items-start max-md:flex-col min-h-0 overflow-auto">
        <!-- Outer wrapper no overflow so focus rings are not clipped -->
        <div class="flex-1 self-stretch max-md:pt-6">
            <!-- Scrolling container with padding to avoid cutting focus border -->
            <div class="h-full overflow-y-auto pb-1">
                <div class="h-full w-full">
                    <div class="h-full w-full max-w-full space-y-6">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
