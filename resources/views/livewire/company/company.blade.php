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
                        <input type="text" id="simple-search" wire:model.live.debounce.1000ms="search"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Search" required="">
                    </div>
                </div>
                <div
                    class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0">
                    <button type="button"
                        class="flex items-center justify-center text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">
                        <svg class="h-3.5 w-3.5 mr-2" fill="currentColor" viewbox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path clip-rule="evenodd" fill-rule="evenodd"
                                d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                        </svg>
                        Add product
                    </button>
                    <div class="flex items-center space-x-3 w-full md:w-auto">
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

            <div class="relative overflow-x-auto">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400"
                    wire:loading.class="opacity-50">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-2">
                                Reg #
                            </th>
                            <th scope="col" class="px-6 py-2">
                                Company
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
                                    Next AR Due
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-2">
                                AR01 Compliance Status
                            </th>
                            <th scope="col" class="px-6 py-2">
                                Last Filed
                            </th>
                            <th scope="col" class="px-6 py-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($companies as $company)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
                                <th scope="row"
                                    class="px-6 py-1.5 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $company->company_number }}
                                </th>
                                <td class="px-6 py-0.5">
                                    <div class="ml-3">
                                        <p class="text-base font-semibold text-gray-900 dark:text-white">
                                            {{ $company->name }}
                                        </p>
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                            {{ $company->custom }}
                                        </span>
                                        @if ($company->tags->isNotEmpty())
                                            @foreach ($company->tags as $tag)
                                                <span
                                                    class="inline-flex items-center gap-x-1 rounded-full px-1.5 py-1 text-xs font-medium text-gray-900 ring-1 ring-inset ring-gray-200 bg-blue-50 dark:bg-gray-800 dark:text-gray-300 dark:ring-gray-700">
                                                    <svg class="size-1.5 fill-indigo-500" viewBox="0 0 6 6"
                                                        aria-hidden="true">
                                                        <circle cx="3" cy="3" r="3" />
                                                    </svg>
                                                    {{ $tag->name }}
                                                </span>
                                            @endforeach
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-1.5">
                                    <div class="flex items-center">
                                        <span class="ml-2 text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $company->next_annual_return }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-1.5" x-data="{ tooltip: false }">
                                    @php
                                        $statusClasses = [
                                            'Overdue' => 'bg-red-100 text-red-700',
                                            'Due Soon' => 'bg-yellow-100 text-yellow-700',
                                            'Compliant' => 'bg-green-100 text-green-700',
                                        ];
                                    @endphp
                                    <span
                                        class="inline-flex items-center gap-x-1.5 rounded-md px-1.5 py-0.5 text-xs font-medium 
                                            {{ $statusClasses[$company->ar_status] ?? $statusClasses['Compliant'] }}">
                                        {{-- Tooltip --}}
                                        {{-- Tooltip Trigger --}}
                                        <div @mouseover="tooltip = true" @mouseleave="tooltip = false"
                                            class="relative">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                class="size-4 cursor-pointer">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                                            </svg>

                                            {{-- Tooltip Box --}}
                                            <div x-show="tooltip"
                                                x-transition:enter="transition ease-out duration-500"
                                                x-transition:enter-start="opacity-0 translate-y-1"
                                                x-transition:enter-end="opacity-100 translate-y-0"
                                                x-transition:leave="transition ease-in duration-150"
                                                x-transition:leave-start="opacity-100 translate-y-0"
                                                x-transition:leave-end="opacity-0 translate-y-1"
                                                class="absolute left-1/2 top-full mt-2 -translate-x-1/2 z-50 w-[200px] rounded-lg bg-gray-700 py-1.5 px-3 font-sans text-sm font-normal text-white text-center">
                                                @if ($company->ar_status === 'Overdue')
                                                    This company is overdue
                                                    {{ round(-now()->diffInDays($company->next_annual_return)) }} days
                                                    for its annual return filing.
                                                @elseif ($company->ar_status === 'Due Soon')
                                                    This company is due in less than 30 days for its annual return
                                                    filing soon.
                                                @else
                                                    This company is compliant with its annual return filing.
                                                @endif
                                            </div>
                                        </div>
                                        {{ $company->ar_status }}
                                    </span>
                                </td>
                                <td class="px-6 py-1.5">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                        {{ $company->last_filed }}
                                    </span>
                                </td>
                                <td class="px-6 py-1.5">
                                    <div wire:click="showCompanySubmissions({{ $company->id }})">
                                        <div
                                            class="relative inline-flex items-center p-3 text-sm font-medium text-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
                                            </svg>
                                            <span class="sr-only">Notifications</span>
                                            <div
                                                class="absolute inline-flex items-center justify-center size-6 text-xs font-bold text-white bg-blue-500 border-2 border-white rounded-full -top-0.5 -end-0.5 dark:border-gray-900">
                                                {{ $company->submissionDocuments->count() }}
                                            </div>
                                        </div>


                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if ($selectedCompany)
                    <x-ui.modal wire:model="showDetailsModal" maxWidth="7xl">
                        <div class="mb-4">
                            <h2 class="text-lg font-semibold text-gray-800 dark:text-white">
                                Submissions for {{ $selectedCompany->name }}
                            </h2>
                        </div>

                        @if ($selectedCompany->submissionDocuments->isEmpty())
                            <p class="text-gray-500 dark:text-gray-300">No submissions found.</p>
                        @else
                            <div class="overflow-x-auto max-h-[80vh] w-full">
                                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                    <thead class="bg-gray-100 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-4 py-2">Form</th>
                                            <th class="px-4 py-2">Received</th>
                                            <th class="px-4 py-2">Effective</th>
                                            <th class="px-4 py-2">Deadline</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($selectedCompany->submissionDocuments as $doc)
                                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                                <td class="px-4 py-2 whitespace-nowrap">
                                                    {{ trim(\Illuminate\Support\Str::before($doc->sub_type_desc, '-')) }}
                                                </td>
                                                <td class="px-4 py-2">{{ $doc->sub_received_date }}</td>
                                                <td class="px-4 py-2">{{ $doc->sub_effective_date ?? '—' }}</td>
                                                <td class="px-4 py-2">
                                                    {{ $doc->deadline }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        <div class="mt-5 text-right">
                            <flux:button wire:click="$set('showDetailsModal', false)">
                                Close
                            </flux:button>
                        </div>
                    </x-ui.modal>
                @endif
            </div>
            <div class="p-4">
                {{ $companies->links() }}
            </div>
        </div>
    </div>
