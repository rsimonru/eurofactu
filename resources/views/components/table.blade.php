@props([
    // Optional table classes
    'containerClass' => 'overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-700',
    'tableClass' => 'min-w-full divide-y divide-zinc-200 dark:divide-zinc-700',
    'theadClass' => 'bg-zinc-50 dark:bg-zinc-800',
    'tbodyClass' => 'divide-y divide-zinc-200 bg-white dark:divide-zinc-700 dark:bg-zinc-900',
])

<div class="{{ $containerClass }}">
    <table class="{{ $tableClass }}">
        <thead class="{{ $theadClass }}">
            {{ $header ?? '' }}
        </thead>
        <tbody class="{{ $tbodyClass }}">
            {{ $slot }}
        </tbody>
    </table>
</div>
