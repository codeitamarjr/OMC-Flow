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
                <div class="border p-4 rounded-xl bg-white dark:bg-gray-800">
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
                    <div class="mt-4 space-y-4 text-left">
                        <flux:select wire:model.defer="company_id" :label="__('Company')">
                            <option value="">{{ __('Select Company') }}</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </flux:select>

                        <flux:select wire:model.live="service_category_id" :label="__('Service Category')">
                            <option value="">{{ __('Select Category') }}</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </flux:select>

                        <flux:select wire:model.defer="service_provider_id" :label="__('Service Provider')">
                            <option value="">{{ __('Select Provider') }}</option>
                            @foreach ($providers as $provider)
                                <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                            @endforeach
                        </flux:select>

                        <flux:input wire:model.defer="budget" :label="__('Budget (€)')" type="number" step="0.01" />
                        <flux:input wire:model.defer="start_date" :label="__('Start Date')" type="date" />
                        <flux:input wire:model.defer="next_due_date" :label="__('Next Due Date')" type="date" />
                        <flux:select wire:model.defer="status" :label="__('Status')">
                            <option value="active">{{ __('Active') }}</option>
                            <option value="inactive">{{ __('Inactive') }}</option>
                            <option value="terminated">{{ __('Terminated') }}</option>
                        </flux:select>
                        <flux:input wire:model.defer="notes" :label="__('Notes')" />
                    </div>
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
    </x-contract.layout>
</section>
