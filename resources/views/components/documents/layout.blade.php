@props([
    'title' => '',
    'search' => '',
    'summary' => null,
    'filter_labels' => null,
    'searchPlaceholder' => '',
    'breadcrumbs' => [],
])
<div class="h-[calc(100vh-14rem)] md:h-[calc(100vh-6rem)] flex flex-col">
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

            @if (isset($buttons) && !$buttons->isEmpty())
                <div class="flex items-center justify-end gap-3">
                    {{ $buttons }}
                </div>
            @endif
        </div>
        <flux:separator variant="subtle" class="my-2"/>
    </div>
    <div class="flex items-start max-md:flex-col">
        <div class="flex-1 self-stretch">
            <div class="mt-2 w-full">
                <div class="my-2 w-full max-w-full space-y-6">
                    <!-- Search Bar -->
                    <div class="flex items-center gap-2">
                        <div class="w-full">
                            @if(isset($filter_labels) && $filter_labels['FilterOn'])
                                <div class="hidden md:block">
                                    <flux:button size="sm" icon:trailing="trash"
                                        class="cursor-pointer"
                                        wire:click="deleteFilter()"
                                        tooltip="{{ __('general.delete_filter') }}"
                                    >
                                        {{ __('general.delete_filter') }}
                                    </flux:button>
                                    @foreach ($filter_labels['aValues'] as $key => $filter_label)
                                        @if(length($filter_label['aValues']) > 0)
                                            <flux:button size="sm" variant="primary" color="sky" class="cursor-pointer"
                                                icon:trailing="x-mark"
                                                wire:click="deleteLabelFilter('{{ $key }}')"
                                                tooltip="{{ __('general.delete_filter') . ' ' . $filter_label['label'] }}">
                                                {{ $filter_label['label'] . ' ('.length($filter_label['aValues']).')' }}
                                            </flux:button>
                                        @endif
                                    @endforeach
                                </div>
                                <div class="block md:hidden">
                                    <flux:dropdown>
                                        <flux:button size="sm" icon:trailing="chevron-down">{{ trans_choice('general.filters', 2) }}</flux:button>
                                        <flux:menu keep-open>
                                            <flux:menu.item icon="trash" wire:click="deleteFilter()">
                                                {{ __('general.delete_filter') }}
                                            </flux:menu.item>
                                            <flux:menu.separator />
                                            @foreach ($filter_labels['aValues'] as $key => $filter_label)
                                                @if(length($filter_label['aValues']) > 0)
                                                    <flux:menu.item icon="x-mark" wire:click="deleteLabelFilter('{{ $key }}')">
                                                        {{ $filter_label['label'] . ' ('.length($filter_label['aValues']).')' }}
                                                    </flux:menu.item>
                                                @endif
                                            @endforeach
                                        </flux:menu>
                                    </flux:dropdown>
                                </div>
                            @endif
                        </div>
                        <flux:modal.trigger name="filter-records">
                            <flux:button size="sm" icon="funnel" variant="outline" class="cursor-pointer">
                                <span class="hidden md:inline">{{ trans_choice('general.filters', 2) }}</span>
                            </flux:button>
                        </flux:modal.trigger>
                        <div class="w-full md:w-1/4">
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
                    <div class="sticky bottom-0 bg-white dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-800 shadow-lg">
                        <div class="max-w-7xl mx-auto px-4 py-1 md:py-2">
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex items-center gap-6">
                                    <div class="text-right">
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400">Docs:</div>
                                        <div class="text-xs md:text-sm font-semibold text-zinc-700 dark:text-zinc-300">
                                            {{ $this->summary->documents ?? 0 }}
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 md:gap-8">
                                    <div class="text-right">
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('sales.base') }}</div>
                                        <div class="text-xs md:text-sm font-semibold text-zinc-700 dark:text-zinc-300">
                                            {{ number_format($this->summary->base_line ?? 0, 2, ',', '.') }} €
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('sales.taxes') }}</div>
                                        <div class="text-xs md:text-sm font-semibold text-zinc-700 dark:text-zinc-300">
                                            {{ number_format($this->summary->tax_line ?? 0, 2, ',', '.') }} €
                                        </div>
                                    </div>
                                    <div class="text-right border-l border-zinc-200 dark:border-zinc-700 pl-4 md:pl-8">
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('sales.total') }}</div>
                                        <div class="text-md md:text-lg font-bold text-zinc-900 dark:text-zinc-100">
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
    @if (isset($modals) && !$modals->isEmpty())
        {{ $modals }}
    @endif
</div>
