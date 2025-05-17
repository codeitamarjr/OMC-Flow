<div class="flex aspect-square size-8 items-center justify-center rounded-md bg-white dark:bg-gray-800">
    <x-app-logo-icon class="size-5 dark:text-black"/>
</div>
<div class="ms-1 grid flex-1 text-start text-sm">
    <span class="mb-0.5 truncate leading-none font-semibold">OMC Flow</span>
    @if (auth()->user()->currentBusiness()->exists())
        <span class="truncate text-xs leading-tight text-gray-500 dark:text-gray-400">
            {{ auth()->user()->currentBusiness->name }}
        </span>
    @endif
</div>
