@props(['label', 'value' => null, 'empty' => 'Não informado'])

<div {{ $attributes->class(['min-w-0']) }}>
    <p class="text-sm uppercase text-base-content/60">{{ $label }}</p>
    <p class="mt-1 break-words text-base font-medium">
        @if (isset($slot) && trim((string) $slot) !== '')
            {{ $slot }}
        @else
            {{ filled($value) ? $value : $empty }}
        @endif
    </p>
</div>
