<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Notifications')" :subheading="__('Manage which notifications you receive from OMC Flow')">
        <div class="my-2 w-full space-y-6">
            @foreach ($preferences as $key => $data)
                <div class="p-2.5 bg-white dark:bg-gray-800 rounded-xl shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="font-medium text-lg">
                                {{ $data['label'] }}
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $data['description'] }}
                            </p>
                        </div>

                        <button 
                            wire:click="toggle('{{ $key }}')"
                            class="px-4 py-2 rounded-full focus:outline-none
                                {{ $data['is_enabled']
                                    ? 'bg-green-500 text-white hover:bg-green-600'
                                    : 'bg-gray-300 text-gray-700 hover:bg-gray-400' }}"
                        >
                            {{ $data['is_enabled'] ? __('On') : __('Off') }}
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </x-settings.layout>
</section>
