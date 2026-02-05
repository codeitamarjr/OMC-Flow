<div class="col-span-2 bg-gradient-to-t from-indigo-500 to-blue-500 px-4 py-8 w-full h-full rounded-xl">
    <p class="mb-4 font-medium text-indigo-100">Company Compliance</p>
    <div class="mb-6 flex max-w-xs">
        <div
            class="mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-400 sm:mr-3 sm:mb-0">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="h-6 w-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
            </svg>
        </div>
        <div class="px-4">
            <p class="mb-1 text-2xl font-black text-white">{{ $totalCompanies }}</p>
            <p class="font-medium text-indigo-100">Active companies tracked</p>
            <p class="text-xs text-indigo-100/90 mt-1">
                Last synced at:
                @if ($lastSyncedAt)
                    {{ $lastSyncedAt->format('d M Y H:i') }} ({{ $lastSyncedAt->diffForHumans() }})
                @else
                    Not synced yet
                @endif
            </p>
        </div>
    </div>
    <div class="flex flex-wrap justify-between">
        <div class="flex flex-col items-center px-4 py-1">
            <p class="text-lg font-medium text-white">{{ $overdueAr }}</p>
            <p class="text-xs font-medium text-indigo-100">Overdue</p>
        </div>
        <div class="mb-1 flex flex-col items-center px-4 py-1 sm:mr-1 sm:mb-0">
            <p class="text-lg font-medium text-white">{{ $riskyCount }}</p>
            <p class="text-xs font-medium text-indigo-100">Risky</p>
        </div>
        <div class="mb-1 flex flex-col items-center rounded-2xl bg-white px-4 py-1 sm:mr-1 sm:mb-0">
            <p class="text-lg font-medium text-indigo-500">{{ $dueSoonAr }}</p>
            <p class="text-xs font-medium text-indigo-500">Due Soon</p>
        </div>
        <div class="flex flex-col items-center px-4 py-1">
            <p class="text-lg font-medium text-white">{{ $missingCount }}</p>
            <p class="text-xs font-medium text-indigo-100">Missing</p>
        </div>
    </div>
</div>
