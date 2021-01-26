<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Rutugo</title>

    <link href="https://fonts.googleapis.com/css?family=Nunito&Source+Serif+Pro&display=swap" rel="stylesheet">

    @yield('head')
</head>

<body>
    @yield('content')

    <div id="vue-app">
        @yield('vue-app')

        <notification
            text="{{ isset($data['message']['text']) ? $data['message']['text'] : false }}"
            type="{{ isset($data['message']['type']) ? $data['message']['type'] : false }}"
        />
    </div>

    @isset($data['locale'])
        <script>
            window._translations = {!! cache('translations') !!};
            window._locale = {!! json_encode(\App\AppConfig::getLocale()) !!};
        </script>
    @endisset

    @yield('scripts')
</body>
</html>
