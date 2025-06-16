<section class="w-full">
    @include('partials.category-heading')

    <x-category.layout :heading="__('Manage Service Categories')" :subheading="__('Create and manage service categories for your OMC budget.')">
        @if (session('success'))
            <div class="mb-4">
                <x-ui.flash-message type="success" title="Success">
                    {{ session('success') }}
                </x-ui.flash-message>
            </div>
        @endif

        <div class="space-y-2">
            @forelse ($categories as $category)
                <div class="flex items-center justify-between border p-3 rounded-xl bg-white dark:bg-gray-800">
                    <div>
                        <div class="font-medium">{{ $category->name }}</div>
                        <div class="text-xs text-gray-500">{{ $category->code }}</div>
                    </div>
                    <div class="flex gap-2">
                        <flux:button wire:click="edit({{ $category->id }})" size="sm" variant="outline">
                            {{ __('Edit') }}
                        </flux:button>
                        <flux:button size="sm" variant="outline" class="!text-red-600 hover:!bg-red-100"
                            wire:click="confirmDelete({{ $category->id }})">
                            {{ __('Delete') }}
                        </flux:button>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">{{ __('No service categories created yet.') }}</p>
            @endforelse

            @if ($showCreateModal)
                <x-ui.modal wire:model="showCreateModal">
                    <div class="mt-3 text-center sm:mt-5">
                        <h3 class="text-base font-semibold text-gray-900">
                            {{ __('Create Category') }}
                        </h3>

                        <div class="mt-4 space-y-4 text-left">
                            <flux:input wire:model.defer="name" :label="__('Category Name')" />
                            <flux:input wire:model.defer="code" :label="__('Code')" />
                            <flux:input wire:model.defer="description" :label="__('Description')" />
                        </div>
                    </div>

                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                        <flux:button type="button" variant="outline" class="w-full"
                            wire:click="$set('showCreateModal', false)">
                            {{ __('Cancel') }}
                        </flux:button>
                        <flux:button type="button" class="w-full" wire:click="create">
                            {{ __('Create Category') }}
                        </flux:button>
                    </div>
                </x-ui.modal>
            @endif

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
                            {{ __('Delete Category') }}
                        </h3>
                        <p class="text-sm text-gray-500 mt-2">
                            {{ __('Are you sure you want to delete ":name"? This action cannot be undone.', ['name' => $confirmingDelete->name]) }}
                        </p>
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

            @if ($showEditModal)
                <x-ui.modal wire:model="showEditModal">
                    <div class="mt-3 text-center sm:mt-5">
                        <h3 class="text-base font-semibold text-gray-900">
                            {{ __('Edit Category') }}
                        </h3>

                        <div class="mt-4 space-y-4 text-left">
                            <flux:input wire:model.defer="editName" :label="__('Category Name')" />
                            <flux:input wire:model.defer="editCode" :label="__('Code')" />
                            <flux:input wire:model.defer="editDescription" :label="__('Description')" />
                        </div>
                    </div>

                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                        <flux:button type="button" variant="outline" class="w-full"
                            wire:click="$set('showEditModal', false)">
                            {{ __('Cancel') }}
                        </flux:button>
                        <flux:button type="button" class="w-full" wire:click="update">
                            {{ __('Update Category') }}
                        </flux:button>
                    </div>
                </x-ui.modal>
            @endif
        </div>
    </x-category.layout>
</section>
