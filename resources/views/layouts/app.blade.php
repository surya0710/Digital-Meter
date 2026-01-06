<!DOCTYPE html >
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <!-- BASIC META TAGS -->
    @include('partials.head')
</head>
<body>
    @include('partials.header')
    <div class="page-body-wrapper">
        @include('partials.aside')
    <main>
        @yield('content')
    </main>
    @include('partials.footer')
    <!-- JS -->
    @vite(['resources/js/app.js'])
    <!-- 🔥 FORCE LOAD ECHO (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/pusher-js@8.4.0/dist/web/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.3/dist/echo.iife.js"></script>

    <script>
        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: "{{ env('REVERB_APP_KEY') }}",
            wsHost: "{{ env('REVERB_HOST') }}",
            wsPort: "{{ env('REVERB_PORT') }}",
            forceTLS: false,
            disableStats: true,
        });
    </script>

    @stack('scripts')

</body>
</html>
