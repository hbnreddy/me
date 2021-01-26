@extends('layout.app')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/frontend/pages/welcome/index.css') }}">

    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@200;400;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bitter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://use.fontawesome.com/d2154184d1.js"></script>
@endsection

@section('vue-app')
    <welcome
        :data="{{ json_encode($data) }}"
        :error="{{ json_encode(session()->get('error')) }}"
    ></welcome>
@endsection

@section('scripts')
    <script src="{{ asset('js/frontend/pages/welcome/index.js') }}"></script>
@endsection
