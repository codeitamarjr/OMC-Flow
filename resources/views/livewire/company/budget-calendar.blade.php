<div class="h-full flex flex-col bg-white dark:bg-gray-800 relative overflow-hidden p-4 space-y-6">
    <div class="w-full md:w-1/3">
        <label for="company" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Select Company</label>
        <select wire:model.live="selectedCompanyId" id="company"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            <option value="">-- Choose a company --</option>
            @foreach ($companies as $company)
                <option value="{{ $company->id }}">{{ $company->name }}</option>
            @endforeach
        </select>
    </div>

    @if ($selectedCompanyId)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($monthlyContracts as $month => $contracts)
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded shadow-sm border dark:border-gray-600">
                    <h3 class="text-md font-semibold text-gray-800 dark:text-white mb-2">
                        {{ \Carbon\Carbon::create()->month($month)->format('F') }}
                    </h3>

                    @if (count($contracts) === 0)
                        <p class="text-sm text-gray-500 dark:text-gray-400">No services due.</p>
                    @else
                        <ul class="space-y-1 text-sm text-gray-700 dark:text-gray-300">
                            @foreach ($contracts as $contract)
                                <li class="flex flex-col border-l-2 border-indigo-500 pl-2 mb-2">
                                    <span class="font-medium">{{ $contract->category->name }}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Provider: {{ $contract->provider->name }}</span>
                                    <span class="text-xs text-green-600">€{{ number_format($contract->budget, 2) }} on {{ \Carbon\Carbon::parse($contract->next_due_date)->format('M d') }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
