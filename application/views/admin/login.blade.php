@extends('admin.layouts.login')
@section('title', $title)
@section('content')
<div class="cont">
    <div class="row">
        <div class="col s12">
            <div class="logo">
                <img src="{{base_url('/public/img/brand/logo.png')}}" alt="">
            </div>
        </div>
    </div>
    <div class="container ">
        <div class="row ">
            <div class="col s12 m3 l4 login">

                <form class="card hoverable" role="form" action="<?php echo $base_url ?>admin/Login/validar/"
                    method="post">
                    <div class="card-panel light-blue center-align">
                        <span class="card-title white-text ">Iniciar sesion</span>
                    </div>
                    <div class="card-content">
                        <p>
                            <div class="input-field">
                                <i class="material-icons prefix">perm_identity</i>
                                <input id="username" type="text" name="username" required="required">
                                <label for="username" class="">Username</label>
                            </div>
                            <div class="input-field">
                                <i class="material-icons prefix">lock_outline</i>
                                <input id="password" name="password" type="password" required="required">
                                <label for="password" class="">Password</label>
                            </div>
                            <br>
                            <input type="checkbox" id="test5" />
                            <label for="test5">Recordarme</label>
                            <br>
                        </p>
                        <?php if (isset($error)): ?>
                        <p class="red-text"><?php echo $error; ?></p>
                        <?php endif ?>
                    </div>
                    <div class="card-action">
                        <button class="btn light-blue waves-effect waves-light" type="submit">Login</button>
                    </div>
                    <?php if (isset($continue)): ?>
                    <input type="hidden" value="<?php echo $continue ?>" name="continue" id="continue">
                    <?php endif ?>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection