@extends('admin.layouts.login')
@section('title', $title)
@section('content')
<div class="cont" id="root">
    <div class="row">
        <div class="col s12 l5">
            <div class="row">
                <div class="col s12" v-show="!loader">
                    <div class="img-container brand-logo">
                        <img src="{{base_url('public/img/admin/brand/logo.svg')}}" alt="Start CMS"> 
                    </div>
                    <div class="brand">
                        Start CMS
                    </div>
                    <div class="sub-brand">
                        The Lightweight CMS
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col s12 m3 l4 login">
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
                    <form v-cloak class="" role="form" method="post" v-show="!loader">
                        <div class="center-align">
                            <span class="title">Iniciar sesion</span>
                        </div>
                        <div class="content">
                            <div v-if="userdata" class="user" tabindex="0">
                                <a href="#!" class="user-avatar">
                                    <img :src="userdata.get_avatarurl()" alt="" class="circle z-depth-1">
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
                                <input id="password" name="password" v-model="password" type="password"
                                    required="required">
                                <label for="password" class="">Password</label>
                            </div>
                            <div class="input-field remember-check">
                                <p>
                                    <label>
                                        <input type="checkbox" v-model="remember_user" />
                                        <span>Recordarme</span>
                                    </label>
                                </p>
                            </div>
                            <div class="input-field" v-if="userdata">
                                <p>
                                    <a href="#!" class="reset-user" @click="resetUserdata();">Ingresar con otro
                                        usuario</a>
                                </p>
                            </div>
                        </div>
                        <div class="action">
                            <button class="btn light-blue waves-effect waves-light" type="button"
                                :class="{disabled: !btnEnable}" @click="login">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col l7 hide-on-med-and-down wallpapper"></div>
    </div>
</div>
@endsection

@section('footer_includes')
<script src="{{base_url('public/js/components/loginForm.min.js?v=' . ADMIN_VERSION)}}"></script>
@endsection