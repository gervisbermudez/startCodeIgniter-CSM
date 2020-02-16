@extends('admin.layouts.app')

@section('title', $title)

@section('header')
@include('admin.shared.header')
@endsection

@section('content')
<?php
	function get($data = array(), $key)
	{
		if ($data) {
			if (array_key_exists($key,$data)) {
				return $data[$key];
			}
		}
		return false;
	}
?>
<div class="container">
	<div class="row">
		<div class="col s12 m10 l10">
			<?php echo form_open_multipart($action);?>
			<div id="initialization" class="section scrollspy">
				<h4 class="section-header">Perfil</h4>
				<div class="input-field">
					<input maxlength="25" type="text" id="username" name="username"
						value="<?php echo get($userdata[0], 'username'); ?>" required="required"
						data-value="<?php echo get($userdata[0], 'username'); ?>">
					<label data-error="Username usado" data-success="Username valido">Username</label>
				</div>
				<div class="input-field ">
					<input maxlength="25" id="password" name="password" value="" required="required" type="password">
					<label>Contraseña</label>
				</div>
				<div class="input-field">
					<input id="email" type="email" name="email" value="<?php echo get($userdata[0], 'email'); ?>"
						required="required" maxlength="255" data-value="<?php echo get($userdata[0], 'email'); ?>">
					<label data-error="Email usado" data-success="Valido">Email</label>
				</div>
				<div class="input-field">
					<select name="usergroup">
						<?php
						foreach ($usergroups as $key => $value): ?>
						<option value="<?php echo $value['id'] ?>"><?php echo $value['name'] ?></option>
						<?php endforeach ?>
					</select>
					<label>Tipo de usuario:</label>
				</div>
			</div>
			<div id="structure" class="section scrollspy">
				<h4 class="section-header">Información de Perfil</h4>

				<div class="input-field">
					<input maxlength="20" id="nombre" type="text" name="nombre"
						value="<?php echo get($userdata[0], 'nombre'); ?>">
					<label>Nombre:</label>
				</div>
				<div class="input-field">

					<input maxlength="20" type="text" id="apellido" name="apellido"
						value="<?php echo get($userdata[0], 'apellido'); ?>"><label for="apellido">Apellido:</label>
				</div>
				<div class="input-field">

					<input maxlength="200" type="text" id="direccion" name="direccion"
						value="<?php echo get($userdata[0], 'direccion'); ?>"><label for="direccion">Direccion:</label>
				</div>
				<div class="input-field">
					<input maxlength="50" type="text" id="telefono" name="telefono"
						value="<?php echo get($userdata[0], 'telefono'); ?>"><label for="telefono">Telefono:</label>
				</div>
				<br><br>
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
				<div class="row userform">
					<div class="col s12" id="buttons">
						<a href="<?php echo base_url('index.php/Admin/User/'); ?>"
							class="btn btn-default waves-effect waves-teal btn-flat">Cancelar</a>
						<button type="submit" class="btn btn-primary waves-effect waves-teal">Guardar</button>
					</div>
				</div>

				<input type="hidden" name="mode" value="<?php echo $mode; ?>">
				<input type="hidden" name="id" value="<?php echo get($userdata[0], 'id'); ?>">
			</div>
			</form>
		</div>
		<div class="col hide-on-small-only m2 l2">
			<ul class="section table-of-contents tabs-wrapper">
				<li><a href="#initialization">Perfil</a></li>
				<li><a href="#structure">Información</a></li>
			</ul>
		</div>
	</div>
</div>
@endsection