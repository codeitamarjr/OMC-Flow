<section class="w-full">
    @include('partials.business-heading')

    <x-business.layout :heading="__('Business')" :subheading="__('Create your business to begin tracking companies')">
        @if (session('success'))
            <div class="mb-4">
                <x-ui.flash-message type="success" title="Success">
                    {{ session('success') }}
                </x-ui.flash-message>
            </div>
        @endif

        <form wire:submit.prevent="createBusiness" class="my-6 w-full space-y-6">
            <flux:input wire:model="name" :label="__('Business Name')" type="text" required autofocus
                autocomplete="organization" />

            <flux:input wire:model="email" :label="__('Business Email')" type="email" required autocomplete="email" />

            <flux:input wire:model="phone" :label="__('Business Phone')" type="tel" required autocomplete="tel" />

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">
                        {{ __('Save') }}
                    </flux:button>
                </div>

                <x-action-message class="me-3" on="business-created">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>
    </x-business.layout>
</section>
