@extends('admin.layouts.app')

@section('title', 'Admin | Dashboard')

@section('head')
    @include('admin.shared.head')
@endsection

@section('navbar')
    @include('admin.shared.navbar')
@endsection

@section('content')
    @include('admin.bodypage')
@endsection

@section('footer')
    @include('admin.shared.footer')
@endsection