@extends('layout.admin')

@section('head')
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
@endsection

@section('vue-app')
    <div id="vue-admin-app">
        <admin-dashboard init-api="{{ json_encode($api) }}" user="{{ json_encode(auth()->user()) }}"/>
    </div>
@endsection
