@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center align-content-center text-center">
        <div class="col-md-8">
            <h5 class="my-5">Thank you for registering!</h5>
            <h1><i class="fa fa-envelope-open-o text-info"></i></h1>
            <h4 class="mt-4">Verify your email address</h4>
            @if (session('resent'))
                <div class="alert alert-success" role="alert">
                    {{ __('A fresh verification link has been sent to your email address.') }}
                </div>
            @endif

            <p class="text-dark">
                {{ __('Before proceeding, please check your email for a verification link.') }}
                {{ __('If you did not receive the email') }}
            </p>
            <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                @csrf
                <button type="submit" class="btn btn-info px-5 text-white align-baseline">{{ __('Request another') }}</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/app.js') }}"></script>
@endpush