<div class="main">
<?php echo $header ?><div class="container">
	<div class="row">
		<div class="col s12">
			
			<div class="panel panel-default">
				<?php foreach ($userdata as $value): ?>
				<div class="panel-heading">
					Editar usuario <?php echo $value['username'] ?>
				</div>
				<div class="panel-body">
					
					<form action="<?php echo $base_url.'index.php/Admin/User/save/'.$value['id'] ?>" method="post" id="form">
						<div class="row">
						<div class="input-field col s6">
							<i class="material-icons prefix">perm_identity</i>
							<input maxlength="20"  type="text" class="validate" id="username" name="username" value="<?php echo $value['username'] ?>" required="required" autofocus >
							<label for="username">Username</label>
						</div>
						<div class="input-field col s6">
							<i class="material-icons prefix">lock_outline</i>
							<input maxlength="20"  class="form-control" id="password"  name="password" value="<?php echo $value['password'] ?>" required="required" type="password">
							<label for="password">Contraseña</label>
						</div>
					</div>
							<div class="form-group">
								<label for="nombre" class="capitalize">Nombre:</label>
								<input class="form-control" id="nombre"  name="nombre" value="<?php echo $value['nombre'] ?>">
							</div>
							<div class="form-group">
								<label for="apellido" class="capitalize">Apellido:</label>
								<input class="form-control" id="apellido"  name="apellido" value="<?php echo $value['apellido'] ?>">
							</div>
							<div class="form-group" id="g-cedula">
								<label for="cedula" class="capitalize">Cedula:</label>
								<input class="form-control" id="cedula"  name="cedula" value="<?php echo $value['cedula'] ?>" required="required" >
								<input type="hidden" id="cedula-val" value="<?php echo $value['cedula'] ?>">
							</div>
							<div class="form-group" id="g-email">
								<label for="email" class="capitalize">Email:</label>
								<input class="form-control" id="email"  type="email" name="email" value="<?php echo $value['email'] ?>" required="required" >
								<input type="hidden" id="email-val" value="<?php echo $value['email'] ?>">
							</div>
							<div class="form-group">
								<label for="direccion" class="capitalize">Direccion:</label>
								<input class="form-control" id="direccion"  name="direccion" value="<?php echo $value['direccion'] ?>">
							</div>
							<div class="form-group">
								<label for="telefono" class="capitalize">Telefono:</label>
								<input class="form-control" id="telefono"  name="telefono" value="<?php echo $value['telefono'] ?>">
							</div>
												<div class="input-field">
						<select>
							<option value="1">Administrador</option>
							<option value="2">Default</option>
						</select>
						<label>Tipo de usuario:</label>
					</div>
							
							<div class="form-group" id="buttons">
								<button type="submit" class="btn btn-primary waves-effect waves-teal btn-flat">Guardar</button>
								<a href="<?php echo $base_url.'index.php/Admin/User/' ?>" class="btn btn-default waves-effect waves-teal">Cancelar</a>
							</div>
							<div class="alert alert-danger alert-dismissable" id="mss">
								Dato ya registrado.
							</div>
						
						</form>						<!-- /.row (nested) -->
						
						<!-- /.row (nested) -->
					</div>
					<?php endforeach ?>
					<!-- /.panel-body -->
				</div>
				<!-- /.panel -->
			</div>
			<!-- /.col-lg-12 -->
		</div>
		<!-- /.row -->
	</div>
</div>
	<script type="text/javascript">
	jQuery(document).ready(function($) {
		$('#mss').hide();
			var	validar = function(grupo, campo, url, campo_val){
			if (campo.val() != campo_val.val() ) {
		$.ajax({url: "<?php echo $base_url.'index.php/User/checkvalue/';?>"+url+campo.val(), type: 'POST', success: function(data){
		if(data == "1"){
		$('#buttons').hide();
		$('#mss').show();
		grupo.attr('class', 'form-group has-error');
		$('#form').attr('onsubmit', 'return false;');
		}
		} , cache:false
		});
		
		}else{
		$('#buttons').show();
		$('#mss').hide();
		grupo.attr('class', 'form-group');
		$('#form').attr('onsubmit', '');
		}
		}
		
		$('#cedula').on('blur', function() {
		validar($('#g-cedula'), $('#cedula'), "useradata/cedula/", $('#cedula-val'));
		});
		$('#username').on('blur', function() {
		validar($('#g-username'), $('#username'), "user/username/", $('#username-val'));
		});
		$('#email').on('blur', function() {
		validar($('#g-email'), $('#email'), "userdata/email/", $('#email-val'));
		});

		$('select').material_select();
	});
	</script>