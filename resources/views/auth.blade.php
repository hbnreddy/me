@extends('layout.app')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/frontend/pages/auth/index.css') }}">

    <script src="https://use.fontawesome.com/d2154184d1.js"></script>

    <style>
        body {
            background-image: url("/landing-page-image.jpg");
            background-position: center;
        }
    </style>
@endsection

@section('vue-app')
    <auth-page></auth-page>
@endsection

@section('scripts')
    <script src="{{ asset('js/frontend/pages/auth/index.js') }}"></script>
@endsection
