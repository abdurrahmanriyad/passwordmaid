@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row  align-content-center align-items-center">
            <div class="col-md-6">
                @if(session()->has('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @elseif(session()->has('error'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <h3 class="mb-4">Change Password</h3>
                <form method="POST" action="{{ route('password.change.update') }}">
                    @csrf
                    @method('put')
                    <div class="form-group row">
                        <div class="col-md-8">
                            <input id="current_password" placeholder="{{ __('Current Password') }}" type="password"
                                   class="form-control @error('current_password') is-invalid @enderror"
                                   name="current_password" required autocomplete="email" autofocus>

                            @error('current_password')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-8">
                            <input id="password" placeholder="{{ __('New Password') }}" {{ __('New Password') }} type="password"
                                   class="form-control @error('password') is-invalid @enderror" name="password"
                                   required autocomplete="new-password">

                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-8">
                            <input placeholder="{{ __('Password again') }}" id="password-confirm" type="password" class="form-control"
                                   name="password_confirmation" required autocomplete="new-password">
                        </div>
                    </div>

                    <div class="form-group row mb-0">
                        <div class="col-md-8">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Change') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>


            <div class="col-md-6 d-none d-md-block">
                <img src="{{ asset('images/change-password-bg.svg') }}" alt="" class="img-fluid">
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/app.js') }}"></script>
@endpush
