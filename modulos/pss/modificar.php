<?php 
require_once(dirname(__FILE__)."/../../config.php");


if (isset($parametros["id_pss"])) {
  $id_pss = $parametros["id_pss"];
}
if (empty($id_pss)) {
  $id_pss = $_POST["id"];
}

if (empty($id_pss)) {
  die('<div class="alert alert-danger alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
          <h4><i class="icon fa fa-ban"></i> ERROR</h4>
          No se ha seleccionado un PSS.
        </div>');
}

if ($id_pss > 0) {
  $query="SELECT 
            id_pss, 
            cod, 
            prestacion, 
            procedimiento, 
            cod_procedimiento, 
            especialidad, 
            cod_especialidad, 
            ambito, 
            cod_ambito, 
            diagnostico, 
            cod_diagnostico, 
            sexo, 
            edad,
            grupo_etareo
          FROM simulador.pss
          WHERE
            id_pss = $id_pss";
  $res = sql($query) or die($db->ErrorMsg());
  if ($res->recordCount() == 1) {
    $cod = $res->fields['cod'];
    $prestacion = $res->fields['prestacion'];
    $procedimiento = $res->fields['procedimiento'];
    $cod_procedimiento = $res->fields['cod_procedimiento'];
    $especialidad = $res->fields['especialidad'];
    $cod_especialidad = $res->fields['cod_especialidad'];
    $ambito = $res->fields['ambito'];
    $cod_ambito = $res->fields['cod_ambito'];
    $diagnostico = $res->fields['diagnostico'];
    $cod_diagnostico = $res->fields['cod_diagnostico'];
    $sexo = trim($res->fields['sexo']);
    $edad = trim($res->fields['edad']);
    $grupo_etareo = trim($res->fields['grupo_etareo']);
    $titulo = "Modificar Paciente";
    include_once("paciente_form.php");
  }
  else {
    echo '<ul class="list-group"><li class="list-group-item alert alert-warning" role="alert">Error: El PSS seleccionado no existe.</li></ul>';
  }
}
else {
  echo '<ul class="list-group"><li class="list-group-item alert alert-warning" role="alert">Error: No se ha seleccionado un PSS para modificar.</li></ul>';
}

?>