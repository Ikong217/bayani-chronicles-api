<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    @yield('head') {{-- General head elements --}}

    <style>

        @yield('css')

    </style>

    <title>@yield('title', 'Bayani Chronicles')</title>
</head>
<body @yield('body')>

    @yield('content')

    @yield('others') {{-- e.g., modals, footers, widgets --}}

    @yield('scripts') {{-- Full control over <script> tags --}}

</body>
</html>
