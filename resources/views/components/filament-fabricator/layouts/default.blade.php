@props(['page'])
<x-filament-fabricator::layouts.base :title="$page->title">
    {{-- Header Here --}}

    <x-filament-fabricator::page-blocks :blocks="$page->blocks" />

    {{-- Footer Here --}}
</x-filament-fabricator::layouts.base>

{{-- <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $page->title }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />


</head>

<body class="antialiased"> --}}

{{--
</body>

</html> --}}
