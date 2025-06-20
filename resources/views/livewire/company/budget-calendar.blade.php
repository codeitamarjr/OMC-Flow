<div class="h-full flex flex-col bg-white dark:bg-gray-800 relative overflow-hidden p-4 space-y-6">

    <div>
        <header class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
            <h1 class="text-base font-semibold text-gray-900">
                <time
                    datetime="{{ \Carbon\Carbon::parse($currentDate) }}">{{ \Carbon\Carbon::parse($currentDate)->format('Y') }}</time>
            </h1>
            <div class="flex items-center">

                @if ($viewMode === 'month')
                    <div class="relative flex items-center rounded-md bg-white shadow-sm md:items-stretch">
                        <button type="button" wire:click="goToPreviousMonth"
                            class="flex h-9 w-12 items-center justify-center rounded-l-md border-y border-l border-gray-300 pr-1 text-gray-400 hover:text-gray-500 focus:relative md:w-9 md:pr-0 md:hover:bg-gray-50">
                            <span class="sr-only">Previous month</span>
                            <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"
                                data-slot="icon">
                                <path fill-rule="evenodd"
                                    d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                        <button type="button" wire:click="goToToday"
                            class="hidden border-y border-gray-300 px-3.5 text-sm font-semibold text-gray-900 hover:bg-gray-50 focus:relative md:block
                            {{ !$currentDate->isToday() ? 'bg-gray-100 cursor-pointer' : '' }}">Today</button>
                        <span class="relative -mx-px h-5 w-px bg-gray-300 md:hidden"></span>
                        <button type="button" wire:click="goToNextMonth"
                            class="flex h-9 w-12 items-center justify-center rounded-r-md border-y border-r border-gray-300 pl-1 text-gray-400 hover:text-gray-500 focus:relative md:w-9 md:pl-0 md:hover:bg-gray-50">
                            <span class="sr-only">Next month</span>
                            <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"
                                data-slot="icon">
                                <path fill-rule="evenodd"
                                    d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                @endif

                <div class="hidden md:ml-4 md:flex md:items-center">
                    <div class="max-w-sm mx-auto">
                        <label for="company" class="sr-only">Select an option</label>
                        <select id="company" wire:model.live="selectedCompanyId"
                            class="font-semibold text-gray-900  text-sm rounded-lg block w-full p-2.5 px-3 gap-x-1.5 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                            <option selected>Choose a company</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="hidden md:ml-4 md:flex md:items-center">
                    <div class="relative" x-data="{ open: false }" @click.away="open = false">
                        <button type="button"
                            class="flex items-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                            id="menu-button" aria-expanded="false" aria-haspopup="true" x-on:click="open = !open">
                            View: {{ ucfirst($viewMode) }}
                            <svg class="-mr-1 size-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"
                                aria-hidden="true" data-slot="icon">
                                <path fill-rule="evenodd"
                                    d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div class="absolute right-0 z-10 mt-3 w-36 origin-top-right overflow-hidden rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-none"
                            role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1"
                            x-show="open" x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95">
                            <div class="py-1" role="none">
                                <div class="block px-4 py-2 text-sm text-gray-700 cursor-pointer
                                    {{ $viewMode == 'month' ? 'bg-gray-100 text-gray-900 outline-none' : '' }}"
                                    role="menuitem" tabindex="-1" id="menu-item-2"
                                    @click="$wire.set('viewMode', 'month')">Month</a>
                                </div>
                                <div class="block px-4 py-2 text-sm text-gray-700 cursor-pointer
                                    {{ $viewMode == 'year' ? 'bg-gray-100 text-gray-900 outline-none' : '' }}"
                                    role="menuitem" tabindex="-1" id="menu-item-3"
                                    @click="$wire.set('viewMode', 'year')">Year</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ml-6 h-6 w-px bg-gray-300"></div>
                    <button type="button"
                        class="ml-6 rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500">Add
                        event</button>
                </div>
            </div>
        </header>
        <div class="bg-white">
            <div
                class="mx-auto grid max-w-3xl grid-cols-1 gap-x-8 gap-y-16 px-4 py-16 sm:grid-cols-2 sm:px-6 xl:max-w-none xl:grid-cols-3 xl:px-8 2xl:grid-cols-4">

                @php
                    $monthsToShow =
                        $viewMode === 'year'
                            ? collect(range(1, 12))->map(fn($m) => \Carbon\Carbon::create(null, $m, 1))
                            : collect([\Carbon\Carbon::create($currentDate->year, $currentDate->month, 1)]);
                @endphp

                @foreach ($monthsToShow as $monthDate)
                    <section class="text-center">
                        <h2 class="text-sm font-semibold text-gray-900">{{ $monthDate->format('F') }}</h2>
                        <div class="mt-6 grid grid-cols-7 text-xs/6 text-gray-500">
                            @foreach (['M', 'T', 'W', 'T', 'F', 'S', 'S'] as $day)
                                <div>{{ $day }}</div>
                            @endforeach
                        </div>

                        @php
                            $startOfMonth = $monthDate->copy()->startOfMonth();
                            $endOfMonth = $monthDate->copy()->endOfMonth();
                            $firstDayOfWeek = $startOfMonth->dayOfWeekIso;
                            $daysInMonth = $monthDate->daysInMonth;
                        @endphp

                        <div
                            class="isolate mt-2 grid grid-cols-7 gap-px rounded-lg bg-gray-200 text-sm shadow ring-1 ring-gray-200">
                            {{-- Padding for first week --}}
                            @for ($i = 1; $i < $firstDayOfWeek; $i++)
                                <div class="bg-gray-50 py-1.5"></div>
                            @endfor

                            {{-- Loop over days in month --}}
                            @for ($day = 1; $day <= $daysInMonth; $day++)
                                @php
                                    $date = \Carbon\Carbon::create($monthDate->year, $monthDate->month, $day);
                                    $formattedDate = $date->toDateString();
                                    $hasDue = $selectedCompanyId && isset($dueDatesByDay[$formattedDate]);
                                    $cellIndex = $firstDayOfWeek - 1 + $day;
                                    $isFirstCell = $cellIndex === 1;
                                    $isLastCell = $cellIndex === $firstDayOfWeek - 1 + $daysInMonth;
                                @endphp

                                <button type="button" wire:click="showDueItems('{{ $formattedDate }}')"
                                    class="bg-white py-1.5 hover:bg-gray-100 focus:z-10 {{ $isFirstCell ? 'rounded-tl-lg' : '' }} {{ $isLastCell ? 'rounded-br-lg' : '' }}">
                                    <time datetime="{{ $formattedDate }}"
                                        title="{{ $hasDue ? implode(', ', collect($dueDatesByDay[$formattedDate])->pluck('category.name')->toArray()) : '' }}"
                                        class="mx-auto flex size-7 items-center justify-center rounded-full {{ $hasDue ? 'bg-indigo-600 font-semibold text-white cursor-pointer' : '' }}">
                                        {{ $day }}
                                    </time>
                                </button>
                            @endfor
                        </div>
                    </section>
                @endforeach

                @if ($selectedDate && count($selectedDueItems))
                    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
                        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                        <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 relative">
                            <button wire:click="$set('selectedDate', null)"
                                wire:keydown.escape.window="$set('selectedDate', null)"
                                class="absolute top-2 right-2 text-gray-400 hover:text-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>

                            <h2 class="text-lg font-semibold mb-4">Due on
                                {{ \Carbon\Carbon::parse($selectedDate)->format('jS F, Y') }}</h2>

                            <table class="min-w-full text-sm text-left border">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 border-b">Category</th>
                                        <th class="px-4 py-2 border-b">Provider</th>
                                        <th class="px-4 py-2 border-b">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($selectedDueItems as $item)
                                        <tr>
                                            <td class="px-4 py-2 border-b">{{ $item->category->name ?? '-' }}</td>
                                            <td class="px-4 py-2 border-b">{{ $item->provider->name ?? '-' }}</td>
                                            <td class="px-4 py-2 border-b">
                                                {{ Number::currency($item->budget, 'EUR') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                @if ($viewMode === 'month')
                    @php
                        $currentMonthStart = $currentDate->copy()->startOfMonth()->toDateString();
                        $currentMonthEnd = $currentDate->copy()->endOfMonth()->toDateString();

                        $monthlyDueItems = collect($dueDatesByDay)
                            ->filter(fn($items, $date) => $date >= $currentMonthStart && $date <= $currentMonthEnd)
                            ->flatMap(fn($items) => $items)
                            ->sortBy('next_due_date');
                    @endphp
                    <section class="mt-12 md:mt-0 md:pl-14 w-full">
                        <h2 class="text-base font-semibold text-gray-900">Monthly Budget for
                            {{ $currentDate->format('F Y') }}</h2>
                        <ol class="mt-4 flex flex-col gap-y-1 text-sm/6 text-gray-500">
                            @forelse ($monthlyDueItems as $dueItem)
                                <li
                                    class="group flex items-center rounded-xl px-4 py-2 focus-within:bg-gray-100 hover:bg-gray-100">
                                    <div class="flex-auto">
                                        <p class="text-gray-900">{{ $dueItem->category->name ?? '-' }}</p>
                                        <p class="mt-0.5">{{ $dueItem->provider->name ?? '-' }}</p>
                                        <p class="mt-0.5">
                                            {{ \Carbon\Carbon::parse($dueItem->next_due_date)->format('j M, Y') }}</p>
                                        <p class="mt-0.5 font-semibold text-indigo-700">
                                            {{ Number::currency($dueItem->budget, 'EUR') }}</p>
                                    </div>
                                    <div class="relative opacity-0 focus-within:opacity-100 group-hover:opacity-100"
                                        x-data="{ open: false }" @click.away="open = false">
                                        <div>
                                            <button type="button" x-on:click="open = !open"
                                                class="-m-2 flex items-center rounded-full p-1.5 text-gray-500 hover:text-gray-600"
                                                id="menu-0-button" aria-expanded="false" aria-haspopup="true">
                                                <span class="sr-only">Open options</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                    class="size-6 text-gray-400">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                </svg>
                                            </button>
                                        </div>

                                        <div x-show="open" x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="transform opacity-0 scale-95"
                                            x-transition:enter-end="transform opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-75"
                                            x-transition:leave-start="transform opacity-100 scale-100"
                                            x-transition:leave-end="transform opacity-0 scale-95"
                                            class="absolute right-0 z-10 mt-2 w-36 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-none"
                                            role="menu" aria-orientation="vertical"
                                            aria-labelledby="menu-0-button" tabindex="-1">
                                            <div class="py-1" role="none">
                                                <a href="#"
                                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                                    role="menuitem" tabindex="-1" id="menu-0-item-0">Edit</a>
                                                <a href="#"
                                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                                    role="menuitem" tabindex="-1" id="menu-0-item-1">Cancel</a>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li class="text-gray-500 italic">No budget items this month.</li>
                            @endforelse
                        </ol>
                    </section>
                @endif


            </div>
        </div>
    </div>
</div>
