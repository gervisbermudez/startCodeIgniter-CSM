<style type="text/css">
    body{
        background: #2193b0;  /* fallback for old browsers */
        background: -webkit-linear-gradient(to left, #6dd5ed, #2193b0);  /* Chrome 10-25, Safari 5.1-6 */
        background: linear-gradient(to left, #6dd5ed, #2193b0); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
    }
</style>
<div class="cont">
    <div class="container ">
    <div class="row ">
        <div class="col s12 m3 l4 login">
            <form class="card hoverable" role="form" action="<?php echo $base_url ?>index.php/Login/validar/" method="post">
                <div class="card-panel center-align">
                    <span class="card-title">Iniciar sesion</span>
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
