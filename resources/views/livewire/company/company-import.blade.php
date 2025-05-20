<section class="w-full">
    @include('partials.company-heading')

    <x-company.layout heading="Import Companies">

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

        @if ($mode === 'upload')
            <h3 class="text-lg font-bold my-4">Upload File</h3>

            @if ($file)
                <div class="mb-4">
                    <x-ui.flash-message type="success" title="File Selected">
                        {{ $file->getClientOriginalName() }}
                    </x-ui.flash-message>
                </div>
            @endif

            <form wire:submit.prevent="preview" class="space-y-4">

                <div class="flex flex-col gap-0.5">
                    <input type="file" wire:model="file" accept=".xlsx,.csv"
                        class="w-full text-slate-500 font-medium text-sm bg-gray-100 file:cursor-pointer cursor-pointer file:border-0 file:py-2 file:px-4 file:mr-4 file:bg-gray-800 file:hover:bg-gray-700 file:text-white rounded" />
                    <span class="text-sm text-gray-500">File must be in .xlsx or .csv format.</span>
                    <span class="text-sm text-gray-500">Column header must be "company_number","custom" and "tags", in
                        that order, tag's can hold multiple tags separated by a "/".</span>
                    <span class="text-sm text-gray-500">Download a sample file <a href="/files/import-sample.xlsx"
                            class="underline">here</a></span>
                    @error('file')
                        <span class="text-sm text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <flux:button type="submit">Preview Companies</flux:button>
            </form>
        @elseif ($mode === 'preview')
            <h3 class="text-lg font-bold my-4">Preview Companies</h3>



            <div class="relative overflow-x-auto">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                #
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Number
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Name
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Type
                            </th>
                            <th scope="col" class="px-6 py-3">
                                AR Date
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Address
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($previewData as $i => $row)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
                                <th scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $i + 1 }}
                                </th>
                                <td class="px-6 py-4">
                                    {{ $row['number'] }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900 dark:text-white">{{ $row['name'] ?? '-' }}</div>
                                    <span class="text-xs text-gray-500">{{ $row['custom'] ?? '-' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    {{ $row['status'] ?? '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $row['type'] ?? '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $row['next_ar'] ?? '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $row['address'] ?? '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    @if (!$row['valid'])
                                        <span class="text-red-600 text-sm">{{ $row['reason'] }}</span>
                                    @else
                                        @if (in_array($row['number'], $imported))
                                            <span class="text-green-600 text-sm">Imported</span>
                                        @else
                                            <flux:button wire:click="importCompany({{ $i }})"
                                                variant="primary">Add</flux:button>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex gap-3">
                <flux:button wire:click="importAll" variant="primary">Import All</flux:button>
                <flux:button wire:click="$set('mode', 'upload')">Cancel</flux:button>
            </div>
        @elseif ($mode === 'done')
            <x-ui.flash-message type="success">{{ session('success') }}</x-ui.flash-message>

            <div class="mt-4">
                <h3 class="font-bold text-green-700">Imported:</h3>
                <ul class="list-disc ml-5">
                    @foreach ($imported as $num)
                        <li>{{ $num }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="mt-4">
                <h3 class="font-bold text-red-700">Skipped:</h3>
                <ul class="list-disc ml-5">
                    @foreach ($skipped as [$num, $reason])
                        <li>{{ $num }} – {{ $reason }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="mt-6">
                <flux:button wire:click="$set('mode', 'upload')">Import More</flux:button>
            </div>
        @endif

    </x-company.layout>


</section>
