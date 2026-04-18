<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div id="global-loader" style="position: fixed; z-index: 9999; background: #fff; inset: 0; display: flex; align-items: center; justify-content: center;">
        <img src="{{ asset('assets/images/loader.svg') }}" alt="Loader">
    </div>

    <div class="min-h-screen bg-gray-100">
        @include('layouts.navigation')

        @isset($header)
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
        @endisset

        <main>
            {{ $slot }}
        </main>
    </div>

    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>

    <script>
        // Use document ready as a fallback to window load
        $(document).ready(function() {
            // Check if loader exists then fade out
            if ($("#global-loader").length) {
                setTimeout(function () {
                    $("#global-loader").fadeOut("slow");
                }, 500); // Thoda delay taaki transition smooth dikhe
            }
        });

        // Hard hide after 2 seconds (Final Safety)
        setTimeout(function () {
            $("#global-loader").hide();
        }, 2000);
    </script>
</body> {{-- Scripts iske andar honi chahiye --}}
</html>