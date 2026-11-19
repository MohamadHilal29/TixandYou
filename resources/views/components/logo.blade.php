@props(['size' => 'md'])

@php
    $wrapperClass = match($size) {
        'sm' => 'h-9',
        'lg' => 'h-14',
        default => 'h-11',
    };
    $imgClass = match($size) {
        'sm' => 'h-20',
        'lg' => 'h-32',
        default => 'h-24',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center overflow-hidden {$wrapperClass}"]) }}>
    <img src="{{ asset('images/logo.png') }}"
         alt="Tix and You - Your Local Event Ticketing Partner"
         class="{{ $imgClass }} w-auto max-w-none object-contain mix-blend-multiply select-none">
</span>