@extends('layout.app')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/frontend/pages/access-code/index.css') }}">
@endsection

@section('content')
    <div class="access-code-page">
        <div class="wrapper">
            <div class="logo text-center" style="text-align: center;">
                <img src="{{ asset('logo/logo-blue.png') }}" class="img-fluid" alt="Rutugo Logo">
            </div>

            <div class="description">Our website is now under construction</div>

            <form class="form" method="post">
                @csrf

                <div class="form-group">
                    <input type="text" placeholder="Enter Access Code" name="access_code" class="form-control">
                    <input type="submit" name="submit-access-code" value="Submit" class="btn-orange">
                </div>
            </form>

            <div class="social">
                <a href="#" class="social-link">
                    <i class="fa fa-facebook"></i>
                </a>

                <a href="#" class="social-link">
                    <i class="fa fa-twitter"></i>
                </a>

                <a href="#" class="social-link">
                    <i class="fa fa-instagram"></i>
                </a>
            </div>
        </div>
    </div>
@endsection
