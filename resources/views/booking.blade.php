@extends('layout.app')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/frontend/pages/booking/index.css') }}">

    <link rel="dns-prefetch" href="//fonts.gstatic.com">

    <script src="https://use.fontawesome.com/d2154184d1.js"></script>
@endsection

@section('vue-app')
    <div id="vue-app">
        <booking
            :data="{{ json_encode($data) }}"
        ></booking>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/frontend/pages/booking/index.js') }}"></script>
@endsection
