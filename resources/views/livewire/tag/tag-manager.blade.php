<section class="w-full">
    @include('partials.tag-heading')

    <x-tag.layout :heading="__('Manage Tags')" :subheading="__('Create and manage tags for your companies.')">
        @if (session('success'))
            <div class="mb-4">
                <x-ui.flash-message type="success" title="Success">
                    {{ session('success') }}
                </x-ui.flash-message>
            </div>
        @endif

        <div class="space-y-2">
            @forelse ($tags as $tag)
                <div class="flex items-center justify-between border p-3 rounded-xl bg-white dark:bg-gray-800">
                    <div>{{ $tag->name }}</div>
                    <div class="flex gap-2">
                        <flux:button wire:click="edit({{ $tag->id }})" size="sm" variant="outline">
                            {{ __('Edit') }}
                        </flux:button>
                        <flux:button size="sm" variant="outline" class="!text-red-600 hover:!bg-red-100"
                            wire:click="confirmDelete({{ $tag->id }})">
                            {{ __('Delete') }}
                        </flux:button>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">{{ __('No tags created yet.') }}</p>
            @endforelse
            @if ($confirmingDelete)
                <x-ui.modal wire:model="showDeleteModal">
                    <div class="mx-auto flex size-12 items-center justify-center rounded-full bg-red-100">
                        <svg class="size-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-5">
                        <h3 class="text-base font-semibold text-gray-900" id="modal-title">
                            {{ __('Delete Tag') }}
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
            @if ($showEditModal)
                <x-ui.modal wire:model="showEditModal">
                    <div class="mt-3 text-center sm:mt-5">
                        <h3 class="text-base font-semibold text-gray-900">
                            {{ __('Edit Tag') }}
                        </h3>

                        <div class="mt-4 space-y-4 text-left">
                            <flux:input wire:model.defer="editName" :label="__('Tag Name')" />
                        </div>
                    </div>

                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                        <flux:button type="button" variant="outline" class="w-full"
                            wire:click="$set('showEditModal', false)">
                            {{ __('Cancel') }}
                        </flux:button>
                        <flux:button type="button" class="w-full" wire:click="update">
                            {{ __('Update Tag') }}
                        </flux:button>
                    </div>
                </x-ui.modal>
            @endif
        </div>
    </x-tag.layout>
</section>
