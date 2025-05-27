<!-- loepos-platform/resources/views/components/shared/badge.blade.php -->
@props(['status'])

@php
    $backgroundColor = match($status) {
        \App\Models\Dossier::STATUS_CLOSED => 'var(--color-transparant-red)',
        \App\Models\Dossier::STATUS_IN_PROCESS => 'var(--color-transparant-yellow)',
        \App\Models\Dossier::STATUS_ACTIVE => 'var(--color-transparant-green)',
        default => 'var(--color-gray)',
    };

    $textColor = match($status) {
        \App\Models\Dossier::STATUS_CLOSED => 'var(--color-red)',
        \App\Models\Dossier::STATUS_IN_PROCESS => 'var(--color-yellow)',
        \App\Models\Dossier::STATUS_ACTIVE => 'var(--color-green)',
        default => 'black',
    };
@endphp

<span class="px-2 py-1 rounded-full" style="background-color: {{ $backgroundColor }}; color: {{ $textColor }};">
    {{ $status }}
</span>
