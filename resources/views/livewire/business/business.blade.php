<section class="w-full">
    @include('partials.business-heading')

    <x-business.layout :heading="__('Your Businesses')" :subheading="__('View and switch between your businesses')">
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

        <div class="space-y-4 mt-6">
            @forelse ($businesses as $business)
                <div class="p-4 rounded-xl border bg-white dark:bg-gray-900 shadow flex items-center justify-between">
                    <div>
                        <div class="font-semibold">{{ $business->name }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            Role: {{ ucfirst($business->pivot->role ?? 'unknown') }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            Companies: {{ $business->companies()->count() }}
                        </div>
                    </div>

                    @if (auth()->user()->roleInBusiness($business->id) === 'admin')
                        <flux:button size="sm" variant="outline" class="!text-red-600 hover:!bg-red-100"
                            wire:click="confirmDelete({{ $business->id }})">
                            {{ __('Delete') }}
                        </flux:button>
                    @endif

                    @if (auth()->user()->current_business_id === $business->id)
                        <span class="text-sm text-green-600 dark:text-green-400 font-medium">
                            {{ __('Current') }}
                        </span>
                    @else
                        <flux:button wire:click="switchBusiness({{ $business->id }})" size="sm" variant="primary">
                            {{ __('Switch') }}
                        </flux:button>
                    @endif
                </div>
            @empty
                <p class="text-sm text-gray-500">{{ __('You do not belong to any businesses yet.') }}</p>
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

        </div>
    </x-business.layout>
</section>
