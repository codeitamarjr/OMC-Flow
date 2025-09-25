<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
        {{-- Flash messages --}}
        @if (session('success'))
            <div class="mb-4">
                <x-ui.flash-message type="success" title="Success">
                    {{ session('success') }}
                </x-ui.flash-message>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4">
                <x-ui.flash-message type="error" title="Error">
                    {{ session('error') }}
                </x-ui.flash-message>
            </div>
        @endif

        <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
            {{-- <div class="w-full md:w-1/2">
                <label for="company" class="hidden">Company</label>
                <div class="grid grid-cols-1">
                    <select wire:model.live="selectedCompanyId" id="company"
                        class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white py-1.5 pl-3 pr-8 text-base text-gray-900 outline outline-1 -outline-offset-1 outline-gray-300 focus-visible:outline focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-indigo-600 sm:text-sm/6 dark:bg-white/5 dark:text-white dark:outline-white/10 dark:*:bg-gray-800 dark:focus-visible:outline-indigo-500">
                        <option value="">-- Select a company --</option>
                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                    </select>
                    <svg viewBox="0 0 16 16" fill="currentColor" data-slot="icon" aria-hidden="true"
                        class="pointer-events-none col-start-1 row-start-1 mr-2 size-5 self-center justify-self-end text-gray-500 sm:size-4 dark:text-gray-400">
                        <path
                            d="M4.22 6.22a.75.75 0 0 1 1.06 0L8 8.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 7.28a.75.75 0 0 1 0-1.06Z"
                            clip-rule="evenodd" fill-rule="evenodd" />
                    </svg>
                </div>
            </div> --}}
            <div class="w-full md:w-1/2">
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
                    <input type="text" id="simple-search" wire:model.live.debounce.1500ms="search"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        placeholder="Search" required="">
                </div>
            </div>
            <div
                class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0">
                <div class="flex items-center space-x-3 w-full md:w-auto">
                    {{-- Actions --}}
                    <div class="relative">
                        <button id="actionsDropdownButton" data-dropdown-toggle="actionsDropdown"
                            class="w-full md:w-auto flex items-center justify-center py-2 px-4 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 cursor-not-allowed"
                            type="button">
                            <svg class="-ml-1 mr-1.5 w-5 h-5" fill="currentColor" viewbox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path clip-rule="evenodd" fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                            </svg>
                            Actions
                        </button>
                        <div id="actionsDropdown"
                            class="hidden z-10 w-44 bg-white rounded divide-y divide-gray-100 shadow dark:bg-gray-700 dark:divide-gray-600">
                            <ul class="py-1 text-sm text-gray-700 dark:text-gray-200"
                                aria-labelledby="actionsDropdownButton">
                                <li>
                                    <a href="#"
                                        class="block py-2 px-4 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Mass
                                        Edit</a>
                                </li>
                            </ul>
                            <div class="py-1">
                                <a href="#"
                                    class="block py-2 px-4 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">Delete
                                    all</a>
                            </div>
                        </div>
                    </div>

                    {{-- Filter --}}
                    <div class="relative" x-data="{ showDropDown: false }">
                        <button id="filterDropdownButton" @click="showDropDown = !showDropDown"
                            data-dropdown-toggle="filterDropdown"
                            class="w-full md:w-auto flex items-center justify-center py-2 px-4 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                            type="button">
                            <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true"
                                class="h-4 w-4 mr-2 text-gray-400" viewbox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z"
                                    clip-rule="evenodd" />
                            </svg>
                            Filter
                            @php
                                $selectedNames = collect($allTags)
                                    ->whereIn('id', $selectedTagFilters)
                                    ->pluck('name')
                                    ->toArray();
                            @endphp
                            @if (count($selectedNames))
                                ({{ implode(', ', $selectedNames) }})
                            @endif
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
                                            <input type="checkbox" value="{{ $tag->id }}"
                                                id="tag-{{ $tag->id }}" wire:model.live="selectedTagFilters"
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
            <table class="w-full h-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400"
                wire:loading.class="opacity-50">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-2" wire:click="sort('company_number')">
                            <div class="flex items-center">
                                @if ($sortBy === 'company_number')
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor"
                                        class="size-6 duration-400 transform  ease-in-out
                                @if ($sortDirection === 'asc' && $sortBy === 'company_number') rotate-180 @endif">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m15 11.25-3-3m0 0-3 3m3-3v7.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                @endif
                                CRO
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-2" wire:click="sort('custom')">
                            <div class="flex items-center">
                                @if ($sortBy === 'custom')
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor"
                                        class="size-6 duration-400 transform  ease-in-out
                                @if ($sortDirection === 'asc' && $sortBy === 'custom') rotate-180 @endif">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m15 11.25-3-3m0 0-3 3m3-3v7.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                @endif
                                Custom Name
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-2" wire:click="sort('next_annual_return')">
                            <div class="flex items-center">
                                @if ($sortBy === 'next_annual_return')
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor"
                                        class="size-6 duration-400 transform  ease-in-out
                                @if ($sortDirection === 'asc' && $sortBy === 'next_annual_return') rotate-180 @endif">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m15 11.25-3-3m0 0-3 3m3-3v7.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                @endif
                                Service
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-2" wire:click="sort('last_agm')">
                            <div class="flex items-center">
                                @if ($sortBy === 'last_agm')
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor"
                                        class="size-6 duration-400 transform  ease-in-out
                                @if ($sortDirection === 'asc' && $sortBy === 'last_agm') rotate-180 @endif">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m15 11.25-3-3m0 0-3 3m3-3v7.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                @endif
                                Supplier
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-2" wire:click="sort('financial_year_end')">
                            <div class="flex items-center">
                                @if ($sortBy === 'financial_year_end')
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor"
                                        class="size-6 duration-400 transform  ease-in-out
                                @if ($sortDirection === 'asc' && $sortBy === 'financial_year_end') rotate-180 @endif">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m15 11.25-3-3m0 0-3 3m3-3v7.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                @endif
                                Budget
                            </div>
                        </th>
                        <th scope="col"></th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($companies as $company)
                        @foreach ($company->contracts as $contract)
                            <tr class="border-b dark:border-gray-700" x-data="{ open: false }"
                                @mouseover="open = true" @mouseleave="open = false">
                                <td class="px-4 py-2">{{ $contract->company->company_number }}</td>
                                <td class="px-4 py-2">{{ $contract->company->custom ?? $contract->company->name }}</td>
                                <td class="px-4 py-2">{{ $contract->category->name }}</td>
                                <td class="px-4 py-2">{{ $contract->provider->name }}</td>
                                <td class="px-4 py-2 font-medium text-green-600">
                                    €{{ number_format($contract->budget, 2) }}
                                </td>
                                <td class="px-4 py-2">{{ $contract->end_date ?? '—' }}</td>
                                <td class="px-4 py-2 capitalize">{{ $contract->status }}</td>
                                <td class="relative">
                                    <div class="flex justify-end items-center h-full">
                                        <div class="absolute right-2 inline-flex rounded-md shadow-xs" role="group"
                                            x-show="open"
                                            x-transition:enter="transition transform ease-out duration-300"
                                            x-transition:enter-start="opacity-0 translate-y-full"
                                            x-transition:enter-end="opacity-100 translate-y-0"
                                            x-transition:leave="transition transform ease-in duration-200"
                                            x-transition:leave-start="opacity-100 translate-y-0"
                                            x-transition:leave-end="opacity-0 translate-y-full">
                                            <button type="button"
                                                class="inline-flex items-center px-1 py-1 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-s-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-blue-500 dark:focus:text-white"
                                                x-data="{ tooltip: false }" @mouseover="tooltip = true"
                                                @mouseleave="tooltip = false">
                                                <div class="relative">
                                                    <a href="{{ route('company.service.contract.manager') }}#contract-{{ $contract->id }}"
                                                        target="_blank">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5"
                                                            stroke="currentColor" class="size-3">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                        </svg>
                                                        <div x-show="tooltip" x-init="setTimeout(() => tooltip = false, 2000)"
                                                            x-transform.transition.origin.top.left
                                                            class="absolute left-1/2 top-full mt-1 -translate-x-1/2 z-50 whitespace-normal break-words rounded-lg bg-black py-1.5 px-3 font-sans text-sm font-normal text-white shadow-lg">
                                                            {{ __('Edit Contract') }}
                                                        </div>
                                                    </a>
                                                </div>
                                            </button>
                                            <button type="button"
                                                class="inline-flex items-center px-1 py-1 text-sm font-medium text-gray-900 bg-white border-t border-b border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-blue-500 dark:focus:text-white"
                                                x-data="{ tooltip: false }" @mouseover="tooltip = true"
                                                @mouseleave="tooltip = false">
                                                <div class="relative">
                                                    <a href="{{ route('company.service.supplier.manager') }}#provider-{{ $contract->provider->id }}"
                                                        target="_blank">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5"
                                                            stroke="currentColor" class="size-3 me-1">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                        </svg>
                                                        <div x-show="tooltip" x-init="setTimeout(() => tooltip = false, 2000)"
                                                            x-transform.transition.origin.top.left
                                                            class="absolute left-1/2 top-full mt-1 -translate-x-1/2 z-50 whitespace-normal break-words rounded-lg bg-black py-1.5 px-3 font-sans text-sm font-normal text-white shadow-lg">
                                                            {{ __('View Provider') }}
                                                        </div>
                                                    </a>
                                                </div>
                                            </button>
                                            <button type="button"
                                                class="inline-flex items-center px-1 py-1 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-e-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-blue-500 dark:focus:text-white">
                                                <svg class="size-3 me-1" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path
                                                        d="M14.707 7.793a1 1 0 0 0-1.414 0L11 10.086V1.5a1 1 0 0 0-2 0v8.586L6.707 7.793a1 1 0 1 0-1.414 1.414l4 4a1 1 0 0 0 1.416 0l4-4a1 1 0 0 0-.002-1.414Z" />
                                                    <path
                                                        d="M18 12h-2.55l-2.975 2.975a3.5 3.5 0 0 1-4.95 0L4.55 12H2a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2Zm-3 5a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>

        </div>
        <div class="p-4">
            {{ $companies->links() }}
        </div>
    </div>
</div>
