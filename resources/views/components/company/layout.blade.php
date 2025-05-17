<div class="flex items-start max-md:flex-col">
    <div class="me-10 w-full pb-4 md:w-[220px]">
        <flux:navlist>
            <flux:navlist.item :href="route('company.manage')" wire:navigate>{{ __('View Companies') }}
            </flux:navlist.item>
            @if (auth()->user()->roleInCurrentBusiness() === 'admin')
                <flux:navlist.item :href="route('company.create')" wire:navigate>{{ __('Create Company') }}
                </flux:navlist.item>
                <flux:navlist.item :href="route('company.import')" wire:navigate>{{ __('Import Companies') }}
                </flux:navlist.item>
            @endif
        </flux:navlist>
    </div>

    <flux:separator class="md:hidden" />

    <div class="flex-1 self-stretch max-md:pt-6">
        <flux:heading>{{ $heading ?? '' }}</flux:heading>
        <flux:subheading>{{ $subheading ?? '' }}</flux:subheading>

        <div class="mt-5 w-full max-w-lg">
            {{ $slot }}
        </div>
    </div>
</div>
