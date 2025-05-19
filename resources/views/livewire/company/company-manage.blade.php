<section class="w-full">
    @include('partials.company-heading')

    <x-company.layout :heading="__('Companies')" :subheading="__('All companies under your current business')">
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


        <div class="mt-6 space-y-4">
            <table class="w-full text-left table-auto min-w-max">
                <thead>
                    <tr>
                        <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50">
                            <p
                                class="block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70">
                                Name
                            </p>
                        </th>
                        <th class="border-b border-blue-gray-100 bg-blue-gray-50">
                            <p
                                class="block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70">
                                Registration Number
                            </p>
                        </th>
                        <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50">
                            <p
                                class="block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70">
                                Status
                            </p>
                        </th>
                        <th class="p-4 border-b">
                        </th>
                        <th class="p-4 border-b"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($companies as $company)
                        <tr>
                            <td class="p-3 border-b border-blue-gray-50">
                                <div class="font-medium text-gray-900">
                                    <p class="text-ellipsis whitespace-nowrap max-w-[350px] overflow-hidden">
                                        {{ $company->name }}
                                    </p>
                                    <span class="text-gray-500 text-sm">
                                        {{ $company->custom }}
                                    </span>
                                </div>
                                <div class="mt-1.5 mb-1 text-gray-500 text-sm">
                                    {{ $company->company_type }}
                                </div>
                                <span class="text-gray-500 text-sm">
                                    <div x-data="{
                                        open: false,
                                        search: '',
                                        selected: @entangle('selectedTags.' . $company->id),
                                        allTags: @js($allTags),
                                        toggle(id) {
                                            if (this.selected.includes(id)) {
                                                this.selected = this.selected.filter(i => i !== id);
                                            } else {
                                                this.selected.push(id);
                                            }
                                            $wire.call('updateTags', this.selected, {{ $company->id }});
                                        },
                                        isSelected(id) {
                                            return this.selected.includes(id);
                                        },
                                        filteredTags() {
                                            return this.allTags.filter(tag =>
                                                tag.name.toLowerCase().includes(this.search.toLowerCase())
                                            );
                                        }
                                    }" class="relative w-full">
                                        <div class="flex items-center justify-between">
                                            <!-- Selected Tag Pills -->
                                            <div class="flex flex-wrap gap-1">
                                                <template x-for="tagId in selected" :key="tagId">
                                                    <span
                                                        class="inline-flex items-center gap-x-1 rounded-full px-1.5 py-1 text-xs font-medium text-gray-900 ring-1 ring-inset ring-gray-200">
                                                        <svg class="size-1.5 fill-indigo-500" viewBox="0 0 6 6"
                                                            aria-hidden="true">
                                                            <circle cx="3" cy="3" r="3" />
                                                        </svg>
                                                        <span x-text="allTags.find(t => t.id === tagId)?.name"></span>
                                                        <button @click.prevent="toggle(tagId)"
                                                            class="group relative size-3.5 rounded-lg hover:bg-blue-500/20"
                                                            title="Remove tag">
                                                            <span class="sr-only">Remove</span>
                                                            <svg viewBox="0 0 14 14"
                                                                class="size-3.5 stroke-blue-700/50 group-hover:stroke-blue-700/75">
                                                                <path d="M4 4l6 6m0-6l-6 6" />
                                                            </svg>
                                                            <span class="absolute -inset-1"></span>
                                                        </button>
                                                    </span>
                                                </template>
                                            </div>
                                        </div>
                                </span>
                            </td>
                            <td class="border-b border-blue-gray-50">
                                <p
                                    class="mb-4 block font-sans text-sm antialiased font-normal leading-normal text-blue-gray-900">
                                    {{ $company->company_number }}
                                </p>
                            </td>
                            <td class="mb-4 border-b border-blue-gray-50">
                                <p
                                    class="block font-sans text-sm antialiased font-normal leading-normal text-blue-gray-900">
                                    {{ $company->status ?? 'N/A' }}
                                </p>
                            </td>
                            <td class="p-4 border-b border-blue-gray-50" x-data="{ showInput: false }">
                                <div x-data="{
                                    open: false,
                                    search: '',
                                    selected: @entangle('selectedTags.' . $company->id),
                                    allTags: @js($allTags),
                                    toggle(id) {
                                        if (this.selected.includes(id)) {
                                            this.selected = this.selected.filter(i => i !== id);
                                        } else {
                                            this.selected.push(id);
                                        }
                                        $wire.call('updateTags', this.selected, {{ $company->id }});
                                    },
                                    isSelected(id) {
                                        return this.selected.includes(id);
                                    },
                                    filteredTags() {
                                        return this.allTags.filter(tag =>
                                            tag.name.toLowerCase().includes(this.search.toLowerCase())
                                        );
                                    }
                                }" class="relative w-full">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center mt-2" @click="showInput = !showInput">
                                            <span class="text-sm text-gray-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                    class="size-6 text-gray-500">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M6 6h.008v.008H6V6Z" />
                                                </svg>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Search Input -->
                                    <input type="text" x-model="search" @focus="open = true"
                                        @click.away="open = false" placeholder="Add tags..."
                                        class="w-full border rounded-md p-2 text-sm" x-show="showInput" x-cloak />

                                    <!-- Dropdown -->
                                    <div x-show="open" x-cloak
                                        class="absolute z-10 mt-1 w-full bg-white border rounded-md shadow max-h-40 overflow-y-auto">
                                        <template x-for="tag in filteredTags()" :key="tag.id">
                                            <div @click="toggle(tag.id)"
                                                class="px-3 py-2 cursor-pointer hover:bg-blue-50 flex justify-between items-center">
                                                <span x-text="tag.name"></span>
                                                <svg x-show="isSelected(tag.id)" class="w-4 h-4 text-blue-500"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 border-b border-blue-gray-50">
                                <a href="{{ route('company.edit', $company->id) }}">
                                    <flux:button size="sm" variant="outline"
                                        class="!text-blue-600 hover:!bg-blue-100">
                                        {{ __('Edit') }}
                                    </flux:button>
                                </a>
                                @if (auth()->user()->roleInCurrentBusiness() === 'admin' && $company->id !== auth()->user()->current_company_id)
                                    <flux:button size="sm" variant="outline"
                                        class="!text-red-600 hover:!bg-red-100"
                                        wire:click="confirmDelete({{ $company->id }})">
                                        {{ __('Delete') }}
                                    </flux:button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-4 text-center">
                                <p
                                    class="block font-sans text-sm antialiased font-normal leading-normal text-blue-gray-900">
                                    {{ __('No companies found.') }}
                                </p>
                            </td>
                        </tr>
                    @endforelse
                    @if ($confirmingDelete)
                        <x-ui.modal wire:model="showDeleteModal">
                            <div class="mx-auto flex size-12 items-center justify-center rounded-full bg-red-100">
                                <svg class="size-6 text-red-600" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-5">
                                <h3 class="text-base font-semibold text-gray-900" id="modal-title">
                                    {{ __('Delete Business') }}
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        {{ __('Are you sure you want to delete ":name"? This action cannot be undone.', ['name' => $confirmingDelete->name]) }}
                                    </p>
                                </div>
                            </div>
                            <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                                <flux:button type="button" variant="outline" class="w-full"
                                    wire:click=" $set('showDeleteModal', false); $set('confirmingDelete', null);">
                                    {{ __('Cancel') }}
                                </flux:button>
                                <flux:button type="button" variant="outline" class="!text-red-600 hover:!bg-red-100"
                                    wire:click="delete">
                                    {{ __('Yes, delete') }}
                                </flux:button>
                            </div>
                        </x-ui.modal>
                    @endif
                </tbody>
            </table>
        </div>
    </x-company.layout>
</section>
