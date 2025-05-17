<section class="w-full">
    @include('partials.company-heading')

    <x-company.layout :heading="__('Edit Company')" :subheading="__('Update details for this company')">
        @if (session('success'))
            <div class="mb-4">
                <x-ui.flash-message type="success" title="Success">
                    {{ session('success') }}
                </x-ui.flash-message>
            </div>
        @endif

        <form wire:submit.prevent="save" class="my-6 w-full space-y-6">
            <flux:input wire:model="company_number" :label="__('Company Number')" required />
            <flux:input wire:model="name" :label="__('Name')" required />
            <flux:input wire:model="company_type" :label="__('Company Type')" />
            <flux:input wire:model="status" :label="__('Status')" />
            <flux:input wire:model="effective_date" :label="__('Effective Date')" type="date" />
            <flux:input wire:model="registration_date" :label="__('Registration Date')" type="date" />
            <flux:input wire:model="last_annual_return" :label="__('Last Annual Return')" type="date" />
            <flux:input wire:model="next_annual_return" :label="__('Next Annual Return')" type="date" />
            <flux:input wire:model="last_accounts" :label="__('Last Accounts')" type="date" />
            <flux:input wire:model="postcode" :label="__('Postcode')" />
            <flux:input wire:model="address_line_1" :label="__('Address Line 1')" />
            <flux:input wire:model="address_line_2" :label="__('Address Line 2')" />
            <flux:input wire:model="address_line_3" :label="__('Address Line 3')" />
            <flux:input wire:model="address_line_4" :label="__('Address Line 4')" />
            <flux:input wire:model="place_of_business" :label="__('Place of Business')" />
            <flux:input wire:model="company_type_code" :label="__('Company Type Code')" />
            <flux:input wire:model="company_status_code" :label="__('Company Status Code')" />

            <div class="flex items-center gap-4">
                <flux:button type="submit" variant="primary">{{ __('Save Changes') }}</flux:button>
            </div>

            <x-action-message on="company-updated">{{ __('Saved.') }}</x-action-message>
        </form>
    </x-company.layout>
</section>
