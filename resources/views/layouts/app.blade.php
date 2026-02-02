@props([
    'title' => null
])
<x-layouts.app.sidebar :title="$title">
    <flux:main class="!p-2 !lg:p-4">
        {{ $slot }}
    </flux:main>
</x-layouts.app.sidebar>
