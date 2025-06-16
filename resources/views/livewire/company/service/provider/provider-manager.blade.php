<section class="w-full">
    @include('partials.provider-heading')

    <x-provider.layout :heading="__('Manage Service Providers')" :subheading="__('Create and manage service providers for your OMC budget.')">

        @if (session('success'))
            <div class="mb-4">
                <x-ui.flash-message type="success" title="Success">
                    {{ session('success') }}
                </x-ui.flash-message>
            </div>
        @endif

        <div class="space-y-2">
            @forelse ($providers as $provider)
                <div class="flex items-center justify-between border p-3 rounded-xl bg-white dark:bg-gray-800">
                    <div>
                        <div class="font-medium">{{ $provider->name }}</div>
                        <div class="text-xs text-gray-500">
                            {{ $provider->email ?? 'No email' }} | {{ $provider->phone ?? 'No phone' }}
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <flux:button wire:click="edit({{ $provider->id }})" size="sm" variant="outline">
                            {{ __('Edit') }}
                        </flux:button>
                        <flux:button size="sm" variant="outline" class="!text-red-600 hover:!bg-red-100"
                            wire:click="confirmDelete({{ $provider->id }})">
                            {{ __('Delete') }}
                        </flux:button>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">{{ __('No providers added yet.') }}</p>
            @endforelse

            {{-- CREATE MODAL --}}
            @if ($showCreateModal)
                <x-ui.modal wire:model="showCreateModal">
                    <div class="mt-3 text-center sm:mt-5">
                        <h3 class="text-base font-semibold text-gray-900">
                            {{ __('Create Provider') }}
                        </h3>

                        <div class="mt-4 space-y-4 text-left">
                            <flux:input wire:model.defer="name" :label="__('Name')" />
                            <flux:input wire:model.defer="contact_name" :label="__('Contact Person')" />
                            <flux:input wire:model.defer="email" :label="__('Email')" />
                            <flux:input wire:model.defer="phone" :label="__('Phone')" />
                            <flux:input wire:model.defer="website" :label="__('Website')" />
                            <flux:input wire:model.defer="address" :label="__('Address')" />
                            <flux:input wire:model.defer="notes" :label="__('Notes')" />

                            {{-- Dropdown for categories if needed in the future --}}
                            <flux:select wire:model.defer="category_id" :label="__('Primary Category')">
                                <option value="">{{ __('Select Category') }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </flux:select>
                        </div>
                    </div>

                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                        <flux:button type="button" variant="outline" class="w-full"
                            wire:click="$set('showCreateModal', false)">
                            {{ __('Cancel') }}
                        </flux:button>
                        <flux:button type="button" class="w-full" wire:click="create">
                            {{ __('Create Provider') }}
                        </flux:button>
                    </div>
                </x-ui.modal>
            @endif

            {{-- EDIT MODAL --}}
            @if ($showEditModal)
                <x-ui.modal wire:model="showEditModal">
                    <div class="mt-3 text-center sm:mt-5">
                        <h3 class="text-base font-semibold text-gray-900">
                            {{ __('Edit Provider') }}
                        </h3>

                        <div class="mt-4 space-y-4 text-left">
                            <div>
                                <label for="category_ids" class="block text-sm font-medium text-gray-700">
                                    {{ __('Service Categories') }}
                                </label>
                                <select wire:model="category_ids" id="category_ids" multiple
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-400">
                                    {{ __('Hold Ctrl (Cmd on Mac) to select multiple categories.') }}
                                </p>
                            </div>
                            <flux:input wire:model.defer="editName" :label="__('Name')" />
                            <flux:input wire:model.defer="editContactName" :label="__('Contact Person')" />
                            <flux:input wire:model.defer="editEmail" :label="__('Email')" />
                            <flux:input wire:model.defer="editPhone" :label="__('Phone')" />
                            <flux:input wire:model.defer="editWebsite" :label="__('Website')" />
                            <flux:input wire:model.defer="editAddress" :label="__('Address')" />
                            <flux:input wire:model.defer="editNotes" :label="__('Notes')" />
                        </div>
                    </div>

                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                        <flux:button type="button" variant="outline" class="w-full"
                            wire:click="$set('showEditModal', false)">
                            {{ __('Cancel') }}
                        </flux:button>
                        <flux:button type="button" class="w-full" wire:click="update">
                            {{ __('Update Provider') }}
                        </flux:button>
                    </div>
                </x-ui.modal>
            @endif

            {{-- DELETE MODAL --}}
            @if ($confirmingDelete)
                <x-ui.modal wire:model="showDeleteModal">
                    <div class="mx-auto flex size-12 items-center justify-center rounded-full bg-red-100">
                        <svg class="size-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-5">
                        <h3 class="text-base font-semibold text-gray-900">
                            {{ __('Delete Provider') }}
                        </h3>
                        <p class="text-sm text-gray-500 mt-2">
                            {{ __('Are you sure you want to delete ":name"? This action cannot be undone.', ['name' => $confirmingDelete->name]) }}
                        </p>
                    </div>
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                        <flux:button type="button" variant="outline" class="w-full"
                            wire:click="$set('showDeleteModal', false); $set('confirmingDelete', null);">
                            {{ __('Cancel') }}
                        </flux:button>
                        <flux:button type="button" variant="outline" class="!text-red-600 hover:!bg-red-100"
                            wire:click="delete">
                            {{ __('Yes, delete') }}
                        </flux:button>
                    </div>
                </x-ui.modal>
            @endif
        </div>
    </x-provider.layout>
</section>
