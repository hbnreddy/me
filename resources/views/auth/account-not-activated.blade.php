@extends('layout.app')

@section('content')
    <div class="d-flex justify-content-center align-items-center" style="height: 100%;">
        <h3>You have to activate your account before login.</h3>
        <h3>Please verify your e-mail.</h3>
    </div>
@endsection

@section('scripts')
    <script>
        setTimeout(function () {
            window.location = '/';
        }, 2000);
    </script>
@endsection
