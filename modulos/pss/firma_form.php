<input type="hidden" name="firma_form_id" id="firma_form_id" value="<?php echo $form_id; ?>" />
<div class="row">
  <div class="col-xs-12">
    <div class="form-group">
      <label for="firma_form_nrodoc" class="control-label">N&uacute;mero de documento</label>
      <input type="text" maxlength="32" class="form-control" name="firma_form_nrodoc" id="firma_form_nrodoc" disabled value="<?php echo $form_nrodoc; ?>" />
    </div>
  </div>
</div>

<div class="row">
  <div class="col-xs-6">
      <div class="form-group">
        <label for="firma_form_nombre" class="control-label">Nombres</label>
        <input type="text" class="form-control" name="firma_form_nombre" id="firma_form_nombre" disabled value="<?php echo $form_nombres; ?>" />
      </div>
    </div>
    <div class="col-xs-6">
      <div class="form-group">
        <label for="firma_form_apellido" class="control-label">Apellido</label>
        <input type="text" class="form-control" name="firma_form_apellido" id="firma_form_apellido" disabled value="<?php echo $form_apellido; ?>" />
      </div>
    </div>
</div>

<div class="row">
  <div class="portlet-body flip-scroll col-sm-6">
    <label for="sexo"> <label class="text-danger"></label> Sexo</label>
    <select name="sexo" id="sexo" class="form-control" disabled>
        <option value="M"<?php echo ($form_sexo == 'M' ? 'selected="selected"' : ''); ?>>Masculino</option>
        <option value="F"<?php echo ($form_sexo == 'F' ? 'selected="selected"' : ''); ?>>Femenino</option>
    </select>
  </div>

  <div class="col-xs-6">		
	  <div class="form-group">
      <label for="firma_form_diagnostico" class="control-label">Diagn&oacute;stico</label>
      <input type="text" class="form-control" name="firma_form_diagnostico" id="firma_form_diagnostico" disabled value="<?php echo $form_diagnostico; ?>" />
    </div>
  </div>
</div>

<div class="row">
  <div class="col-xs-12">
    <div class="form-group">
      <label for="firma_form_evolucion" class="control-label">Evoluci&oacute;n</label>
      <textarea class="form-control" name="firma_form_evolucion" id="firma_form_evolucion" disabled><?php echo $form_evolucion; ?></textarea>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-xs-12">
    <div class="form-group">
      <label for="firma_form_status" class="control-label">Estado</label>
      <input type="text" class="form-control" name="firma_form_status" id="firma_form_status" disabled value="<?php echo $form_status; ?>" />
    </div>
  </div>
</div>

<script type="text/javascript">
  <?php if (empty($form_id)) { ?>
  var url_submit = '<?php echo encode_link($html_root."/sistema/firmas_datos", array("accion" => "agregar_firma")); ?>';
  <?php } else { ?>
  var url_submit = '<?php echo encode_link($html_root."/sistema/firmas_datos", array("accion" => "modificar_firma")); ?>';
  <?php } ?>
  function firma_form_submit(form) {
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


