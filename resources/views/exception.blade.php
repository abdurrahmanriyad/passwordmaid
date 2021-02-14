@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="error-header mb-5">
                    <h1 class="error-status mb-0">{{ $status }}</h1>
                    <h3 class="error-type">Internal server error!</h3>
                </div>

                <div class="error-body">
                    <p class="mb-0 text-muted">We are looking towards improving things.</p>
                    <h6>Won't be long...</h6>
                </div>
            </div>

            <div class="col-md-6 d-none d-md-block">
                <img src="{{ asset('images/exception.svg') }}" alt="Internal error!" class="img-fluid">
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/app.js') }}"></script>
@endpush
