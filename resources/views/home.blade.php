@extends('layouts.app')

@push('stylenav')

    <link href="{{ asset('css/navbar.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />@endpush

@section('header')

@endsection
@section('content')

@endsection
@section('footer')
    @include('layouts.footer')
@endsection
