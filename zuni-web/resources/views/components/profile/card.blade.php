@props(['title'])

<div {{ $attributes->class(['card bg-base-100 shadow-md min-w-0']) }}>
    <div class="card-body min-w-0 p-4 sm:p-6">
        <h2 class="text-lg font-bold uppercase text-error">{{ $title }}</h2>

        <div class="mt-5 grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">
            {{ $slot }}
        </div>
    </div>
</div>
