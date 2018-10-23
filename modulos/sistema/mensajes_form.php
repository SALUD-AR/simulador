<form id="mensaje_form" method="POST" onsubmit="return mensaje_form_submit(this);">
  <?php if (permiso_check('/sistema/mensajes', "editar")) { ?>
  <div class="row">
    <div class="col-xs-6">
      <div class="form-group">
        <label for="mensaje_form_tipo" class="control-label">Tipo de usuario</label>
        <select name="mensaje_form_tipo" id="mensaje_form_tipo" class="form-control" onchange="mensaje_tipo_change(this);">
          <option value="0">Todos</option>
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
        <label for="mensaje_form_usuario" class="control-label">Usuario</label>
        <select name="mensaje_form_usuario" id="mensaje_form_usuario" class="form-control" disabled="disabled">
          <option value="0">Todos</option>
        </select>
      </div>
    </div>
  </div>
  <?php } ?>
  <div class="row">
    <div class="col-xs-12">
      <div class="form-group">
        <label for="mensaje_form_titulo" class="control-label">Asunto</label>
        <input type="text" name="mensaje_form_titulo" id="mensaje_form_titulo" class="form-control">
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-xs-12">
      <div class="form-group">
        <label for="mensaje_form_mensaje" class="control-label">Mensaje</label>
        <textarea class="form-control" name="mensaje_form_mensaje" id="mensaje_form_mensaje" rows="5"></textarea>
      </div>
    </div>
  </div>
</form>
<script type="text/javascript">
  function mensaje_form_submit(form) {
    var url_submit = '<?php echo encode_link($html_root."/sistema/mensajes_datos", array("accion" => "agregar_mensaje")); ?>';
    $.post(url_submit, $(form).serialize(), function(data) {
      if (data.trim() == "OK") {
        if (typeof(tabla) != 'undefined') {
          tabla.ajax.reload();
        }
        toastr.success('Mensaje enviado correctamente.');
        $('#popup_modal').modal('hide');
      }
      else {
        toastr.error(data);
      }
    }).fail(function() {
        toastr.error('Ocurrio un error al guardar los datos!');
    });
    return false;
  }

  <?php if (permiso_check('/sistema/mensajes', "editar")) { ?>
  function mensaje_tipo_change(select) {
    var id = parseInt($(select).val());
    if (id > 0) {
      var url = '<?php echo encode_link($html_root."/sistema/mensajes_datos", array("accion" => "options_usuarios")); ?>';
      $.post(url, { id_tipo: id }, function(data) {
        $('#mensaje_form_usuario').html(data);
        $('#mensaje_form_usuario').prop('disabled', false);
      }).fail(function() {
        $('#mensaje_form_usuario').html('<option value="0">Todos</option>');
        $('#mensaje_form_usuario').prop('disabled', true);
        BootstrapDialog.alert('Ocurrio un error al cargar los datos de los usuarios!');
      });
    }
    else {
      $('#mensaje_form_usuario').html('<option value="0">Todos</option>');
      $('#mensaje_form_usuario').prop('disabled', true);
    }
  }
  <?php } ?>

</script>