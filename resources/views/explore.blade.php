@extends('layout.app')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/frontend/pages/explore/index.css') }}">

    <script src="https://use.fontawesome.com/d2154184d1.js"></script>
@endsection

@section('vue-app')
    <div id="vue-app">
        <explore-page :data="{{ json_encode($data) }}">
        </explore-page>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/frontend/pages/explore/index.js') }}"></script>
@endsection
