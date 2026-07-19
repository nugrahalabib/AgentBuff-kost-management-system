{{-- Inject palet warna dasar (brand) pilihan owner ke CSS variable --c-em-*.
     Dipakai di <head> layout owner & admin. Default 'emerald' -> tak inject (pakai :root app.css).
     Admin mewarisi warna owner-nya via resolveOwner(). --}}
@php
    $__brandUser = auth()->check() ? auth()->user() : null;
    $__brandOwner = $__brandUser && method_exists($__brandUser, 'resolveOwner')
        ? $__brandUser->resolveOwner()
        : $__brandUser;
    $__brandKey = $__brandOwner?->businessSettings?->brand_color ?? 'emerald';
    $__brandShades = $__brandKey && $__brandKey !== 'emerald'
        ? config("brand_colors.{$__brandKey}.shades")
        : null;
@endphp
@if(is_array($__brandShades))
    <style id="brand-theme">:root{@foreach($__brandShades as $__k => $__v)--c-em-{{ $__k }}:{{ $__v }};@endforeach}</style>
@endif
