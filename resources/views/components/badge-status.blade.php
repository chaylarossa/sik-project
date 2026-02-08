@props(['status'])

@php
    $classes = match ($status) {
        'BARU' => 'badge-baru',
        'DALAM_PENANGANAN' => 'badge-proses',
        'SELESAI' => 'badge-selesai',
        'DITUTUP' => 'badge-ditutup',
        default => 'badge-baru',
    };
    
    $label = str_replace('_', ' ', $status);
@endphp

<span {{ $attributes->merge(['class' => 'badge ' . $classes]) }}>
    {{ $label }}
</span>
