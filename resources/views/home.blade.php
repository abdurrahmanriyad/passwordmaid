@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center stats-area">
            <div class="col-md-3">
                <a href="{{ route('app') }}" class="text-decoration-none text-dark">
                    <div class="card bg-white mb-3 shadow">
                        <div class="card-body">
                            <h2 class="card-title mb-2">{{ $total_projects }}</h2>
                            <h5 class="card-text text-black-50">Projects</h5>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3">
                <a href="{{ route('app') }}" class="text-decoration-none text-dark">
                    <div class="card bg-white mb-3 shadow">
                        <div class="card-body">
                            <h2 class="card-title">{{ $total_groups }}</h2>
                            <h5 class="card-text text-black-50">Groups</h5>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3">
                <a href="{{ route('app') }}" class="text-decoration-none text-dark">
                    <div class="card bg-white mb-3 shadow">
                        <div class="card-body">
                            <h2 class="card-title">{{ $shared_groups }}</h2>
                            <h5 class="card-text  text-black-50">Groups shared</h5>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3">
                <a href="{{ route('app') }}" class="text-decoration-none text-dark">
                    <div class="card bg-white mb-3 shadow">
                        <div class="card-body">
                            <h2 class="card-title">{{ $shared_with_me }}</h2>
                            <h5 class="card-text text-black-50">Groups shared with you</h5>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="row mt-md-5 mt-3">
            <div class="col-md-4">
                <h3 class="mb-3">Shared users</h3>
                @foreach($shared_users as $user)
                    <div class="card bg-white mb-1 shadow-sm">
                        <div class="card-body py-2 d-flex align-items-center">
                            <i class="fa fa-user-circle-o fa-2x d-inline-block mr-2 pr-3 border-right"></i>
                            <div class="d-inline-block">
                                <h5 class="card-title mb-1">{{ $user->name }}</h5>
                                <h6 class="card-text text-black-50">{{ $user->email }}</h6>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="col-md-4 mt-md-0 mt-4">
                <h3 class="mb-3">Recent groups</h3>
                @foreach($recent_groups as $group)
                    <a href="{{ route('app') . '#/' . $group->project->id }}" class="text-decoration-none recent-groups text-dark">
                        <div class="card border-radius-0 border-bottom-0 outline-0">
                            <div class="card-body p-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5 class="my-0">{{ $group->name }}</h5>
                                    </div>
                                    <div class="col-md-6 text-right text-black-50">
                                        <span class="d-inline-block mr-3"><i class="fa fa-users"></i> <strong>{{ $group->totalUsers() }}</strong></span>
                                        <span><i class="fa fa-key"></i> <strong>{{ $group->total_credentials }}</strong></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="col-md-4 mt-md-0 mt-4">
                <h3 class="mb-3">Recently shared with me</h3>
                @forelse($recent_shared_by_groups as $group)
                    <a href="{{ route('app') . '#/' . $group->project->id }}" class="text-decoration-none recent-groups text-dark">
                        <div class="card border-radius-0 border-bottom-0 outline-0">
                            <div class="card-body p-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5 class="my-0">{{ $group->name }}</h5>
                                    </div>
                                    <div class="col-md-6 text-right text-black-50">
                                        <span><i class="fa fa-key"></i> <strong>{{ $group->total_credentials }}</strong></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="text-black-50">Nothing shared!</div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/app.js') }}"></script>
@endpush