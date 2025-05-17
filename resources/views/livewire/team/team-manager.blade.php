<section class="w-full">
    @include('partials.team-heading')

    <x-team.layout heading="Team" subheading="Manage users in this business.">
        @if (session('success'))
            <x-ui.flash-message type="success">{{ session('success') }}</x-ui.flash-message>
        @endif

        @if (session('error'))
            <x-ui.flash-message type="error">{{ session('error') }}</x-ui.flash-message>
        @endif

        @if (auth()->user()->roleInCurrentBusiness() === 'admin')
            <form wire:submit.prevent="invite" class="mb-4 flex gap-2">
                <flux:input wire:model.defer="name" type="text" placeholder="User name" required />
                <flux:input wire:model.defer="email" type="email" placeholder="Invite user by email" required />
                <flux:button type="submit">Invite</flux:button>
            </form>
        @endif

        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
            @foreach ($teamMembers as $member)
                <li class="py-2 flex justify-between items-center">
                    <div>
                        <div class="font-semibold">{{ $member->name }}</div>
                        <div class="text-sm text-gray-500">{{ $member->email }}</div>
                        <div class="text-xs text-gray-400">Role: {{ $member->pivot->role ?? 'N/A' }}</div>
                    </div>
                    @if (auth()->user()->roleInCurrentBusiness() === 'admin' && $member->id !== auth()->id())
                        <flux:button wire:click="remove({{ $member->id }})" variant="outline" size="sm"
                            class="text-red-500">Remove</flux:button>
                    @endif
                </li>
            @endforeach
        </ul>
    </x-team.layout>

</section>
