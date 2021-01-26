<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Rutugo - Admin</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

    <!-- Fonts -->
    <link rel="stylesheet" href="{{ asset('css/admin/admin-app.css') }}">

    <script src="https://use.fontawesome.com/d2154184d1.js"></script>

    @yield('head')
</head>

<body>
@yield('content')

<script src="{{ asset('js/admin-app.js') }}"></script>

@yield('scripts')
</body>
</html>
