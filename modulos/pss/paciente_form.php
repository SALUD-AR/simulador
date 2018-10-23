<?php 
require_once("listado_funciones.php");
?>
<form id="paciente-form">
<div class="portlet light portlet-fit calendar">
  <div class="portlet-title">
    <div class="caption">
      <i class="icon-user font-green"></i>
      <span class="caption-subject font-green sbold uppercase"><?php echo $titulo; ?></span>
    </div>
  </div>
  <div class="portlet-body">
    <div class="row">
      <div class="col-md-12 col-lg-12">
        <div class="portlet box green">
          <div class="portlet-title">
            <div class="caption">
              Datos PSS
            </div>
          </div>
          <div class="portlet-body" id="panel_datos_personales">
            <input type="hidden" name="id_pss" id="id_pss" value="<?php echo $id_pss; ?>" />          
            <div class="row">
              <div class="col-sm-12 form-group">
                <label class="control-label" for="cod">Codigo</label>
                <input type="text" name="cod" class="form-control" id="cod" value="<?php echo $cod; ?>">
              </div>
            </div>
            <div class="row">
              <div class="col-sm-12 form-group">
                <label class="control-label" for="prestacion">Prestacion</label>
                <input type="text" name="prestacion" class="form-control" id="prestacion" value="<?php echo $prestacion; ?>">
              </div>
            </div>
            <div class="row">
              <div class="col-sm-12 form-group">
                <label class="control-label" for="procedimiento">Procedimiento</label>
                <input type="text" name="procedimiento" class="form-control" id="procedimiento" value="<?php echo $procedimiento; ?>">
              </div>
            </div>
            <div class="row">
              <div class="col-sm-12 form-group">
                <label class="control-label" for="cod_procedimiento">Codigo Procedimiento</label>
                <input type="text" name="cod_procedimiento" class="form-control" id="cod_procedimiento" value="<?php echo $cod_procedimiento; ?>">
              </div>
            </div>
            <div class="row">
              <div class="col-sm-12 form-group">
                <label class="control-label" for="especialidad">Especialidad</label>
                <input type="text" name="especialidad" class="form-control" id="especialidad" value="<?php echo $especialidad; ?>">
              </div>
            </div>
            <div class="row">
              <div class="col-sm-12 form-group">
                <label class="control-label" for="cod_especialidad">Codigo Especialidad</label>
                <input type="text" name="cod_especialidad" class="form-control" id="cod_especialidad" value="<?php echo $cod_especialidad; ?>">
              </div>
            </div>
            <div class="row">
              <div class="col-sm-12 form-group">
                <label class="control-label" for="ambito">Ambito</label>
                <input type="text" name="ambito" class="form-control" id="ambito" value="<?php echo $ambito; ?>">
              </div>
            </div>
            <div class="row">
              <div class="col-sm-12 form-group">
                <label class="control-label" for="cod_ambito">Codigo Ambito</label>
                <input type="text" name="cod_ambito" class="form-control" id="cod_ambito" value="<?php echo $cod_ambito; ?>">
              </div>
            </div>
            <div class="row">
              <div class="col-sm-12 form-group">
                <label class="control-label" for="diagnostico">Diagnostico</label>
                <input type="text" name="diagnostico" class="form-control" id="diagnostico" value="<?php echo $diagnostico; ?>">
              </div>
            </div>
            <div class="row">
              <div class="col-sm-12 form-group">
                <label class="control-label" for="cod_diagnostico">Codigo Diagnostico</label>
                <input type="text" name="cod_diagnostico" class="form-control" id="cod_diagnostico" value="<?php echo $cod_diagnostico; ?>">
              </div>
            </div>
            <div class="row">
              <div class="col-sm-12 form-group">
                <label class="control-label" for="sexo">Sexo</label>
                <select name="sexo" id="sexo" class="form-control">
                  <option value="M"<?php echo ($sexo == 'M' ? 'selected="selected"' : ''); ?>>Masculino</option>
                  <option value="F"<?php echo ($sexo == 'F' ? 'selected="selected"' : ''); ?>>Femenino</option>
                  <option value=""<?php echo ($sexo == '' ? 'selected="selected"' : ''); ?>>Ambos</option>
                </select>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-12 form-group">
                <label class="control-label" for="edad">Edad</label>
                <input type="text" name="edad" class="form-control" id="edad" value="<?php echo $edad; ?>">
              </div>
            </div>
            <div class="row">
              <div class="col-sm-12 form-group">
                <label class="control-label" for="grupo_etareo">Grupo</label>
                <select name="grupo_etareo" id="grupo_etareo" class="form-control">
                  <option value="1"<?php echo ($grupo_etareo == '1' ? 'selected="selected"' : ''); ?>>Embarazo - Parto - Puerperio</option>
                  <option value="2"<?php echo ($grupo_etareo == '2' ? 'selected="selected"' : ''); ?>>0 a 5</option>
                  <option value="3"<?php echo ($grupo_etareo == '3' ? 'selected="selected"' : ''); ?>>6 a 9</option>
                  <option value="4"<?php echo ($grupo_etareo == '4' ? 'selected="selected"' : ''); ?>>Adolescente</option>
                  <option value="5"<?php echo ($grupo_etareo == '5' ? 'selected="selected"' : ''); ?>>Adulto</option>
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

<?php if ($id_pss){?>
  <div class="portlet-body">
    <div class="row">
      <div class="col-md-12 col-lg-12">
        <div class="portlet box green">
          <div class="portlet-title">
            <div class="caption">
              Datos Reportables
            </div>
          </div>
          <div class="portlet-body" id="panel_datos_reportables">
            <div class="row">
              <div class="col-sm-12 form-group">
                <div class="portlet box blue">
                  <div class="portlet-title">
                    <div class="caption">
                      <i class="fa fa-stethoscope"></i>Listado de Datos</div>
                    <div class="tools">
                      <a href="javascript:;" class="expand" data-original-title="" title=""> </a>
                      <a href="javascript:;" class="fullscreen" data-original-title="" title=""> </a>
                    </div>
                  </div>
                  <div class="portlet-body" style="display: block;">
                    <div id="listado-reportables">
                      <?php echo datos_reportables($id_pss); ?>
                    </div>
                    <br/>
                    <div class="row">
                      <div class="col-xs-12 text-center">
                        <button id="btn-agregar-report" class="btn green-jungle btn-sm">
                          <i class="fa fa-plus"></i> Agregar
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <div class="portlet-body">
    <div class="row">
      <div class="col-md-12 col-lg-12">
        <div class="portlet box green">
          <div class="portlet-title">
            <div class="caption">
              Comentarios
            </div>
          </div>
          <div class="portlet-body" id="panel_datos_reportables">
            <div class="row">
              <div class="col-sm-12 form-group">
                <div class="portlet box blue">
                  <div class="portlet-title">
                    <div class="caption">
                      <i class="fa fa-stethoscope"></i>Comentarios</div>
                    <div class="tools">
                      <a href="javascript:;" class="expand" data-original-title="" title=""> </a>
                      <a href="javascript:;" class="fullscreen" data-original-title="" title=""> </a>
                    </div>
                  </div>
                  <div class="portlet-body" style="display: block;">
                    <div id="listado-comentarios">
                      <?php echo comentarios($id_pss); ?>
                    </div>
                    <br/>
                    <div class="row">
                      <div class="col-xs-12 text-center">
                        <button id="btn-comentarios-report" class="btn green-jungle btn-sm">
                          <i class="fa fa-plus"></i> Agregar
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
<?php }?>

</div>
<div class="row">
  <div class="col-lg-2 col-lg-offset-3 col-xs-3 col-xs-offset-2">
    <input type="submit" class="btn btn-success form-control" value="Guardar"/>
  </div>
  <div class="col-lg-2 col-lg-offset-2 col-xs-3 col-xs-offset-2">
    <a href="<?php echo $html_root; ?>/pss/listado_pss" class="btn btn-default form-control ajax-load" data-titulo="<i class='fa fa-users'></i> <span>listado</span>">Cancelar</a>
  </div>
</div>
<br/>
</form>


<script type="text/javascript">
  var tabla = null;

  $(document).ready(function() {

    $('#paciente-form').on('submit', function(e) {
      e.preventDefault();
      var url = '<?php echo encode_link($html_root."/pss/listado_datos", array("accion" => "paciente_form_submit")); ?>';
      
      loading_fade = false;
      $.post(url, $(this).serialize(), function(data) {
        if (data.trim() == "OK") {
          toastr.success('Datos guardados correctamente!');
          cargar_pagina("<?php echo $html_root; ?>/pss/listado_pss");
        }
        else {
          toastr.error(data);
        }

      }).fail(function() {
        toastr.error('Ocurrio un error al guardar los datos del PSS!');
      });

    });
  });

  $("#btn-agregar-report").on("click", function(e) {
      e.preventDefault();
      App.showPopupModal({
        title: 'Agregar Dato Reportable',
        url: '<?php echo encode_link($html_root."/pss/listado_datos", array("accion" => "reportables_agregar_form")); ?>',
        form_data: { 
          id_pss: <?php echo $id_pss; ?>
        }
      });
    });

  $("#btn-comentarios-report").on("click", function(e) {
      e.preventDefault();
      App.showPopupModal({
        title: 'Agregar Comentarios',
        url: '<?php echo encode_link($html_root."/pss/listado_datos", array("accion" => "comentarios_agregar_form")); ?>',
        form_data: { 
          id_pss: <?php echo $id_pss; ?>
        }
      });
    });

  function reportables_form_submit(form) {
    var url = '<?php echo encode_link($html_root."/pss/listado_datos", array("accion" => "reportables_agregar")); ?>';
    var desc = $(form).find('#descri_reportables').val();
    if (desc == '') {
      toastr.error('Debe ingresar la descripci&oacute;n del Dato!');
      return false;
    }
    $.post(url, $(form).serialize(), function(data) {
      $('#listado-reportables').html(data);
      App.initComponents();
    }).fail(function() {
      toastr.error('Ocurrio un error al guardar los datos!');
    });
    $('#popup_modal').modal('hide');
    return false;
  }

  function comentarios_form_submit(form) {
    var url = '<?php echo encode_link($html_root."/pss/listado_datos", array("accion" => "comentarios_agregar")); ?>';
    var desc = $(form).find('#descri_comentarios').val();
    if (desc == '') {
      toastr.error('Debe ingresar la descripci&oacute;n del Dato!');
      return false;
    }
    $.post(url, $(form).serialize(), function(data) {
      $('#listado-comentarios').html(data);
      App.initComponents();
    }).fail(function() {
      toastr.error('Ocurrio un error al guardar los datos!');
    });
    $('#popup_modal').modal('hide');
    return false;
  }

  function elimina_report(id){
    var url = '<?php echo encode_link($html_root."/pss/listado_datos", array("accion" => "eliminar_reportable")); ?>';
    $.post(url, {
        id: id,
      },
      function(data) {
        if (data.trim() == "OK") {
          toastr.success('Reportable eliminado correctamente');
          cargar_pagina("<?php echo $html_root; ?>/pss/listado_pss");
        }
        else {
          toastr.error(data);
        }
      }
    ).fail(function() {
        toastr.error('Ocurri&oacute; un error al eliminar el Reportable!');
    });
  }

  function elimina_coment(id){
    var url = '<?php echo encode_link($html_root."/pss/listado_datos", array("accion" => "eliminar_comentario")); ?>';
    $.post(url, {
        id: id,
      },
      function(data) {
        if (data.trim() == "OK") {
          toastr.success('Comentario eliminado correctamente');
          cargar_pagina("<?php echo $html_root; ?>/pss/listado_pss");
        }
        else {
          toastr.error(data);
        }
      }
    ).fail(function() {
        toastr.error('Ocurri&oacute; un error al eliminar el Reportable!');
    });
  }
</script>