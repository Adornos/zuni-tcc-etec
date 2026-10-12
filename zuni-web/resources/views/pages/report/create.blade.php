<x-dynamic-component :component="'panel.' . auth()->user()->role->value">

    <x-report.form
        :report="$report"
        :target="$target"
        :selected="$selected"
        :sheets="$sheets"
        :classrooms="$classrooms"
        :types="$types"
        :prefix="$prefix"
    />

</x-dynamic-component>
