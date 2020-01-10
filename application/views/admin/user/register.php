<?php echo $header ?>
	<div class="container">
		<div class="row">
			<?php echo form_open_multipart($action);?>
			<div class="col s12">
				<ul class="tabs">
					<li class="tab col s6"><a href="#test1">Usuario</a></li>
					<li class="tab col s6"><a href="#test2">Informacíon</a></li>
				</ul>
			</div>
			<div id="test1" class="col s12">
				<br><br>
				<div class="row userform">
					<div class="input-field col s12">
						<i class="material-icons prefix">perm_identity</i>
						<input maxlength="25"  type="text" class="validate" id="username" name="username" value="" required="required">
						<label for="username">Username</label>
					</div>
					<div class="input-field col s12">
						<i class="material-icons prefix">lock_outline</i>
						<input maxlength="25"  class="form-control" id="password"  name="password" value="" required="required" type="password">
						<label for="password">Contraseña</label>
					</div>
					<div class="input-field col s12">
						<input id="email" type="email" class="validate" name="email" value="" required="required" maxlength="255">
						<label for="email" data-error="wrong" data-success="right">Email</label>
					</div>
					<br>
					<div class="switch">
						<label>
							Bloqueado
							<input type="checkbox" id="status" name="status" checked="checked">
							<span class="lever"></span>
							Permitido
						</label>
					</div>
					<br>
					<br>
					<div class="input-field">
						<select name="usergroup">
							<?php
							foreach ($usergroups as $key => $value): ?>
							<option value="<?php echo $value['id'] ?>">
								<?php echo $value['name'] ?>
							</option>
							<?php endforeach ?>
						</select>
						<label>Tipo de usuario:</label>
					</div>
				</div>
			</div>
			<div id="test2" class="col s12">
				<div class="row userform">
					<br>
					<div class="form-group">
						<label for="nombre" class="capitalize">Nombre:</label>
						<input maxlength="20"  class="form-control" id="nombre"  name="nombre" value="">
					</div>
					<div class="form-group">
						<label for="apellido" class="capitalize">Apellido:</label>
						<input maxlength="20"  class="form-control" id="apellido"  name="apellido" value="">
					</div>
					<div class="form-group">
						<label for="direccion" class="capitalize">Direccion:</label>
						<input maxlength="200"  class="form-control" id="direccion"  name="direccion" value="">
					</div>
					<div class="form-group">
						<label for="telefono" class="capitalize">Telefono:</label>
						<input maxlength="50"  class="form-control" id="telefono" name="telefono" value="">
					</div>
				</div>
			</div>
			<div class="row userform">
			<div class="col s12" id="buttons">
				<a href="<?php echo $base_url.'index.php/Admin/User/' ?>" class="btn btn-default waves-effect waves-teal btn-flat">Cancelar</a>
				<button type="submit" class="btn btn-primary waves-effect waves-teal btn-flat">Guardar</button>
			</div>
			</div>
		</form>
	</div>
</div>