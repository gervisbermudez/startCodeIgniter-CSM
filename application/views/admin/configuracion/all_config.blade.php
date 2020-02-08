@extends('admin.layouts.app')

@section('title', $title)

@section('header')
@include('admin.shared.header')
@endsection

@section('content')
<div class="container">
    @isset($saved)
    <div class="row">
        <div class="col s12">
            <h4>
                {{$saved}}
            </h4>
        </div>
    </div>
    @endisset
    <form action="{{$action}}" method="POST" class="row">
        <div class="col s12">
            <div class="input-field col s4">
                <select name="theme_selected">
                    <option value="" disabled selected>Choose your theme</option>
                    <option value="DEFAULT">Default Theme</option>
                    @foreach ($themes_ as $theme)
                    <option value="{{$theme['name']}}" {{ ($config->site_theme == $theme['name']) ? 'selected' : ''}}>
                        {{$theme['name']}}</option>
                    @endforeach
                </select>
                <label>Theme</label>
            </div>
        </div>
        <div class="col s12">
            <button type="submit" class="waves-effect waves-light btn"><i class="material-icons left">cloud</i>
                Guardar</button>
        </div>
    </form>
</div>
@endsection