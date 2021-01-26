@extends('layout.admin')

@section('head')
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
@endsection

@section('content')
    <div id="vue-admin-app">
        <admin-app :user="{{ json_encode(auth()->user()) }}"/>
    </div>
@endsection

@section('scripts')
    @isset($data['locale'])
        <script>
            window._translations = {!! cache('translations') !!};
            window._locale = {!! json_encode(\App\AppConfig::getLocale()) !!};
        </script>
    @endisset
@endsection
