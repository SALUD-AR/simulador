<?php require_once(dirname(__FILE__)."/../../config.php"); 
require_once("funciones.php");?>

<div class="portlet light portlet-fit calendar">
  <div class="portlet-title">
    <div class="caption">
      <i class="fa fa-list-alt font-green"></i>
      <span class="caption-subject font-green sbold uppercase">Simulador de Codigos</span>
    </div> 
    <div class="actions">     
      <a class="btn btn-circle btn-success ajaxify" onclick="actualiza_seleccion()" title="Actualizar Seleccion">
        <i class="fa fa-plus-circle"></i> Actualizar Seleccion
      </a>                                      
    </div>
  </div>
  
                          <div class="portlet box green">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="fa fa-cogs"></i>Ingreso de Datos
                                    </div>
                                    
                                    <div class="tools">
                                        <a href="javascript:;" class="collapse" data-original-title="" title=""> </a>
                                        <a href="javascript:;" class="reload" data-original-title="" title=""> </a>
                                    </div>
                                </div>

                                <div class="portlet-body flip-scroll col-sm-12">
                                  <div class="portlet-body flip-scroll col-sm-6">
                                    <label for="sexo"> <label class="text-danger">(*)</label> Sexo</label>
                                    <select name="sexo" id="sexo" class="form-control">
                                      <option value="M"<?php echo ($sexo == 'M' ? 'selected="selected"' : ''); ?>>Masculino</option>
                                      <option value="F"<?php echo ($sexo == 'F' ? 'selected="selected"' : ''); ?>>Femenino</option>
                                    </select>
                                  </div>
                                  <div class="portlet-body flip-scroll col-sm-6">
                                      <label for="grupo_etareo"> <label class="text-danger">(*)</label> Grupo </label>
                                      <select name="grupo_etareo" id="grupo_etareo" class="form-control">
                                        <option value="1">Embarazo - Parto - Puerperio</option>
                                        <option value="2">0 a 5</option>
                                        <option value="3">6 a 9</option>
                                        <option value="4">Adolescente</option>
                                        <option value="5">Adulto</option>
                                      </select>
                                  </div>
                                </div>

                                <div class="portlet-body flip-scroll col-sm-12">
                                  <div class="portlet-body flip-scroll col-sm-6">
                                      <label for="procedimientos"><label class="text-danger">(*)</label> Procedimientos</label>
                                      <select id="proc" class="js-example-basic-multiple form-control" multiple="multiple">
                                        <?php 
                                          echo generar_options_proc();
                                        ?>
                                      </select>
                                  </div>
                                  <div class="portlet-body flip-scroll col-sm-6">
                                      <label for="esp"><label class="text-danger">(*)</label> Especialidad</label>
                                      <select id="esp" class="js-example-basic-multiple form-control" multiple="multiple">
                                        <?php 
                                          echo generar_options_esp();
                                        ?>
                                      </select>
                                  </div>
                                </div>

                                <div class="portlet-body flip-scroll col-sm-12">
                                  <div class="portlet-body flip-scroll col-sm-6">
                                      <label for="amb"><label class="text-danger">(*)</label> Ambito</label>
                                      <select id="amb" class="js-example-basic-multiple form-control" multiple="multiple">
                                        <?php 
                                          echo generar_options_amb();
                                        ?>
                                      </select>
                                  </div>
                                  <div class="portlet-body flip-scroll col-sm-6">
                                      <label for="diag"><label class="text-danger">(*)</label> Diagnostico</label>
                                      <select id="diag" class="js-example-basic-multiple form-control" multiple="multiple">
                                        <?php 
                                          echo generar_options_diag();
                                        ?>
                                      </select>
                                  </div>
                                </div>

                                <div class="portlet-body flip-scroll col-sm-12">
                                  <div class="portlet-body flip-scroll col-sm-6">
                                      <span class="label label-danger">(*) Criterios de Busqueda y Requeridos</span>                                      
                                  </div>
                                  <div class="portlet-body flip-scroll col-sm-6">                                      
                                  </div>
                                </div>
                                
                                <div class="portlet-body flip-scroll">
                                  <input type="button" class="btn green-sharp btn-outline  btn-block" value="Consultar" name="consultar" id="consultar" onclick="return consultar ()" />
                                </div>
                                <div class="portlet-body flip-scroll">
                                  <div id="res_pss" class="portlet-body flip-scroll">
                                  </div>
                                </div>
                            </div>
</div>

 
 <script type="text/javascript">

  $(document).ready(function() {
    $('.js-example-basic-multiple').select2()
  });

  function consultar() {
    var proc=$("#proc").select2("val");
    var esp=$("#esp").select2("val");
    var amb=$("#amb").select2("val");
    var diag=$("#diag").select2("val");
    var sexo=$('#sexo').val();
    var grupo_etareo=$('#grupo_etareo').val();

    if (!proc || !esp || !amb || !diag){
      toastr.error('Debe completar los campos!');
      return 0;
    }

    var url = '<?php echo encode_link($html_root."/pss/listado_datos", array("accion" => "busca_pss")); ?>';
    $.post(url, {
      proc: proc,
      esp: esp,
      amb: amb,
      diag: diag,
      sexo: sexo,
      grupo_etareo: grupo_etareo
    },
    function(data) {
      $('#res_pss').html(data);
    }
    ).fail(function() {
      toastr.error('Error al Buscar PSS!');
    });
    return 0;
  }

  function actualiza_seleccion(){
    var url = '<?php echo encode_link($html_root."/pss/listado_datos", array("accion" => "actualiza_seleccion")); ?>';
    $.post(url, {
    },
    function(data) {
      toastr.success(data);
    }
    ).fail(function() {
      toastr.error('Error al Buscar Actualizar!');
    });
    location.reload();
    return 0;
  }
  </script>
