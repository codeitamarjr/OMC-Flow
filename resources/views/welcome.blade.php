<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }}</title>

    <link rel="icon" type="image/png" href="/favicon/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/favicon/favicon.svg" />
    <link rel="shortcut icon" href="/favicon/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png" />
    <link rel="manifest" href="/favicon/site.webmanifest" />

    <!-- Fonts -->
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
    </style>

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="antialiased bg-white w-full min-h-screen flex flex-col">

    <div class="relative isolate overflow-hidden bg-gradient-to-b from-indigo-100/20">
        <div class="mx-auto max-w-7xl pb-24 pt-10 sm:pb-32 lg:grid lg:grid-cols-2 lg:gap-x-8 lg:px-8 lg:py-40">
            <div class="px-6 lg:px-0 lg:pt-4">
                <div class="mx-auto max-w-2xl">
                    <div class="max-w-lg">
                        <img class="h-11" src="/favicon/favicon.svg" alt="{{ config('app.name') }}">
                        <div class="mt-24 sm:mt-32 lg:mt-16">
                            <a href="#" class="inline-flex space-x-6">
                                <span
                                    class="rounded-full bg-indigo-600/10 px-3 py-1 text-sm/6 font-semibold text-indigo-600 ring-1 ring-inset ring-indigo-600/10">What's
                                    new</span>
                                <span class="inline-flex items-center space-x-2 text-sm/6 font-medium text-gray-600">
                                    <span>Just launched v0.1.0</span>
                                    <svg class="size-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"
                                        aria-hidden="true" data-slot="icon">
                                        <path fill-rule="evenodd"
                                            d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </a>
                        </div>
                        <h1 class="mt-10 text-pretty text-5xl font-semibold tracking-tight text-gray-900 sm:text-7xl">
                            {{ config('app.name') }}</h1>
                        <p class="mt-8 text-pretty text-lg font-medium text-gray-500 sm:text-xl/8">
                            Simplifying OMC Compliance & Reporting
                        </p>
                        <div class="mt-10 flex items-center gap-x-6">
                            @auth
                                <a href="{{ route('dashboard') }}"
                                    class="rounded-md bg-indigo-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                    Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}"
                                    class="rounded-md bg-indigo-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                    Get Started
                                </a>
                            @endauth
                            <a href="https://github.com/codeitamarjr/OMC-Flow"
                                class="text-sm/6 font-semibold text-gray-900" target="_blank">
                                View on GitHub <span aria-hidden="true">→</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-20 sm:mt-24 md:mx-auto md:max-w-2xl lg:mx-0 lg:mt-0 lg:w-screen">
                <div class="absolute inset-y-0 right-1/2 -z-10 -mr-10 w-[200%] skew-x-[-30deg] bg-white shadow-xl shadow-indigo-600/10 ring-1 ring-indigo-50 md:-mr-20 lg:-mr-36"
                    aria-hidden="true"></div>
                <div class="shadow-lg md:rounded-3xl">
                    <div
                        class="bg-indigo-500 [clip-path:inset(0)] md:[clip-path:inset(0_round_theme(borderRadius.3xl))]">
                        <div class="absolute -inset-y-px left-1/2 -z-10 ml-10 w-[200%] skew-x-[-30deg] bg-indigo-100 opacity-20 ring-1 ring-inset ring-white md:ml-20 lg:ml-36"
                            aria-hidden="true"></div>
                        <div class="relative px-6 pt-8 sm:pt-16 md:pl-16 md:pr-0">
                            <div class="mx-auto max-w-2xl md:mx-0 md:max-w-none">
                                <div class="w-screen overflow-hidden rounded-tl-xl bg-gray-900">
                                    <div class="flex bg-gray-800/40 ring-1 ring-white/5">
                                        <div class="-mb-px flex text-sm/6 font-medium text-gray-400">
                                            <div
                                                class="border-b border-r border-b-white/20 border-r-white/10 bg-white/5 px-4 py-2 text-white">
                                                Features</div>
                                        </div>
                                    </div>
                                    <div class="mt-20 mb-20 px-6 pb-14 pt-6">
                                        <div class="mt-6 flow-root">
                                            <ul role="list" class="-mb-8">
                                                <li class="flex items-start">
                                                    <div class="flex-shrink-0">
                                                        <span class="h-10 w-10 rounded-full bg-gray-800/40"></span>
                                                    </div>
                                                    <div class="ml-4">
                                                        <p class="text-sm font-medium text-gray-200">Import CRO's
                                                        </p>
                                                    </div>
                                                </li>
                                                <li class="flex items-start">
                                                    <div class="flex-shrink-0">
                                                        <span class="h-10 w-10 rounded-full bg-gray-800/40"></span>
                                                    </div>
                                                    <div class="ml-4">
                                                        <p class="text-sm font-medium text-gray-200">
                                                            Overview Compliances
                                                        </p>
                                                    </div>
                                                </li>
                                                <li class="flex items-start">
                                                    <div class="flex-shrink-0">
                                                        <span class="h-10 w-10 rounded-full bg-gray-800/40"></span>
                                                    </div>
                                                    <div class="ml-4">
                                                        <p class="text-sm font-medium text-gray-200">
                                                            Connected with Companies Registration Office (CRO's)
                                                        </p>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="pointer-events-none absolute inset-0 ring-1 ring-inset ring-black/10 md:rounded-3xl"
                                aria-hidden="true"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="absolute inset-x-0 bottom-0 -z-10 h-24 bg-gradient-to-t from-white sm:h-32"></div>
    </div>
    <footer class="mx-auto max-w-2xl py-16 text-sm/6 text-gray-500 text-center">
        <p>Made with ❤️ by <a href="mailto:hello@itjunior.dev" class="text-gray-900">Itamar Junior</a></p>
        <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </footer>
</body>

</html>
