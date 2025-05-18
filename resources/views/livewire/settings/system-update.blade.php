<section class="w-full">
    @include('partials.update-heading')

    <x-update.layout :heading="__('Update')" :subheading="__('Update your application to the latest version')">

        <div class="p-6">
            <h2 class="text-xl font-bold mb-4">System Update</h2>

            @if ($updateAvailable)
                <div class="mb-4 p-4 bg-yellow-100 border-l-4 border-yellow-500">
                    <p><strong>New Version:</strong> {{ $updateAvailable['version'] }}</p>
                    <p><strong>Title:</strong> {{ $updateAvailable['title'] }}</p>
                    <p><strong>Description:</strong> {{ $updateAvailable['description'] }}</p>
                </div>

                <button wire:click="runUpdate" wire:loading.attr="disabled"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    Run Update
                </button>
            @else
                <div class="p-4 bg-green-100 border-l-4 border-green-500">
                    Your application is up to date.
                </div>
            @endif

            @if ($log)
                <div class="mt-6 bg-black text-green-400 p-4 text-sm overflow-y-auto h-64 whitespace-pre-wrap">
                    {{ $log }}
                </div>

                @if ($status === 'successful')
                    <div class="mt-2 text-green-700 font-semibold">✅ Update completed successfully.</div>
                @elseif ($status === 'failed')
                    <div class="mt-2 text-red-700 font-semibold">❌ Update failed. Check logs.</div>
                @endif
            @endif
        </div>

    </x-update.layout>
</section>
