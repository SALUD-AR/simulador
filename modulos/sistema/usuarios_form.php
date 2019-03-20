<input type="hidden" name="usuario_form_id" id="usuario_form_id" value="<?php echo $form_id; ?>" />
<div class="row">
  <div class="col-xs-6">
    <div class="form-group">
      <label for="usuario_form_login" class="control-label">Nombre de usuario</label>
      <input type="text" maxlength="32" class="form-control" name="usuario_form_login" id="usuario_form_login" value="<?php echo $form_login; ?>" />
    </div>
  </div>
  <div class="col-xs-6">
    <div class="form-group">
      <label for="usuario_form_passwd1" class="control-label">Contrase&ntilde;a</label>
      <input type="password" class="form-control" name="usuario_form_passwd1" id="usuario_form_passwd1" />
    </div>
  </div>
</div>
<div class="row">
  <div class="col-xs-6">
    <div class="form-group">
      <label for="usuario_form_tipo" class="control-label">Tipo de usuario</label>
      <select name="usuario_form_tipo" id="usuario_form_tipo" class="form-control">
        <?php 
        foreach ($form_tipos as $tipo) {
          echo '<option value="'.$tipo["id"].'"';
          if ($form_tipo == $tipo["id"]) {
            echo ' selected="selected"';
          }
          echo '>'.$tipo["descripcion"].'</option>';
        }
        ?>
      </select>
    </div>
  </div>
  <div class="col-xs-6">
    <div class="form-group">
      <label for="usuario_form_passwd2" class="control-label">Confirmaci&oacute;n de contrase&ntilde;a</label>
      <input type="password" class="form-control" name="usuario_form_passwd2" id="usuario_form_passwd2" />
    </div>
  </div>
</div>
<div class="row">
  <div class="col-xs-6">
    <div class="form-group">
      <label for="usuario_form_apellido" class="control-label">Apellido</label>
      <input type="text" class="form-control" name="usuario_form_apellido" id="usuario_form_apellido" value="<?php echo $form_apellido; ?>" />
    </div>
  </div>
  <div class="col-xs-6">
    <div class="form-group">
      <label for="usuario_form_nombre" class="control-label">Nombre</label>
      <input type="text" class="form-control" name="usuario_form_nombre" id="usuario_form_nombre" value="<?php echo $form_nombre; ?>" />
    </div>
  </div>
</div>
<div class="row">
  <div class="col-xs-12">
    <div class="form-group">
      <label for="usuario_form_direccion" class="control-label">Direcci&oacute;n</label>
      <textarea class="form-control" name="usuario_form_direccion" id="usuario_form_direccion"><?php echo $form_direccion; ?></textarea>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-xs-6">
    <div class="form-group">
      <label for="usuario_form_telefono" class="control-label">Tel&eacute;fono</label>
      <input type="text" class="form-control" name="usuario_form_telefono" id="usuario_form_telefono" value="<?php echo $form_telefono; ?>" />
    </div>
  </div>
  <div class="col-xs-6">
    <div class="form-group">
      <label for="usuario_form_email" class="control-label">Email</label>
      <input type="text" class="form-control" name="usuario_form_email" id="usuario_form_email" value="<?php echo $form_email; ?>" />
    </div>
  </div>
</div>
<div class="row">
  <div class="col-xs-12">
    <div class="form-group">
      <label for="usuario_form_cuil" class="control-label">CUIL</label>
      <textarea class="form-control" name="usuario_form_cuil" id="usuario_form_cuil"><?php echo $form_cuil; ?></textarea>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-xs-12">
    <div class="form-group">
      <label for="usuario_form_observaciones" class="control-label">Observaciones</label>
      <textarea class="form-control" name="usuario_form_observaciones" id="usuario_form_observaciones"><?php echo $form_observaciones; ?></textarea>
    </div>
  </div>
</div>
<script type="text/javascript">
  <?php if (empty($form_id)) { ?>
  var url_submit = '<?php echo encode_link($html_root."/sistema/usuarios_datos", array("accion" => "agregar_usuario")); ?>';
  <?php } else { ?>
  var url_submit = '<?php echo encode_link($html_root."/sistema/usuarios_datos", array("accion" => "modificar_usuario")); ?>';
  <?php } ?>
  function usuario_form_submit(form) {
    loading_fade = false;
    $.post(url_submit, $(form).serialize(), function(data) {
      if (data.trim() == "OK") {
        if (typeof(tabla) != 'undefined') {
          tabla.ajax.reload();
        }
        notify_success('Datos guardados correctamente.');
        $('#popup_modal').modal('hide');
      }
      else {
        BootstrapDialog.alert(data);
      }
    }).fail(function() {
        notify_danger('Ocurrio un error al guardar los datos!');
    });
    $('#popup_modal').modal('hide');
    return false;
  }

</script>