<x-dynamic-component :component="'panel.' . auth()->user()->role->value">
    <div class="row-span-4 col-span-4">
        <livewire:search.student-list />
    </div>
</x-dynamic-component>