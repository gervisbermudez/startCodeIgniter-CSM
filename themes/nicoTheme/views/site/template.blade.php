@extends('site.layouts.site')

@section('title', $title)

@section('content')
<div class="container">
    <h1>
        {{$page['title']}}
    </h1>
    <div class="article">
        <?= $page['content'] ?>
    </div>
</div>
@endsection