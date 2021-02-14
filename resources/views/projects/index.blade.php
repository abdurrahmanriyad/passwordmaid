@extends('layouts.app')

@push('styles')
    <link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet" />
@endpush

@section('content')

@endsection

@push('header-scripts')
    <script src="{{ asset('js/project.js') }}" defer></script>
@endpush
