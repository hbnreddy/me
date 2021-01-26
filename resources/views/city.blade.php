@extends('layout.app')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/frontend/pages/city/index.css') }}">
@endsection

@section('vue-app')
    <div id="vue-app">
        <city-page :data="{{ json_encode($data) }}">
        </city-page>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/frontend/pages/city/index.js') }}"></script>
@endsection
