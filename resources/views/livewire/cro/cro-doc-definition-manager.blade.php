<section class="w-full">
    @include('partials.cro-definition-heading')

    <x-cro-definition.layout :heading="__('CRO Definitions')" :subheading="__('All definitions related to your CRO')">
        {{-- Flash messages --}}
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

        <div>
            <div class="flex justify-between mb-4 space-x-4">
                <input type="text" wire:model.live.debounce.500ms="search" placeholder="Search definitions..."
                    class="border rounded p-2" />
                <button wire:click="showCreateModal()" class="bg-blue-600 text-white px-4 py-2 rounded">
                    New Definition
                </button>
            </div>

            <table class="w-full text-left table-auto min-w-max" wire:loading.class="opacity-50">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2">Name</th>
                        <th class="px-4 py-2">Code</th>
                        <th class="px-4 py-2">Days from ARD</th>
                        <th class="px-4 py-2">Global</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($definitions as $def)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $def->name }}</td>
                            <td class="px-4 py-2">{{ $def->code }}</td>
                            <td class="px-4 py-2">{{ $def->days_from_ard }}</td>
                            <td class="px-4 py-2">{{ $def->is_global ? 'Yes' : 'No' }}</td>
                            <td class="px-4 py-2 space-x-2">
                                @if (!$def->is_global)
                                    <button wire:click="showEditModal({{ $def->id }})"
                                        class="text-indigo-600">Edit</button>
                                    <button wire:click="delete({{ $def->id }})"
                                        class="text-red-600">Delete</button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4">
                {{ $definitions->links() }}
            </div>

            {{-- Create/Edit Modal --}}
            <x-ui.modal wire:model="modalFormVisible" maxWidth="md">
                <h3 class="text-lg font-semibold mb-4">
                    {{ $modelId ? 'Edit Definition' : 'New Definition' }}
                </h3>
                <span class="text-sm text-gray-600 mb-4 block">
                    {{ $modelId ? 'Update the details of this definition.' : 'Fill in the details for the new definition, this will apply to all Businesses.' }}
                </span>

                <form wire:submit.prevent="{{ $modelId ? 'update' : 'create' }}">
                    <div class="space-y-4">
                        <div>
                            <label class="block">Name</label>
                            <input type="text" wire:model.defer="name" class="w-full border rounded p-2" />
                            @error('name')
                                <span class="text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block">Description</label>
                            <input type="text" wire:model.defer="description" class="w-full border rounded p-2" />
                            @error('description')
                                <span class="text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block">Code</label>
                            <input type="text" wire:model.defer="code" class="w-full border rounded p-2" />
                            @error('code')
                                <span class="text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block">Days from ARD</label>
                            <input type="number" wire:model.defer="days_from_ard" class="w-full border rounded p-2" />
                            @error('days_from_ard')
                                <span class="text-red-600">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 text-right space-x-2">
                        <flux:button wire:click="$set('modalFormVisible', false)">Cancel</flux:button>
                        <flux:button type="submit" primary>
                            {{ $modelId ? 'Update' : 'Create' }}
                        </flux:button>
                    </div>
                </form>
            </x-ui.modal>
        </div>

    </x-cro-definition.layout>

</section>
