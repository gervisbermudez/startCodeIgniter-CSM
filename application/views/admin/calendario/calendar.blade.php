@extends('admin.layouts.app')
@section('title', $title)
@section('header')
@endsection

@section('head_includes')
<link rel="stylesheet" href="<?=base_url('public/js/fullcalendarjs/main.min.css')?>">
@endsection

@section('content')
<div id="root" class="container">
    <div class="col s12 center" v-bind:class="{ hide: !loader }" style="min-height: 160px;">    
        <br><br>
        <preloader />
    </div>
    <br>
        <br>
    <div id='calendar'></div>
    <br>
    <br>
    <br>
</div>
@endsection

@section('footer_includes')
<script src="{{base_url('public/js/fullcalendarjs/main.min.js')}}"></script>
<script src="{{base_url('resources/components/CalendarList.js')}}"></script>
@endsection
