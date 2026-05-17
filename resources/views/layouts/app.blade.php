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
        @if($device->device_id != '3C:E9:0E:CD:90:45')
            @include('partials.aside')
        @endif
    <main>
        @yield('content')
    </main>
    @include('partials.footer')
    <!-- JS -->
    @vite(['resources/js/app.js'])
    <!-- 🔥 FORCE LOAD ECHO (CDN) -->

    @stack('scripts')

</body>
</html>