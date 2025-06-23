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

            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                Name
                            </th>
                            <th scope="col" class="px-6 py-3">
                                <span class="sr-only">Edit</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($contracts as $contract)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600"
                                x-data x-init="if (window.location.hash === '#contract-{{ $contract->id }}') $el.classList.add('alerts-border')" id="contract-{{ $contract->id }}">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                    {{ $contract->company->name }}
                                    <span class="text-xs text-gray-500">
                                        ({{ $contract->category->name }}, {{ $contract->provider->name }})
                                    </span>
                                    @foreach ($contract->reminders as $reminder)
                                        <div class="flex items-center gap-2">
                                            <div class="text-xs text-gray-500">{{ $reminder->title }} -
                                                {{ \Carbon\Carbon::parse($reminder->due_date)->format('j M, Y') }}</div>
                                            <flux:button size="xs" variant="outline"
                                                wire:click="editReminder({{ $reminder->id }})">
                                                {{ __('Edit Reminders') }}
                                            </flux:button>
                                        </div>
                                    @endforeach
                                    <flux:button size="sm" variant="outline"
                                        wire:click="openReminderModal({{ $contract->id }})">
                                        {{ __('Add Reminder') }}
                                    </flux:button>
                                </th>
                                <td class="px-6 py-4 text-right flex items-center justify-end space-x-2">
                                    <flux:button size="sm" variant="outline"
                                        wire:click="edit({{ $contract->id }})">
                                        {{ __('Edit') }}
                                    </flux:button>
                                    <flux:button size="sm" variant="outline"
                                        class="!text-red-600 hover:!bg-red-100"
                                        wire:click="confirmDelete({{ $contract->id }})">
                                        {{ __('Delete') }}
                                    </flux:button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                    {{ __('No contracts added yet.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
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

        {{-- EDIT MODAL --}}
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

        {{-- DELETE MODAL --}}
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

        {{-- REMINDER MODAL --}}
        @if ($showReminderModal)
            <x-ui.modal wire:model="showReminderModal">
                <div class="mt-3 text-center sm:mt-5">
                    <h3 class="text-base font-semibold text-gray-900">
                        {{ $reminder_id ? __('Edit Reminder') : __('Add Contract Reminder') }}
                    </h3>

                    <div class="mt-4 space-y-4 text-left">
                        <flux:input wire:model.defer="reminder_title" :label="__('Title')" />
                        <flux:input wire:model.defer="reminder_due_date" type="date" :label="__('First Due Date')" />

                        <flux:select wire:model.defer="reminder_frequency" :label="__('Frequency')">
                            <option value="manual">{{ __('Manual') }}</option>
                            <option value="weekly">{{ __('Weekly') }}</option>
                            <option value="biweekly">{{ __('Biweekly') }}</option>
                            <option value="semimonthly">{{ __('Semimonthly') }}</option>
                            <option value="monthly">{{ __('Monthly') }}</option>
                            <option value="bimonthly">{{ __('Bimonthly') }}</option>
                            <option value="threemonthly">{{ __('Three-Monthly') }}</option>
                            <option value="quarterly">{{ __('Quarterly') }}</option>
                            <option value="yearly">{{ __('Yearly') }}</option>
                            <option value="once">{{ __('Once') }}</option>
                        </flux:select>

                        <flux:input wire:model.defer="reminder_day_of_month" type="number" :label="__('Day of Month')"
                            min="1" max="31" />

                        <flux:input wire:model.defer="reminder_months_active_string"
                            :label="__('Months Active (comma-separated)')" placeholder="e.g. 1,2,3,4,5,6" />
                        <flux:input wire:model.defer="reminder_custom_dates_string"
                            :label="__('Custom Dates (comma-separated)')" placeholder="e.g. 2025-01-01,2025-04-15" />

                        <flux:input wire:model.defer="reminder_days_before" type="number"
                            :label="__('Reminder Days Before')" min="0" />
                        <flux:input wire:model.defer="reminder_days_after" type="number"
                            :label="__('Reminder Days After')" min="0" />

                        <flux:input wire:model.defer="reminder_notes" :label="__('Notes')" />
                    </div>
                </div>

                <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                    <flux:button type="button" variant="outline" class="w-full"
                        wire:click="$set('showReminderModal', false)">
                        {{ __('Cancel') }}
                    </flux:button>
                    <flux:button type="button" class="w-full" wire:click="saveReminder">
                        {{ __('Save Reminder') }}
                    </flux:button>
                </div>
            </x-ui.modal>
        @endif

    </x-contract.layout>
    <style>
        .alerts-border {
            background-color: rgba(28, 46, 203, 0.1);

            animation: blink 0.8s ease-in-out;
            animation-iteration-count: 5;
            animation-fill-mode: forwards;

            box-shadow: 0 0 10px rgba(28, 46, 203, 0.5);
        }

        @keyframes blink {
            50% {
                background-color: rgba(255, 255, 255, 0.5);
            }
        }
    </style>
</section>
