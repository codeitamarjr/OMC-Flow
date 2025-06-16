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
            </tr>
        </thead>
        <tbody>
            @forelse ($contracts as $contract)
                <tr class="border-b dark:border-gray-700">
                    <td class="px-4 py-2">{{ $contract->category->name }}</td>
                    <td class="px-4 py-2">{{ $contract->provider->name }}</td>
                    <td class="px-4 py-2 font-medium text-green-600">€{{ number_format($contract->budget, 2) }}</td>
                    <td class="px-4 py-2">{{ $contract->next_due_date ?? '—' }}</td>
                    <td class="px-4 py-2 capitalize">{{ $contract->status }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-4 text-center text-sm text-gray-500 dark:text-gray-300">
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
