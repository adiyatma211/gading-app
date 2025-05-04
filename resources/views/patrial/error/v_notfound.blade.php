@extends('layouts.base')
@section('content')
    <div class="error-page container">
        <div class="col-md-8 col-12 offset-md-2">
            <div class="text-center">
                <img class="img-error" src="./assets/compiled/svg/error-404.svg" alt="Not Found">
                <h1 class="error-title">Not Found</h1>
                <p class='fs-5 text-gray-600'>Opps Data Tidak di temukan</p>
                <a href="/logout" class="btn btn-lg btn-outline-primary mt-3">Go Home</a>
            </div>
        </div>
    </div>
@endsection
