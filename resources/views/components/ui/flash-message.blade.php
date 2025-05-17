@props(['type' => 'success', 'title' => null])

@php
    $color = match ($type) {
        'success' => 'green',
        'error' => 'red',
        'warning' => 'yellow',
        'info' => 'blue',
        default => 'gray',
    };
@endphp

<div class="rounded-md bg-{{ $color }}-100 p-4 m-2">
    <div class="flex">
        <div class="shrink-0">
            @if ($type === 'success')
                <svg class="size-5 text-{{ $color }}-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z"
                        clip-rule="evenodd" />
                </svg>
            @endif
            {{-- Add other icons here for error/info if desired --}}
        </div>
        <div class="ml-3 w-full">
            @if ($title)
                <h3 class="text-sm font-medium text-{{ $color }}-800">{{ $title }}</h3>
            @endif
            <div class="mt-1 text-sm text-{{ $color }}-700">
                <p>{{ $slot }}</p>
            </div>
        </div>
    </div>
</div>
