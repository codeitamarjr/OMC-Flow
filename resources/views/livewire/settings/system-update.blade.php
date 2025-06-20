<section class="w-full">
    @include('partials.update-heading')

    <x-update.layout :heading="__('Update')" :subheading="__('Update your application to the latest version')">

        <div class="p-6">
            <h2 class="text-xl font-bold mb-4">System Update</h2>

            @if ($updateAvailable)
                <div class="mb-4 p-4 bg-yellow-100 border-l-4 border-yellow-500" wire:loading.remove>
                    <p><strong>New Version:</strong> {{ $updateAvailable['version'] }}</p>
                    <p><strong>Title:</strong> {{ $updateAvailable['title'] }}</p>
                    <p><strong>Description:</strong> {{ $updateAvailable['description'] }}</p>
                </div>

                <button wire:click="runUpdate" wire:loading.remove
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    Run Update
                </button>
            @else
                <div class="p-4 bg-green-100 border-l-4 border-green-500"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95">
                    Your application is up to date.
                </div>
            @endif

            <div class="flex items-center justify-center mt-4" wire:loading wire:target="runUpdate"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95">
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-gray-700 font-semibold mr-8">Updating...</div>
                    <div class="spinner">
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>
                </div>
            </div>

            @if ($log)
                <div class="mt-6 bg-black text-green-400 p-4 text-sm whitespace-pre-wrap break-words transition-all duration-300 ease-in-out"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95">
                    {{ $log }}
                </div>

                @if ($status === 'successful')
                    <div class="mt-2 text-green-700 font-semibold">✅ Update completed successfully.</div>
                @elseif ($status === 'failed')
                    <div class="mt-2 text-red-700 font-semibold">❌ Update failed. Check logs.</div>
                @endif
            @endif
        </div>

        @if ($updates)
            <div class="p-6">
                <h2 class="text-xl font-bold mb-4">Updates History</h2>
                </h2>

                @foreach ($updates as $update)
                    <div class="mb-4 p-4 bg-gray-100 border-l-4 border-gray-500">
                        <p><strong>Version:</strong> {{ $update->version }}</p>
                        <p><strong>Title:</strong> {{ $update->commit_title }}</p>
                        <p><strong>Description:</strong> {{ $update->description }}</p>
                        <p><strong>Status:</strong>
                            <span
                                class="inline-flex items-center gap-x-1.5 rounded-full px-2 py-1 text-xs font-medium text-gray-900 ring-1 ring-inset ring-gray-200">
                                <svg class="size-1.5 fill-blue-500" viewBox="0 0 6 6" aria-hidden="true">
                                    <circle cx="3" cy="3" r="3" />
                                </svg>
                                {{ $update->status }}
                            </span>
                        </p>
                    </div>
                @endforeach
            </div>
        @endif

        <style>
            .spinner {
                width: 44.8px;
                height: 44.8px;
                animation: spinner-y0fdc1 2s infinite ease;
                transform-style: preserve-3d;
            }

            .spinner>div {
                background-color: rgba(71, 75, 255, 0.2);
                height: 100%;
                position: absolute;
                width: 100%;
                border: 2.2px solid #474bff;
            }

            .spinner div:nth-of-type(1) {
                transform: translateZ(-22.4px) rotateY(180deg);
            }

            .spinner div:nth-of-type(2) {
                transform: rotateY(-270deg) translateX(50%);
                transform-origin: top right;
            }

            .spinner div:nth-of-type(3) {
                transform: rotateY(270deg) translateX(-50%);
                transform-origin: center left;
            }

            .spinner div:nth-of-type(4) {
                transform: rotateX(90deg) translateY(-50%);
                transform-origin: top center;
            }

            .spinner div:nth-of-type(5) {
                transform: rotateX(-90deg) translateY(50%);
                transform-origin: bottom center;
            }

            .spinner div:nth-of-type(6) {
                transform: translateZ(22.4px);
            }

            @keyframes spinner-y0fdc1 {
                0% {
                    transform: rotate(45deg) rotateX(-25deg) rotateY(25deg);
                }

                50% {
                    transform: rotate(45deg) rotateX(-385deg) rotateY(25deg);
                }

                100% {
                    transform: rotate(45deg) rotateX(-385deg) rotateY(385deg);
                }
            }
        </style>

    </x-update.layout>
</section>
