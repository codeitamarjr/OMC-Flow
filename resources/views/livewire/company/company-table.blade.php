<div class="h-full flex flex-col bg-white dark:bg-gray-800 relative overflow-hidden">
    <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
        <div class="w-full md:w-1/2">
            <form class="flex items-center">
                <label for="simple-search" class="sr-only">Search</label>
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor"
                            viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input type="text" id="simple-search" wire:model.live.debounce.1000ms="search"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        placeholder="Search" required="">
                </div>
            </form>
        </div>
        <div
            class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0">
            <div class="flex items-center space-x-3 w-full md:w-auto">
                {{-- Filter --}}
                <div class="relative" x-data="{ showDropDown: false }">
                    <button id="filterDropdownButton" @click="showDropDown = !showDropDown"
                        data-dropdown-toggle="filterDropdown"
                        class="w-full md:w-auto flex items-center justify-center py-2 px-4 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                        type="button">
                        <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="h-4 w-4 mr-2 text-gray-400"
                            viewbox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z"
                                clip-rule="evenodd" />
                        </svg>
                        Filter
                        <svg class="-mr-1 ml-1.5 w-5 h-5" fill="currentColor" viewbox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path clip-rule="evenodd" fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                        </svg>
                    </button>
                    @if ($allTags->isEmpty())
                        <div class="absolute right-0 z-10 w-48 p-3 bg-white rounded-lg shadow dark:bg-gray-700"
                            x-show="showDropDown">
                            <p class="text-sm text-gray-500 dark:text-gray-400">No tags available</p>
                        </div>
                    @else
                        {{-- Dropdown menu --}}
                        <div id="filterDropdown" x-show="showDropDown" @click.away="showDropDown = false"
                            class="absolute right-0 z-10 w-48 p-3 bg-white rounded-lg shadow dark:bg-gray-700">
                            <h6 class="mb-3 text-sm font-medium text-gray-900 dark:text-white">Filter by Tag
                            </h6>
                            <ul class="space-y-2 text-sm" aria-labelledby="filterDropdownButton">
                                @foreach ($allTags as $tag)
                                    <li class="flex items-center">
                                        <input type="checkbox" value="{{ $tag->id }}" id="tag-{{ $tag->id }}"
                                            wire:model.live="selectedTagFilters"
                                            class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                                        <label for="tag-{{ $tag->id }}"
                                            class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $tag->name }}
                                        </label>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full h-full text-sm text-left text-gray-500 dark:text-gray-400" wire:loading.class="opacity-50">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-4 py-3">Company</th>
                    <th scope="col" class="px-4 py-3" wire:click="sort('nearest_deadline')">
                        <div class="flex items-center">
                            @if ($sortBy === 'nearest_deadline')
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor"
                                    class="size-6 duration-400 transform  ease-in-out
                                @if ($sortDirection === 'asc' && $sortBy === 'nearest_deadline') rotate-180 @endif">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m15 11.25-3-3m0 0-3 3m3-3v7.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            @endif
                            Next Deadline
                        </div>
                    </th>
                    <th scope="col" class="px-4 py-3" wire:click="sort('max_risk_score')">
                        <div class="flex items-center">
                            @if ($sortBy === 'max_risk_score')
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor"
                                    class="size-6 duration-400 transform ease-in-out
                                @if ($sortDirection === 'asc' && $sortBy === 'max_risk_score') rotate-180 @endif">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m15 11.25-3-3m0 0-3 3m3-3v7.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            @endif
                            Risk Summary
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($companies as $company)
                    <tr class="border-b dark:border-gray-700">
                        <th scope="row"
                            class="px-4 py-1 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            <div class="text-sm/6 text-gray-500 dark:text-gray-400">{{ $company->company_number }}
                            </div>
                            <div class="text-sm/6 text-gray-900 dark:text-gray-300">{{ $company->name }}</div>
                            <div class="text-sm/6 text-gray-500 dark:text-gray-400">{{ $company->custom }}
                                @if ($company->tags->isNotEmpty())
                                    @foreach ($company->tags as $tag)
                                        <span
                                            class="inline-flex items-center gap-x-1 rounded-full px-1.5 py-0.5 text-xs font-medium text-gray-900 ring-1 ring-inset ring-gray-200 bg-indigo-50 dark:bg-gray-800 dark:text-gray-300 dark:ring-gray-700">
                                            <svg class="size-1.5 fill-indigo-500" viewBox="0 0 6 6"
                                                aria-hidden="true">
                                                <circle cx="3" cy="3" r="3" />
                                            </svg>
                                            {{ $tag->name }}
                                        </span>
                                    @endforeach
                                @endif
                            </div>
                        </th>
                        <td class="px-4 py-1">
                            @php
                                $nextDeadline = $company->croDocDefinitions
                                    ->filter(fn($d) => in_array($d->code, ['B1', 'B10'], true))
                                    ->filter(fn($d) => !empty($d->pivot->due_date))
                                    ->sortBy('pivot.due_date')
                                    ->first();
                            @endphp

                            @if ($nextDeadline)
                                @php
                                    $dueDate = \Carbon\Carbon::parse($nextDeadline->pivot->due_date);
                                @endphp
                                <div class="mt-1 text-xs/5 text-gray-900 dark:text-gray-300">
                                    {{ $nextDeadline->code }} - {{ $nextDeadline->name }}
                                </div>
                                <div class="mt-1 text-xs/5 text-gray-500 dark:text-gray-400">
                                    {{ $dueDate->format('d M Y') }}
                                </div>
                            @else
                                <div class="mt-1 text-xs/5 text-gray-500 dark:text-gray-400">No scheduled deadline</div>
                            @endif
                        </td>
                        <td class="px-4 py-1">
                            @php
                                $obligations = $company->croDocDefinitions->filter(
                                    fn($d) => in_array($d->code, ['B1', 'B10'], true),
                                );
                                $overdueCount = $obligations->where('pivot.status', 'overdue')->count();
                                $riskyCount = $obligations->where('pivot.status', 'risky')->count();
                                $missingCount = $obligations->where('pivot.status', 'missing')->count();
                                $issueCount = $overdueCount + $riskyCount + $missingCount;
                                $riskLabel = match ((int) ($company->max_risk_score ?? 0)) {
                                    4 => 'High',
                                    3 => 'Elevated',
                                    2 => 'Medium',
                                    1 => 'Low',
                                    default => 'Compliant',
                                };
                                $riskClass = match ((int) ($company->max_risk_score ?? 0)) {
                                    4 => 'bg-red-100 text-red-700',
                                    3 => 'bg-orange-100 text-orange-700',
                                    2 => 'bg-yellow-100 text-yellow-700',
                                    1 => 'bg-blue-100 text-blue-700',
                                    default => 'bg-green-100 text-green-700',
                                };
                            @endphp

                            <span class="inline-flex items-center gap-x-1.5 rounded-md px-1.5 py-0.5 text-xs font-medium {{ $riskClass }}">
                                {{ $riskLabel }}
                            </span>
                            <div class="mt-2 flex flex-wrap gap-1">
                                @if ($issueCount === 0)
                                    <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium bg-green-100 text-green-700">
                                        No open issues
                                    </span>
                                @else
                                    @if ($overdueCount > 0)
                                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium bg-red-100 text-red-700">
                                            Overdue: {{ $overdueCount }}
                                        </span>
                                    @endif
                                    @if ($riskyCount > 0)
                                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium bg-orange-100 text-orange-700">
                                            Risky: {{ $riskyCount }}
                                        </span>
                                    @endif
                                    @if ($missingCount > 0)
                                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium bg-yellow-100 text-yellow-700">
                                            Missing: {{ $missingCount }}
                                        </span>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach

                @if ($companies->isEmpty())
                    <tr>
                        <td colspan="6" class="text-center py-40 text-gray-500">
                            No companies found.
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 sm:px-6 {{ $companies->links() ? 'mt-10' : '' }}">
        {{ $companies->links() ?? '' }}
    </div>
</div>
