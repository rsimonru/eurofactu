@props([
    'title' => '',
    'search' => '',
    'summary' => null,  
    'searchPlaceholder' => '',
    'breadcrumbs' => [],
])
<div class="h-[calc(100vh-14rem)] lg:h-[calc(100vh-6rem)] flex flex-col">
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

            @if ($buttons->isEmpty())
            @else
                <div class="flex items-center justify-end gap-3">
                    {{ $buttons }}
                </div>
            @endif
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
                                :placeholder="(empty($searchPlaceholder) ? __('general.search').' ...' : $searchPlaceholder.' ...')"
                                type="text"
                                class="w-full"
                                size="sm"
                            >
                                <x-slot name="iconTrailing">
                                    <flux:icon.magnifying-glass variant="outline" />
                                </x-slot>
                            </flux:input>
                        </div>
                    </div>

                    {{ $slot }}
                    @if ($summary)
                    <!-- Summary Bar -->
                    <div class="sticky bottom-0 bg-white dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-800 shadow-lg z-50">
                        <div class="max-w-7xl mx-auto px-4 py-1 lg:py-2">
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex items-center gap-6">
                                    <div class="text-right">
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400">Docs:</div>
                                        <div class="text-xs lg:text-sm font-semibold text-zinc-700 dark:text-zinc-300">
                                            {{ $this->summary->documents ?? 0 }}
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-8">
                                    <div class="text-right">
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('sales.base') }}</div>
                                        <div class="text-xs lg:text-sm font-semibold text-zinc-700 dark:text-zinc-300">
                                            {{ number_format($this->summary->base_line ?? 0, 2, ',', '.') }} €
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('sales.taxes') }}</div>
                                        <div class="text-xs lg:text-sm font-semibold text-zinc-700 dark:text-zinc-300">
                                            {{ number_format($this->summary->tax_line ?? 0, 2, ',', '.') }} €
                                        </div>
                                    </div>
                                    <div class="text-right border-l border-zinc-200 dark:border-zinc-700 pl-8">
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('sales.total') }}</div>
                                        <div class="text-md lg:text-lg font-bold text-zinc-900 dark:text-zinc-100">
                                            {{ number_format($this->summary->total_line ?? 0, 2, ',', '.') }} €
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
