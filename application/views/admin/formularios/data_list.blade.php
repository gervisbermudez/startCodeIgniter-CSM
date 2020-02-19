@extends('admin.layouts.app')

@section('title', $title)

@section('header')
@include('admin.shared.header')
@endsection

@section('content')
<div class="container" id="root">
    <div class="row">
        <div class="input-field col s14">
            <input type="text" id="autocomplete-input" class="autocomplete" v-model="filter">
            <label for="autocomplete-input">Filtrar</label>
        </div>
        <div class="col s12">
            <table>
                <thead>
                    <tr>
                        <th>Form Type</th>
                        <th>User</th>
                        <th>Date update</th>
                        <th>Edit</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody v-cloak>
                    <tr v-for="(item, index) in filterContent" :key="index">
                        <td>
                            @{{item.form_name}}
                        </td>
                        <td>
                            @{{item.username}}
                        </td>
                        <td>
                            @{{item.date_update}}
                        </td>
                        <td>
                            <a :href="getEditData(item)"><i class="material-icons">create</i></a>
                        </td>
                        <td>
                            <a href="#!"><i class="material-icons">delete</i></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
        <a class="btn-floating btn-large red waves-effect waves-teal btn-flat new tooltipped" data-position="left"
            data-delay="50" data-tooltip="Nuevo Formulario" href="{{base_url('admin/formularios/new')}}">
            <i class="large material-icons">add</i>
        </a>
        <ul>
            <li v-for="(item, index) in filterFormsTypes" :key="index">
                <a data-position="left" data-delay="50" :data-tooltip="item.form_name" class="btn-floating tooltipped"
                    :href="getFormsTypeUrl(item)">
                    <i class="material-icons">add_to_photos</i>
                </a>
            </li>
        </ul>
    </div>
</div>
@endsection