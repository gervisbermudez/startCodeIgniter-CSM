@extends('admin.layouts.login')
@section('title', $title)
@section('content')
<div class="cont">
    <div class="row">
        <div class="col s12">
            <div class="logo">
            </div>
        </div>
    </div>
    <div class="container ">
        <div class="row ">
            <div class="col s12 m3 l4 login" id="root">
                <div class="center" v-show="loader">
                    <div class="preloader-wrapper active">
                        <div class="spinner-layer spinner-red-only">
                            <div class="circle-clipper left">
                                <div class="circle"></div>
                            </div>
                            <div class="gap-patch">
                                <div class="circle"></div>
                            </div>
                            <div class="circle-clipper right">
                                <div class="circle"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <form v-cloak class="card hoverable" role="form" method="post" v-show="!loader">
                    <div class="card-panel light-blue center-align">
                        <span class="card-title">Iniciar sesion</span>
                    </div>
                    <div class="card-content">
                        <div v-if="userdata" class="user" tabindex="0">
                            <a href="#!" class="user-avatar">
                            <img :src="'/public/img/profile/' + userdata.username + '/' + userdata.user_data.avatar" alt="" class="circle z-depth-1">
                            </a>
                            <a class="avatar-username" href="#!">
                            <span class="name">@{{userdata.user_data.nombre + ' ' + userdata.user_data.apellido}}</span>
                            </a>
                            </a>
                        </div>
                        <div v-show="!userdata" class="input-field">
                            <i class="material-icons prefix">perm_identity</i>
                            <input id="username" type="text" v-model="username" name="username" required="required">
                            <label for="username" class="">Username</label>
                        </div>
                        <div class="input-field">
                            <i class="material-icons prefix">lock_outline</i>
                            <input id="password" name="password" v-model="password" type="password" required="required">
                            <label for="password" class="">Password</label>
                        </div>
                        <div class="input-field">
                            <p>
                                <label>
                                    <input type="checkbox" v-model="remember_user"/>
                                    <span>Recordarme</span>
                                </label>
                            </p>
                        </div>
                        <div class="input-field"  v-if="userdata">
                            <p>
                                <a href="#!" class="reset-user" @click="resetUserdata();">Ingresar con otro usuario</a>
                            </p>
                        </div>
                    </div>
                    <div class="card-action">
                        <button class="btn light-blue waves-effect waves-light" type="button"
                            :class="{disabled: !btnEnable}" @click="login">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer_includes'):
<script src="{{base_url('public/js/components/loginForm.min.js?v=' . SITEVERSION)}}"></script>
@endsection