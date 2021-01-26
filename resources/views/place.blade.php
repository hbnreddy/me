@extends('layout.app')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/frontend/pages/place/index.css') }}">

    <script src="https://use.fontawesome.com/d2154184d1.js"></script>
@endsection

@section('vue-app')
    <div id="vue-app">
        <place-page :data="{{ json_encode($data) }}">
        </place-page>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/frontend/pages/place/index.js') }}"></script>
@endsection
