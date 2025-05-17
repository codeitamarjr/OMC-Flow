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

        <form wire:submit.prevent="{{ $editingTagId ? 'update' : 'save' }}" class="my-6 space-y-4">
            <flux:input wire:model="name" :label="__('Tag Name')" required />

            <div class="flex items-center gap-4">
                <flux:button type="submit" variant="primary">
                    {{ $editingTagId ? __('Update Tag') : __('Create Tag') }}
                </flux:button>

                @if ($editingTagId)
                    <flux:button type="button" variant="outline" wire:click="$set('editingTagId', null)">
                        {{ __('Cancel') }}
                    </flux:button>
                @endif
            </div>
        </form>
    </x-tag.layout>
</section>
