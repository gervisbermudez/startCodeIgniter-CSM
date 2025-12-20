@extends('admin.layouts.app')
@section('title', $title)
@section('header')
@endsection

@section('head_includes')
<link rel="stylesheet" href="<?=base_url('public/vendors/fullcalendar/main.min.css')?>">
@endsection

@section('content')
<div id="root" class="container">
    <div class="col s12 center" v-bind:class="{ hide: !loader }" style="min-height: 160px;">    
        <br><br>
        <preloader />
    </div>
    
    <div class="row" v-bind:class="{ hide: loader }">
        <div class="col s12">
            <div class="card">
                <div class="card-content">
                    <span class="card-title">Filtros</span>
                    <div class="row">
                        <div class="col s12 m6 l3">
                            <p>
                                <label>
                                    <input type="checkbox" v-model="filters.pages" @change="applyFilters" />
                                    <span>📄 Páginas</span>
                                </label>
                            </p>
                        </div>
                        <div class="col s12 m6 l3">
                            <p>
                                <label>
                                    <input type="checkbox" v-model="filters.albums" @change="applyFilters" />
                                    <span>🖼️ Álbumes</span>
                                </label>
                            </p>
                        </div>
                        <div class="col s12 m6 l3">
                            <p>
                                <label>
                                    <input type="checkbox" v-model="filters.users" @change="applyFilters" />
                                    <span>👤 Usuarios</span>
                                </label>
                            </p>
                        </div>
                        <div class="col s12 m6 l3">
                            <p>
                                <label>
                                    <input type="checkbox" v-model="filters.categories" @change="applyFilters" />
                                    <span>🏷️ Categorías</span>
                                </label>
                            </p>
                        </div>
                        <div class="col s12 m6 l3">
                            <p>
                                <label>
                                    <input type="checkbox" v-model="filters.menus" @change="applyFilters" />
                                    <span>🧭 Menús</span>
                                </label>
                            </p>
                        </div>
                        <div class="col s12 m6 l3">
                            <p>
                                <label>
                                    <input type="checkbox" v-model="filters.siteforms" @change="applyFilters" />
                                    <span>📋 Formularios</span>
                                </label>
                            </p>
                        </div>
                        <div class="col s12 m6 l3">
                            <p>
                                <label>
                                    <input type="checkbox" v-model="filters.form_customs" @change="applyFilters" />
                                    <span>⚙️ Modelos</span>
                                </label>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <br>
    <div id='calendar'></div>
    <br>
    <br>
    <br>
</div>
@endsection

@section('footer_includes')
<script src="{{base_url('public/vendors/fullcalendar/main.min.js')}}"></script>
<script src="{{base_url('resources/components/CalendarList.js')}}"></script>
@endsection
