<section class="w-full">
    @include('partials.contract-heading')

    <x-contract.layout :heading="__('Service Contracts')" :subheading="__('Link providers to OMCs and track budgeted services.')">
        @if (session('success'))
            <div class="mb-4">
                <x-ui.flash-message type="success" title="Success">
                    {{ session('success') }}
                </x-ui.flash-message>
            </div>
        @endif

        <div class="space-y-3">
            @forelse ($contracts as $contract)
                <div class="border p-4 rounded-xl bg-white dark:bg-gray-800" id="contract-{{ $contract->id }}">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="text-sm text-gray-500">{{ $contract->company->name }}</div>
                            <div class="text-lg font-semibold">{{ $contract->category->name }}</div>
                            <div class="text-sm">{{ $contract->provider->name }}</div>
                        </div>
                        <div class="text-right text-sm">
                            <div class="font-medium">€{{ number_format($contract->budget, 2) }}</div>
                            <div class="text-xs text-gray-500">Next Due: {{ $contract->next_due_date ?? '—' }}</div>
                            <div class="text-xs">{{ ucfirst($contract->status) }}</div>
                        </div>
                    </div>
                    <div class="flex gap-2 mt-2 text-right">
                        <flux:button size="sm" variant="outline" wire:click="edit({{ $contract->id }})">
                            {{ __('Edit') }}
                        </flux:button>
                        <flux:button size="sm" variant="outline" class="!text-red-600 hover:!bg-red-100"
                            wire:click="confirmDelete({{ $contract->id }})">
                            {{ __('Delete') }}
                        </flux:button>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">{{ __('No contracts created yet.') }}</p>
            @endforelse
        </div>

        {{-- CREATE MODAL --}}
        @if ($showCreateModal)
            <x-ui.modal wire:model="showCreateModal">
                <div class="mt-3 text-center sm:mt-5">
                    <h3 class="text-base font-semibold text-gray-900">{{ __('Create Service Contract') }}</h3>
                    @include('livewire.company.service.partials.contract-form')
                </div>

                <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3">
                    <flux:button type="button" variant="outline" class="w-full"
                        wire:click="$set('showCreateModal', false)">
                        {{ __('Cancel') }}
                    </flux:button>
                    <flux:button type="button" class="w-full" wire:click="create">
                        {{ __('Save Contract') }}
                    </flux:button>
                </div>
            </x-ui.modal>
        @endif

        @if ($showEditModal)
            <x-ui.modal wire:model="showEditModal">
                <div class="mt-3 text-center sm:mt-5">
                    <h3 class="text-base font-semibold text-gray-900">{{ __('Edit Service Contract') }}</h3>
                    @include('livewire.company.service.partials.contract-form')
                </div>
                <div class="mt-5 sm:grid sm:grid-cols-2 sm:gap-3">
                    <flux:button type="button" variant="outline" class="w-full"
                        wire:click="$set('showEditModal', false)">
                        {{ __('Cancel') }}
                    </flux:button>
                    <flux:button type="button" class="w-full" wire:click="update" wire:keydown.enter="update">
                        {{ __('Update Contract') }}
                    </flux:button>
                </div>
            </x-ui.modal>
        @endif

        @if ($showDeleteModal)
            <x-ui.modal wire:model="showDeleteModal">
                <div class="mx-auto flex size-12 items-center justify-center rounded-full bg-red-100">
                    <svg class="size-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <div class="mt-3 text-center">
                    <h3 class="text-base font-semibold text-gray-900">
                        {{ __('Delete Contract') }}
                    </h3>
                    <p class="text-sm text-gray-500 mt-2">
                        {{ __('Are you sure you want to delete this contract? This action cannot be undone.') }}
                    </p>
                </div>
                <div class="mt-5 sm:grid sm:grid-cols-2 sm:gap-3">
                    <flux:button type="button" variant="outline" class="w-full"
                        wire:click="$set('showDeleteModal', false)">
                        {{ __('Cancel') }}
                    </flux:button>
                    <flux:button type="button" variant="outline" class="!text-red-600 hover:!bg-red-100"
                        wire:click="delete">
                        {{ __('Yes, delete') }}
                    </flux:button>
                </div>
            </x-ui.modal>
        @endif


    </x-contract.layout>
</section>
