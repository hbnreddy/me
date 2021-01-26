@extends('layout.app')

@section('content')
    <div class="d-flex justify-content-center align-items-center" style="height: 100%;">
        <h3>We've sent you an email confirmation !</h3>
    </div>
@endsection

@section('scripts')
    <script>
        setTimeout(function () {
            window.location = '/';
        }, 2000);
    </script>
@endsection
