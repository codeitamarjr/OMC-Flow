<div class="h-full flex flex-col bg-white dark:bg-gray-800 relative overflow-hidden">
    <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
        <div class="w-full md:w-1/2">
            <label for="company" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Select
                Company</label>
            <select wire:model.live="selectedCompanyId" id="company"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="">-- Choose a company --</option>
                @foreach ($companies as $company)
                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <table class="w-full h-fit text-sm text-left text-gray-500 dark:text-gray-400 mt-4" wire:loading.class="opacity-50">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th class="px-4 py-3">Service</th>
                <th class="px-4 py-3">Provider</th>
                <th class="px-4 py-3">Budget (€)</th>
                <th class="px-4 py-3">Next Due</th>
                <th class="px-4 py-3">Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($contracts as $contract)
                <tr class="border-b dark:border-gray-700" x-data="{ open: false }" @mouseover="open = true"
                    @mouseleave="open = false">
                    <td class="px-4 py-2">{{ $contract->category->name }}</td>
                    <td class="px-4 py-2">{{ $contract->provider->name }}</td>
                    <td class="px-4 py-2 font-medium text-green-600">€{{ number_format($contract->budget, 2) }}</td>
                    <td class="px-4 py-2">{{ $contract->next_due_date ?? '—' }}</td>
                    <td class="px-4 py-2 capitalize">{{ $contract->status }}</td>
                    <td class="px-4 py-2 relative">
                        <div class="flex justify-end items-center h-full">
                            <div class="absolute right-0 inline-flex rounded-md shadow-xs" role="group" x-show="open"
                                x-transition:enter="transition transform ease-out duration-300"
                                x-transition:enter-start="opacity-0 translate-y-full"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition transform ease-in duration-200"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 translate-y-full">
                                <button type="button"
                                    class="inline-flex items-center px-1 py-1 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-s-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-blue-500 dark:focus:text-white"
                                    x-data="{ tooltip: false }" @mouseover="tooltip = true" @mouseleave="tooltip = false">
                                    <div class="relative">
                                        <a href="{{ route('company.service.contract.manager') }}#contract-{{ $contract->id }}"
                                            target="_blank">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-3">
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
                                    class="inline-flex items-center px-1 py-1 text-sm font-medium text-gray-900 bg-white border-t border-b border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-blue-500 dark:focus:text-white">
                                    <svg class="size-3 me-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 20 20">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M4 12.25V1m0 11.25a2.25 2.25 0 0 0 0 4.5m0-4.5a2.25 2.25 0 0 1 0 4.5M4 19v-2.25m6-13.5V1m0 2.25a2.25 2.25 0 0 0 0 4.5m0-4.5a2.25 2.25 0 0 1 0 4.5M10 19V7.75m6 4.5V1m0 11.25a2.25 2.25 0 1 0 0 4.5 2.25 2.25 0 0 0 0-4.5ZM16 19v-2" />
                                    </svg>
                                </button>
                                <button type="button"
                                    class="inline-flex items-center px-1 py-1 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-e-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-blue-500 dark:focus:text-white">
                                    <svg class="size-3 me-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        fill="currentColor" viewBox="0 0 20 20">
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
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-4 text-center text-sm text-gray-500 dark:text-gray-300">
                        No contracts available.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="border-t dark:border-gray-700">
                <td colspan="2" class="px-4 py-2 text-right font-semibold text-gray-700 dark:text-gray-300">Total
                </td>
                <td class="px-4 py-2 font-bold text-green-700 dark:text-green-400">
                    €{{ number_format(collect($contracts)->sum('budget'), 2) }}
                </td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
</div>
