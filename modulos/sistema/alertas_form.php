<input type="hidden" name="alerta_form_id" id="alerta_form_id" value="<?php echo $form_id; ?>" />
<div class="row">
  <div class="col-xs-6">
    <div class="form-group">
      <label for="alerta_form_tipo" class="control-label">Tipo de usuario</label>
      <select name="alerta_form_tipo" id="alerta_form_tipo" class="form-control" onchange="alerta_tipo_change(this);">
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
      <label for="alerta_form_usuario" class="control-label">Usuario</label>
      <select name="alerta_form_usuario" id="alerta_form_usuario" class="form-control" disabled="disabled">
        <option value="0">Todos</option>
      </select>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-xs-12">
    <div class="form-group">
      <label for="alerta_form_mensaje" class="control-label">Mensaje</label>
      <textarea class="form-control" name="alerta_form_mensaje" id="alerta_form_mensaje" rows="5"></textarea>
    </div>
  </div>
</div>
<script type="text/javascript">
  var url_submit = '<?php echo encode_link($html_root."/sistema/alertas_datos", array("accion" => "agregar_alerta")); ?>';

  function alerta_form_submit(form) {
    loading_fade = false;
    $.post(url_submit, $(form).serialize(), function(data) {
      if (data.trim() == "OK") {
        if (typeof(tabla) != 'undefined') {
          tabla.ajax.reload();
        }
        notify_success('Alerta generada correctamente.');
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

  function alerta_tipo_change(select) {
    var id = parseInt($(select).val());
    if (id > 0) {
      var url = '<?php echo encode_link($html_root."/sistema/alertas_datos", array("accion" => "options_usuarios")); ?>';
      loading_fade = false;
      $.post(url, { id_tipo: id }, function(data) {
        $('#alerta_form_usuario').html(data);
        $('#alerta_form_usuario').prop('disabled', false);
      }).fail(function() {
        $('#alerta_form_usuario').html('<option value="0">Todos</option>');
        $('#alerta_form_usuario').prop('disabled', true);
        BootstrapDialog.alert('Ocurrio un error al cargar los datos de los usuarios!');
      });
    }
    else {
      $('#alerta_form_usuario').html('<option value="0">Todos</option>');
      $('#alerta_form_usuario').prop('disabled', true);
    }
  }

</script>