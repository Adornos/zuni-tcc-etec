@props(['report', 'compact' => false])

@php
    $prefix = auth()->user()->role->value;
    $size = $compact ? 'btn-xs' : 'btn-sm';
@endphp

<div class="flex flex-wrap items-center gap-2">

    @can('update', $report)
        <a href="{{ route($prefix . '.report.edit', $report) }}" class="btn {{ $size }} btn-outline">
            Editar
        </a>
    @endcan

    @can('toggleActive', $report)
        <form action="{{ route($prefix . '.report.status', $report) }}" method="POST">
            @csrf
            @method('PATCH')
            <input type="hidden" name="is_active" value="{{ $report->is_active ? 0 : 1 }}">
            <button type="submit" class="btn {{ $size }} btn-outline">
                {{ $report->is_active ? 'Inativar relatório' : 'Reativar relatório' }}
            </button>
        </form>
    @endcan

    @can('delete', $report)
        <form
            action="{{ route($prefix . '.report.destroy', $report) }}"
            method="POST"
            onsubmit="return confirm('Excluir definitivamente este relatório? Esta ação não pode ser desfeita.');"
        >
            @csrf
            @method('DELETE')
            <button type="submit" class="btn {{ $size }} btn-error btn-outline">
                Excluir
            </button>
        </form>
    @endcan

</div>
