@props(['page'])
<x-base :title="$page->title">
    {{-- Header Here --}}

    <x-filament-fabricator::page-blocks :blocks="$page->blocks" />

    {{-- Footer Here --}}
</x-base>
