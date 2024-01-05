<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->

    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/sweetalert2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/app.css?hash=12399a') }}" rel="stylesheet">

    @yield('css')

    <!-- Requests -->
    <script>
        window.API = {
           'csrfToken': '{{ csrf_token() }}',
           'apiUrl': '{{ config('app.api_url') }}',
           'appUrl': '{{ config('app.url') }}'
       };
    </script>
    <script src="{{ asset('assets/js/axios.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.internal.js') }}"></script>

</head>
<body>

    @yield('content')

    <!-- Scripts -->

    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/js/vue.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>

    @yield('js')

    @yield('modals')

</body>
</html>
