<?php
require_once(dirname(__FILE__)."/../../config.php");
require_once("listado_funciones.php");

if (isset($parametros["accion"])) {
  switch ($parametros["accion"]) {
    
    case 'busca_sn':
      $buscar = $_GET["q"];
      $buscar=trim($buscar);
      $buscar=str_replace(' ', ':*&',$buscar).':*';      
      $buscar=str_replace('(', '',$buscar);
      $buscar=str_replace(')', '',$buscar);
      $query = "select * from snomed.term('$buscar')";
      $res_snomed = sql($query, "al obtener los datos terminologias") or die();
      $total_count = $res_snomed->recordCount();
      $res_json = array(
                    "total_count"          => $total_count,
                    "incomplete_results"   => 'false',
                    "items"                => array()
                    );
      while (!$res_snomed->EOF) {
        $res_json['items'][]= array(
                                "id"     => $res_snomed->fields["conceptid"],
                                "id_tabla"            => $res_snomed->fields["id"],
                                "term"          => $res_snomed->fields["term"],
                              );
        $res_snomed->MoveNext();
      }
      echo json_encode($res_json);
    break;

    case 'listado':
      $res_json = array();
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
              FROM simulador.pss;";
      $res = sql($query) or die($db->ErrorMsg());

      while (!$res->EOF) {
        
        switch (trim($res->fields["grupo_etareo"])) {
          case 1:
            $grupo_etareo = "Emb Par Puer";
            break;
          case 2:
            $grupo_etareo = "0 a 5";
            break;
          case 3:
            $grupo_etareo = "6 a 9";
            break;
          case 4:
            $grupo_etareo = "Adolescente";
            break;
          case 5:
            $grupo_etareo = "Adulto";
            break;
          default:
            $grupo_etareo = "Sin Dato";
        }

        if (trim($res->fields["sexo"])=='') $sexo = 'Ambos';
        if (trim($res->fields["sexo"])=='M') $sexo = 'M';
        if (trim($res->fields["sexo"])=='F') $sexo = 'F';

        $res_json[] = array(
          "id"                => trim($res->fields["id_pss"]),
          "cod"               => trim($res->fields["cod"]),
          "prestacion"        => trim($res->fields["prestacion"]),
          "procedimiento"     => trim($res->fields["procedimiento"]),
          "cod_procedimiento" => trim($res->fields["cod_procedimiento"]),
          "especialidad"      => trim($res->fields["especialidad"]),
          "cod_especialidad"  => trim($res->fields["cod_especialidad"]),
          "ambito"            => trim($res->fields["ambito"]),
          "cod_ambito"        => trim($res->fields["cod_ambito"]),
          "diagnostico"       => trim($res->fields["diagnostico"]),
          "cod_diagnostico"   => trim($res->fields["cod_diagnostico"]),
          "sexo"              => $sexo,
          "grupo_etareo"      => $grupo_etareo
        );
        $res->MoveNext();
      }
      // echo json_encode(array("data" => $res_json, "query" => $query));
      echo json_encode(array("data" => $res_json));
      break;

    case 'eliminar_pss':
      $id_pss = intval($_POST["id_pss"]);
      $ret = '';
      if ($id_pss > 0) {
          $query_del = "DELETE FROM simulador.pss WHERE id_pss=$id_pss;";
          $res_del = sql($query_del, "Error al eliminar el Paciente");

          $query_log="INSERT INTO 
                        simulador.log_pss
                      (id_pss, accion, fecha, usuario)
                      VALUES 
                      ($id_pss, 'Elimina PSS', current_timestamp, {$usuario->id});"; 
          $res_query_log = sql($query_log) or die($db->ErrorMsg());

          if ($res_del && $db->Affected_Rows() == 1) {
            $ret .= "OK";
          }
          else {
            $ret .= "Ocurri&oacute un error al eliminar el PSS!";
          }
      }
      else {
        $ret .= 'Par&aacute;metros incorrectos';
      }
      echo $ret;
      break; 

    case 'eliminar_reportable':
      $id = intval($_POST["id"]);
      $ret = '';
      if ($id > 0) {
          $query_del = "DELETE FROM simulador.datos_reportables WHERE id=$id;";
          $res_del = sql($query_del, "Error al eliminar el Reportable");

          $query_log="INSERT INTO 
                        simulador.log_pss
                      (accion, fecha, usuario)
                      VALUES 
                      ('Elimina Reportable', current_timestamp, {$usuario->id});"; 
          $res_query_log = sql($query_log) or die($db->ErrorMsg());

          if ($res_del && $db->Affected_Rows() == 1) {
            $ret .= "OK";
          }
          else {
            $ret .= "Ocurri&oacute un error al eliminar el Reportable!";
          }
      }
      else {
        $ret .= 'Par&aacute;metros incorrectos';
      }
      echo $ret;
      break;  

    case 'eliminar_comentario':
      $id = intval($_POST["id"]);
      $ret = '';
      if ($id > 0) {
          $query_del = "DELETE FROM simulador.comentarios WHERE id=$id;";
          $res_del = sql($query_del, "Error al eliminar el comentarios");

          $query_log="INSERT INTO 
                        simulador.log_pss
                      (accion, fecha, usuario)
                      VALUES 
                      ('Elimina comentarios', current_timestamp, {$usuario->id});"; 
          $res_query_log = sql($query_log) or die($db->ErrorMsg());

          if ($res_del && $db->Affected_Rows() == 1) {
            $ret .= "OK";
          }
          else {
            $ret .= "Ocurri&oacute un error al eliminar el comentarios!";
          }
      }
      else {
        $ret .= 'Par&aacute;metros incorrectos';
      }
      echo $ret;
      break;   

    case 'clonar_pss':
      $id_pss = intval($_POST["id_pss"]);
      $ret = '';
      if ($id_pss > 0) {
          $query_clone = "INSERT INTO simulador.pss 
                          (cod, 
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
                          grupo_etareo) 
                            (SELECT cod, 
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
                            WHERE id_pss=$id_pss)";
          $res_clone = sql($query_clone, "Error al clonar el Paciente");

          $query_log="INSERT INTO 
                        simulador.log_pss
                      (id_pss, accion, fecha, usuario)
                      VALUES 
                      ($id_pss, 'Clona registro', current_timestamp, {$usuario->id});"; 
          $res_query_log = sql($query_log) or die($db->ErrorMsg());

          if ($res_clone && $db->Affected_Rows() == 1) {
            $ret .= "OK";
          }
          else {
            $ret .= "Ocurri&oacute un error al clonar el PSS!";
          }
      }
      else {
        $ret .= 'Par&aacute;metros incorrectos';
      }
      echo $ret;
    break;   

    case 'actualiza_seleccion':
      $ret= 'Se Actualizo Correctamente';

      $query_truncar1="TRUNCATE TABLE simulador.proc;";
      sql($query_truncar1) or die($db->ErrorMsg());
      $query_truncar2="TRUNCATE TABLE simulador.proc_aux;";
      sql($query_truncar2) or die($db->ErrorMsg());
      $query="select cod_procedimiento from simulador.pss where trim(cod_procedimiento) !='';";
      $res_query = sql($query) or die($db->ErrorMsg());
      while (!$res_query->EOF) {          
          $arr_ret= explode ("|",trim($res_query->fields['cod_procedimiento']));
          $arr_ret=array_filter($arr_ret, "strlen");          
          //saco el numero de elementos
          $longitud = count($arr_ret);           
          //Recorro todos los elementos
          for($i=0; $i<$longitud; $i++){
              if ($i%2){
                $val1=$arr_ret[$i-1];
                $val2=$arr_ret[$i];
                $query_insert="INSERT INTO simulador.proc_aux(cod, descri)VALUES ('$val1', '$val2');";
                sql($query_insert) or die($db->ErrorMsg()); 
              }
          }          
          $res_query->MoveNext();
      }
      $query_insert_fin="INSERT INTO simulador.proc (descri) select distinct trim(descri) from simulador.proc_aux";
      sql($query_insert_fin) or die($db->ErrorMsg());

      $query_truncar1="TRUNCATE TABLE simulador.esp;";
      sql($query_truncar1) or die($db->ErrorMsg());
      $query_truncar2="TRUNCATE TABLE simulador.esp_aux;";
      sql($query_truncar2) or die($db->ErrorMsg());
      $query="select cod_especialidad from simulador.pss where trim(cod_especialidad) !='';";
      $res_query = sql($query) or die($db->ErrorMsg());
      while (!$res_query->EOF) {          
          $arr_ret= explode ("|",trim($res_query->fields['cod_especialidad']));
          $arr_ret=array_filter($arr_ret, "strlen");          
          //saco el numero de elementos
          $longitud = count($arr_ret);           
          //Recorro todos los elementos
          for($i=0; $i<$longitud; $i++){
              if ($i%2){
                $val1=$arr_ret[$i-1];
                $val2=$arr_ret[$i];
                $query_insert="INSERT INTO simulador.esp_aux(cod, descri)VALUES ('$val1', '$val2');";
                sql($query_insert) or die($db->ErrorMsg()); 
              }
          }          
          $res_query->MoveNext();
      }
      $query_insert_fin="INSERT INTO simulador.esp (descri) select distinct trim(descri) from simulador.esp_aux";
      sql($query_insert_fin) or die($db->ErrorMsg());

      $query_truncar1="TRUNCATE TABLE simulador.diag;";
      sql($query_truncar1) or die($db->ErrorMsg());
      $query_truncar2="TRUNCATE TABLE simulador.diag_aux;";
      sql($query_truncar2) or die($db->ErrorMsg());
      $query="select cod_diagnostico from simulador.pss where trim(cod_diagnostico) !='';";
      $res_query = sql($query) or die($db->ErrorMsg());
      while (!$res_query->EOF) {          
          $arr_ret= explode ("|",trim($res_query->fields['cod_diagnostico']));
          $arr_ret=array_filter($arr_ret, "strlen");          
          //saco el numero de elementos
          $longitud = count($arr_ret);           
          //Recorro todos los elementos
          for($i=0; $i<$longitud; $i++){
              if ($i%2){
                $val1=$arr_ret[$i-1];
                $val2=$arr_ret[$i];
                $query_insert="INSERT INTO simulador.diag_aux(cod, descri)VALUES ('$val1', '$val2');";
                sql($query_insert) or die($db->ErrorMsg()); 
              }
          }          
          $res_query->MoveNext();
      }
      $query_insert_fin="INSERT INTO simulador.diag (descri) select distinct trim(descri) from simulador.diag_aux";
      sql($query_insert_fin) or die($db->ErrorMsg());

      $query_truncar1="TRUNCATE TABLE simulador.amb;";
      sql($query_truncar1) or die($db->ErrorMsg());
      $query_truncar2="TRUNCATE TABLE simulador.amb_aux;";
      sql($query_truncar2) or die($db->ErrorMsg());
      $query="select cod_ambito from simulador.pss where trim(cod_ambito) !='';";
      $res_query = sql($query) or die($db->ErrorMsg());
      while (!$res_query->EOF) {          
          $arr_ret= explode ("|",trim($res_query->fields['cod_ambito']));
          $arr_ret=array_filter($arr_ret, "strlen");          
          //saco el numero de elementos
          $longitud = count($arr_ret);           
          //Recorro todos los elementos
          for($i=0; $i<$longitud; $i++){
              if ($i%2){
                $val1=$arr_ret[$i-1];
                $val2=$arr_ret[$i];
                $query_insert="INSERT INTO simulador.amb_aux(cod, descri)VALUES ('$val1', '$val2');";
                sql($query_insert) or die($db->ErrorMsg()); 
              }
          }          
          $res_query->MoveNext();
      }
      $query_insert_fin="INSERT INTO simulador.amb (descri) select distinct trim(descri) from simulador.amb_aux";
      sql($query_insert_fin) or die($db->ErrorMsg());

      echo $ret;
    break; 

    case 'paciente_form_submit':
      $ret = '';
      $id_pss = intval($_POST["id_pss"]);
      $cod = $db->Quote($_POST["cod"]);      
      $prestacion = $db->Quote($_POST["prestacion"]);      
      $procedimiento = $db->Quote($_POST["procedimiento"]);      
      $cod_procedimiento = $db->Quote($_POST["cod_procedimiento"]);      
      $especialidad = $db->Quote($_POST["especialidad"]);      
      $cod_especialidad = $db->Quote($_POST["cod_especialidad"]);      
      $ambito = $db->Quote($_POST["ambito"]);      
      $cod_ambito = $db->Quote($_POST["cod_ambito"]);      
      $diagnostico = $db->Quote($_POST["diagnostico"]);      
      $cod_diagnostico = $db->Quote($_POST["cod_diagnostico"]);      
      $sexo = $db->Quote(trim($_POST["sexo"]));      
      $edad = $db->Quote($_POST["edad"]);      
      $grupo_etareo = $db->Quote(trim($_POST["grupo_etareo"]));      

      if ($id_pss > 0) {
        // Modo Edicion      
          $query="UPDATE simulador.pss p SET 
                    cod = ".$cod.",                  
                    prestacion = ".$prestacion.",                  
                    procedimiento = ".$procedimiento.",                  
                    cod_procedimiento = ".$cod_procedimiento.",                  
                    especialidad = ".$especialidad.",                  
                    cod_especialidad = ".$cod_especialidad.",                  
                    ambito = ".$ambito.",                  
                    cod_ambito = ".$cod_ambito.",                  
                    diagnostico = ".$diagnostico.",                  
                    cod_diagnostico = ".$cod_diagnostico.",                  
                    sexo = ".$sexo.",                  
                    edad = ".$edad.",
                    grupo_etareo = ".$grupo_etareo."
                  WHERE              
                    id_pss = $id_pss";
          $res = sql($query) or die($db->ErrorMsg());

          $query_log="INSERT INTO 
                        simulador.log_pss
                      (id_pss, accion, fecha, usuario)
                      VALUES 
                      ($id_pss, 'Modifica PSS', current_timestamp, {$usuario->id});"; 
          $res_query_log = sql($query_log) or die($db->ErrorMsg());

          if ($res) {
            $ret .= 'OK';
          }
          else {
            $ret .= 'Ocurri&oacute; un error al actualizar los datos del PSS!';
          }
      }
      else {
        // Modo Agregar
       $query="INSERT INTO simulador.pss (
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
                ) VALUES ( 
                  ".$cod.",
                  ".$prestacion.",
                  ".$procedimiento.",
                  ".$cod_procedimiento.",
                  ".$especialidad.",
                  ".$cod_especialidad.",
                  ".$ambito.",
                  ".$cod_ambito.",
                  ".$diagnostico.",
                  ".$cod_diagnostico.",
                  ".$sexo.",
                  ".$edad.",
                  ".$grupo_etareo."
                ) RETURNING id_pss
        ";
        $res = sql($query) or die($db->ErrorMsg());

        $id_pss=$res->fields['id_pss'];
        $query_log="INSERT INTO 
                      simulador.log_pss
                    (id_pss, accion, fecha, usuario)
                    VALUES 
                    ($id_pss, 'Alta PSS', current_timestamp, {$usuario->id});"; 
        $res_query_log = sql($query_log) or die($db->ErrorMsg());

        if ($res) {
          $ret .= 'OK';
        }
        else {
          $ret .= 'Ocurri&oacute; un error al agregar los datos del PSS!';
        }
      }
      echo $ret;
      break;

    case 'busca_pss':
      $proc  = $_POST["proc"];
      $esp  = $_POST["esp"];
      $amb  = $_POST["amb"];
      $diag  = $_POST["diag"];
      $sexo  = trim($_POST["sexo"]);
      $grupo_etareo  = trim($_POST["grupo_etareo"]);
            
      $res_proc = implode(",",$proc);
      $res_esp = implode(",",$esp);
      $res_amb = implode(",",$amb);
      $res_diag = implode(",",$diag);

      $sql="SELECT descri FROM simulador.proc WHERE id_prod IN ($res_proc)";
      $res_proc_q=sql($sql) or die($db->ErrorMsg());
      $sql="SELECT descri FROM simulador.esp WHERE id_esp IN ($res_esp)";
      $res_esp_q=sql($sql) or die($db->ErrorMsg());
      $sql="SELECT descri FROM simulador.amb WHERE id_amb IN ($res_amb)";
      $res_amb_q=sql($sql) or die($db->ErrorMsg());
      $sql="SELECT descri FROM simulador.diag WHERE id_diag IN ($res_diag)";
      $res_diag_q=sql($sql) or die($db->ErrorMsg());

      $ret_proc='';
      while (!$res_proc_q->EOF) {          
          $ret_proc .= '%'.limpiarString(strtoupper(trim($res_proc_q->fields['descri']))).'%';                     
          $res_proc_q->MoveNext();
      }
      $ret_esp='';
      while (!$res_esp_q->EOF) {          
          $ret_esp .= '%'.limpiarString(strtoupper(trim($res_esp_q->fields['descri']))).'%';                     
          $res_esp_q->MoveNext();
      }
      $ret_amb='';
      while (!$res_amb_q->EOF) {          
          $ret_amb .= '%'.limpiarString(strtoupper(trim($res_amb_q->fields['descri']))).'%';                     
          $res_amb_q->MoveNext();
      }
      $ret_diag='';
      while (!$res_diag_q->EOF) {          
          $ret_diag .= '%'.limpiarString(strtoupper(trim($res_diag_q->fields['descri']))).'%';                     
          $res_diag_q->MoveNext();
      }

      $sql_final="SELECT
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
                  FROM 
                    simulador.pss
                  WHERE
                  (regexp_replace(upper(trim(cod_procedimiento)),'[^a-zA-Z0-9]', '', 'g') LIKE '".$ret_proc."') AND 
                  (regexp_replace(upper(trim(cod_especialidad)),'[^a-zA-Z0-9]', '', 'g') LIKE '".$ret_esp."') AND
                  (regexp_replace(upper(trim(cod_ambito)),'[^a-zA-Z0-9]', '', 'g') LIKE '".$ret_amb."') AND
                  (regexp_replace(upper(trim(cod_diagnostico)),'[^a-zA-Z0-9]', '', 'g') LIKE '".$ret_diag."') AND
                  (trim(grupo_etareo) LIKE '%".$grupo_etareo."%') AND
                  (CASE WHEN sexo='M' THEN 'M'
                        WHEN sexo='F' THEN 'F'
                        ELSE 'F M'
                   END LIKE '%".$sexo."%') 
                  ORDER BY
                    id_pss";
      $res_final=sql($sql_final) or die($db->ErrorMsg());

      $ret = '';
      if ($res_final->recordCount() > 0) {
        $ret .= '<table class="table table-condensed table-hover">
                      <thead>
                        <tr>     
                          <th class="small">ID</th>
                          <th class="small">Codigo</th>
                          <th class="small">Prestacion</th>
                          <th class="small">C Procedimiento</th>
                          <th class="small">C Especialidad</th>
                          <th class="small">C Ambito</th>
                          <th class="small">C Diagnostico</th>
                          <th class="small">Grupo</th>
                          <th class="small">Sexo</th>
                        </tr>
                      </thead>
                      <tbody >';
        while (!$res_final->EOF) {

          switch (trim($res_final->fields["grupo_etareo"])) {
            case 1:
              $grupo_etareo = "Emb Par Puer";
              break;
            case 2:
              $grupo_etareo = "0 a 5";
              break;
            case 3:
              $grupo_etareo = "6 a 9";
              break;
            case 4:
              $grupo_etareo = "Adolescente";
              break;
            case 5:
              $grupo_etareo = "Adulto";
              break;
            default:
              $grupo_etareo = "Sin Dato";
          }
          if (trim($res_final->fields["sexo"])=='') $sexo = 'Ambos';
          if (trim($res_final->fields["sexo"])=='M') $sexo = 'M';
          if (trim($res_final->fields["sexo"])=='F') $sexo = 'F';
          $cod=trim($res_final->fields['cod']); 
          $id_pss=trim($res_final->fields['id_pss']);
          $ret .= '<tr>';
          $ret .= '<td>'.$id_pss.'</td>';
          $ret .= '<td>'.$cod.'</td>';
          $ret .= '<td>'.trim($res_final->fields['prestacion']).'</td>';
          $ret .= '<td>'.trim($res_final->fields['cod_procedimiento']).'</td>';
          $ret .= '<td>'.trim($res_final->fields['cod_especialidad']).'</td>';
          $ret .= '<td>'.trim($res_final->fields['cod_ambito']).'</td>';
          $ret .= '<td>'.trim($res_final->fields['cod_diagnostico']).'</td>';
          $ret .= '<td>'.$grupo_etareo.'</td>';
          $ret .= '<td>'.$sexo.'</td>';          
          $ret .= '</tr>';

          $query = "SELECT a.descri FROM simulador.datos_reportables a WHERE a.id_pss = $id_pss ORDER BY a.descri ASC";
          $res = sql($query, "al traer los datos de la Medicaci&oacute;n de Uso Continuo") or fin_pagina();

          $datos_reportables='';
          if ($res->recordCount() > 0) {
              while (!$res->EOF) {
                $datos_reportables .= $res->fields["descri"]. ' - ';
                $res->MoveNext();
              }
          } else {
            $datos_reportables .= 'No hay datos';
          }

          $ret .= '<tr><td colspan="12"><div class="col-lg-6 col-md-3 col-xs-12">
            <div class="mt-element-ribbon bg-grey-steel">
              <div class="ribbon ribbon-color-info uppercase">Datos Reportables</div>
              <p class="ribbon-content">'.$datos_reportables.'</p>
            </div>
            </div></td></tr>';
          $res_final->MoveNext();        
        }
        $ret .= '</tbody>
              </table>';
      }
      else{
        $ret .= '<ul class="list-group"><li class="list-group-item alert alert-info" role="alert">No hay Registros</li></ul>';
      }
      echo $ret;
      break;

      case 'reportables_agregar_form':
        $id_pss = intval($_POST["id_pss"]);
        $ret = '<form id="reportables_agregar_form" method="POST" onsubmit="return reportables_form_submit(this);">';
        $ret .= '<input type="hidden" name="id_pss" id="id_pss" value="'.$id_pss.'" />';
        $ret .= '<div class="form-group">';
        $ret .= '<label for="reportables_form_descripcion" class="control-label">Descripci&oacute;n</label>';
        $ret .= '<input type="text" class="form-control" name="descri_reportables" id="descri_reportables" />';
        $ret .= '</div>';
        $ret .= '</form>';
        echo $ret;
      break;

      case 'comentarios_agregar_form':
        $id_pss = intval($_POST["id_pss"]);
        $ret = '<form id="reportables_agregar_form" method="POST" onsubmit="return comentarios_form_submit(this);">';
        $ret .= '<input type="hidden" name="id_pss" id="id_pss" value="'.$id_pss.'" />';
        $ret .= '<div class="form-group">';
        $ret .= '<label for="reportables_form_descripcion" class="control-label">Descripci&oacute;n</label>';
        $ret .= '<input type="text" class="form-control" name="descri_comentarios" id="descri_comentarios" />';
        $ret .= '</div>';
        $ret .= '</form>';
        echo $ret;
      break;

    case 'reportables_agregar':
      $id_pss  = intval($_POST["id_pss"]);
      $descri_reportables  = $_POST["descri_reportables"];
      $ret = "";
      if ($id_pss > 0 && !empty($descri_reportables)) {
        $query = "INSERT INTO simulador.datos_reportables (id_pss, descri) VALUES (";
        $query .= $id_pss.", ".$db->Quote($descri_reportables).")";
        $res = sql($query);
        if ($res === false) {
          $ret .= '<ul class="list-group"><li class="list-group-item alert alert-warning" role="alert">Ocurri&oacute; un error al guardar los datos de la medicaci&oacute;n</li></ul>';
        }
      }
      if ($id_pss > 0) {
        $ret .= datos_reportables($id_pss);
      }
      echo $ret;
    break;

    case 'comentarios_agregar':
      $id_pss  = intval($_POST["id_pss"]);
      $descri_comentarios  = $_POST["descri_comentarios"];
      $ret = "";
      if ($id_pss > 0 && !empty($descri_comentarios)) {
        $query = "INSERT INTO simulador.comentarios (id_pss, descri) VALUES (";
        $query .= $id_pss.", ".$db->Quote($descri_comentarios).")";
        $res = sql($query);
        if ($res === false) {
          $ret .= '<ul class="list-group"><li class="list-group-item alert alert-warning" role="alert">Ocurri&oacute; un error al guardar los datos de la medicaci&oacute;n</li></ul>';
        }
      }
      if ($id_pss > 0) {
        $ret .= comentarios($id_pss);
      }
      echo $ret;
    break;

    case 'busca_snomed_res':
      $proc  = trim($_POST["proc"]);
      $esp   = trim($_POST["esp"]);
      $amb   = trim($_POST["amb"]);
      $diag  = trim($_POST["diag"]);
      $sexo  = trim($_POST["sexo"]);
      $grupo_etareo  = trim($_POST["grupo_etareo"]);
          
      $sql="select * from snomed.ansestros_OUT($proc)";
      $res_proc_q=sql($sql) or die($db->ErrorMsg());
      $sql="select * from snomed.ansestros_OUT($esp)";
      $res_esp_q=sql($sql) or die($db->ErrorMsg());
      $sql="select * from snomed.ansestros_OUT($amb)";
      $res_amb_q=sql($sql) or die($db->ErrorMsg());
      $sql="select * from snomed.ansestros_OUT($diag)";
      $res_diag_q=sql($sql) or die($db->ErrorMsg());

      $ret_proc='';
      $i=0;
      $i_fin=$res_proc_q->recordCount();
      while (!$res_proc_q->EOF) {          
          $ret_proc .= "trim(regexp_replace(cod_procedimiento,'[^0-9]','','g')) LIKE '%".$res_proc_q->fields['conceptid']."%'";          
          $res_proc_q->MoveNext();
          $i++;
          if ($i<=$i_fin-1) $ret_proc .= " or ";
      }
      $ret_esp='';
      $i=0;
      $i_fin=$res_esp_q->recordCount();
      while (!$res_esp_q->EOF) {          
          $ret_esp .= "trim(regexp_replace(cod_especialidad,'[^0-9]','','g')) LIKE '%".$res_esp_q->fields['conceptid']."%'";                     
          $res_esp_q->MoveNext();
          $i++;
          if ($i<=$i_fin-1) $ret_esp .= " or ";
      }
      $ret_amb='';
      $i=0;
      $i_fin=$res_amb_q->recordCount();
      while (!$res_amb_q->EOF) {          
          $ret_amb .= "trim(regexp_replace(cod_ambito,'[^0-9]','','g')) LIKE '%".$res_amb_q->fields['conceptid']."%'";                     
          $res_amb_q->MoveNext();
          $i++;
          if ($i<=$i_fin-1) $ret_amb .= " or ";
      }
      $ret_diag='';
      $i=0;
      $i_fin=$res_diag_q->recordCount();
      while (!$res_diag_q->EOF) {          
          $ret_diag .= "trim(regexp_replace(cod_diagnostico,'[^0-9]','','g')) LIKE '%".$res_diag_q->fields['conceptid']."%'";                     
          $res_diag_q->MoveNext();
          $i++;
          if ($i<=$i_fin-1) $ret_diag .= " or ";
      }

      $sql_final="SELECT
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
                  FROM 
                    simulador.pss
                  WHERE
                  ($ret_proc) AND 
                  ($ret_esp) AND 
                  ($ret_amb) AND 
                  ($ret_diag) AND
                  (trim(grupo_etareo) LIKE '%".$grupo_etareo."%') AND
                  (CASE WHEN sexo='M' THEN 'M'
                        WHEN sexo='F' THEN 'F'
                        ELSE 'F M'
                   END LIKE '%".$sexo."%') 
                  ORDER BY
                    id_pss";
      //echo $sql_final;
      $res_final=sql($sql_final) or die($db->ErrorMsg());

      $ret = '';
      if ($res_final->recordCount() > 0) {
        $ret .= '<table class="table table-condensed table-hover">
                      <thead>
                        <tr>     
                          <th class="small">ID</th>
                          <th class="small">Codigo</th>
                          <th class="small">Prestacion</th>
                          <th class="small">C Procedimiento</th>
                          <th class="small">C Especialidad</th>
                          <th class="small">C Ambito</th>
                          <th class="small">C Diagnostico</th>
                          <th class="small">Grupo</th>
                          <th class="small">Sexo</th>
                        </tr>
                      </thead>
                      <tbody >';
        while (!$res_final->EOF) {

          switch (trim($res_final->fields["grupo_etareo"])) {
            case 1:
              $grupo_etareo = "Emb Par Puer";
              break;
            case 2:
              $grupo_etareo = "0 a 5";
              break;
            case 3:
              $grupo_etareo = "6 a 9";
              break;
            case 4:
              $grupo_etareo = "Adolescente";
              break;
            case 5:
              $grupo_etareo = "Adulto";
              break;
            default:
              $grupo_etareo = "Sin Dato";
          }
          if (trim($res_final->fields["sexo"])=='') $sexo = 'Ambos';
          if (trim($res_final->fields["sexo"])=='M') $sexo = 'M';
          if (trim($res_final->fields["sexo"])=='F') $sexo = 'F';
          $cod=trim($res_final->fields['cod']); 
          $id_pss=trim($res_final->fields['id_pss']);
          $ret .= '<tr>';
          $ret .= '<td>'.$id_pss.'</td>';
          $ret .= '<td>'.$cod.'</td>';
          $ret .= '<td>'.trim($res_final->fields['prestacion']).'</td>';
          $ret .= '<td>'.trim($res_final->fields['cod_procedimiento']).'</td>';
          $ret .= '<td>'.trim($res_final->fields['cod_especialidad']).'</td>';
          $ret .= '<td>'.trim($res_final->fields['cod_ambito']).'</td>';
          $ret .= '<td>'.trim($res_final->fields['cod_diagnostico']).'</td>';
          $ret .= '<td>'.$grupo_etareo.'</td>';
          $ret .= '<td>'.$sexo.'</td>';          
          $ret .= '</tr>';

          $query = "SELECT a.descri FROM simulador.datos_reportables a WHERE a.id_pss = $id_pss ORDER BY a.descri ASC";
          $res = sql($query, "al traer los datos de la Medicaci&oacute;n de Uso Continuo") or fin_pagina();

          $datos_reportables='';
          if ($res->recordCount() > 0) {
              while (!$res->EOF) {
                $datos_reportables .= $res->fields["descri"]. ' - ';
                $res->MoveNext();
              }
          } else {
            $datos_reportables .= 'No hay datos';
          }

          $ret .= '<tr><td colspan="12"><div class="col-lg-6 col-md-3 col-xs-12">
            <div class="mt-element-ribbon bg-grey-steel">
              <div class="ribbon ribbon-color-info uppercase">Datos Reportables</div>
              <p class="ribbon-content">'.$datos_reportables.'</p>
            </div>
            </div></td></tr>';
          $res_final->MoveNext();        
        }
        $ret .= '</tbody>
              </table>';
      }
      else{
        $ret .= '<ul class="list-group"><li class="list-group-item alert alert-info" role="alert">No hay Registros</li></ul>';
      }
      echo $ret;
      break;

	  case 'firmar_snomed_res':
      $nrodoc= trim($_POST["nrodoc"]);
      $nombre= trim($_POST["nombre"]);
      $apellido = trim($_POST["apellido"]);
      $proc  = trim($_POST["proc"]);
      $esp   = trim($_POST["esp"]);
      $amb   = trim($_POST["amb"]);
      $diag  = trim($_POST["diag"]);
      $sexo  = trim($_POST["sexo"]);
      $grupo_etareo  = trim($_POST["grupo_etareo"]);  
      $obs = trim($_POST["obs"]);
      
      //$firmahash = hash('md5',$proc.$esp.$amb.$diag.$sexo.$grupo_etareo.$firma);  //(p.ej., "md5", "sha256", "haval160,4", etc..) md5(string); sha1(string);
      //$firmahash = base64_encode($nrodoc.$nombre.$apellido.$proc.$esp.$amb.$diag.$sexo.$grupo_etareo.$obs); 
      $firmahash = base64_encode($nrodoc.$nombre.$apellido.$diag.$sexo.$obs); 
      
      $data_array=null; // array
      $atoken = base64_encode("$http_token_key:$http_secret");
      
      $headers = null; // array

      $make_call = callAPI_token('POST', $html_firma.$html_RA.'oauth/token?grant_type=client_credentials', json_encode($data_array),array($headers),$atoken);

      $response = json_decode($make_call);
     
      if (!$response->error) {
          $access_token   = $response->access_token;
          $token_type     = $response->token_type;
          $expires_in     = $response->expires_in;
          $scope          = $response->scope;
          $callback_url   = $response->callback_url;
          $jti            = $response->jti;
      } else {
          $timestamp      = $response->timestamp;
          $status         = $response->status;
          $error          = $response->error;
          $message        = $response->message;
          $path           = $response->path;
          $error_description  = $response->error_description;
          $ret .= '<ul class="list-group"><li class="list-group-item alert alert-danger" role="alert">Error! Acceso no autorizado.<br>'.var_dump($response).'</li></ul>';
          echo $ret;
          break;
      }

      //$cuil = "xx-xxxxxxxx-x"; // El login de usuario deberia estar ligado a un cuil
      //$cuil = $http_cuil_firmador; // en db.php version anterior
      $cuil = $usuario->cuil;

      $headers = array(
        //"Authorization"  => "Bearer ".$access_token   
        //'Content-Type: application/json',
        //'Authorization', 'OAuth '.$access_token,
      );
      
      $location = 'Pendiente';
      $paciente = "$nrodoc - $nombre $apellido";

      $query="INSERT INTO 
                    firma.firmas 
                      (id_usuario, fecha_firma, documento, documento_enviado, metadata, status, location, nrodoc, nombres, apellido, diagnostico, sexo, evolucion)
                    VALUES (
                      ".$usuario->id.",
                      current_timestamp,
                      '".$firmahash."',
                      '".$firmahash."',
                      '{\"Firma\": \"Pendiente\"}',
                      'Pendiente de Envio',
                      '".$location."',
					  '".$nrodoc."',
					  '".$nombre."',
					  '".$apellido."',
					  '".$diag."',
					  '".$sexo."',
					  '".$obs."'
                      ) RETURNING id_firma
                    ;";
      
      $res = sql($query) or die($db->ErrorMsg());

      $id_firma=$res->fields['id_firma'];
      
      $query_log="INSERT INTO 
                    simulador.log_pss
                  (id_pss, accion, fecha, usuario)
                  VALUES 
                  ($id_firma, 'Pendiente de Envio de Firma', current_timestamp, {$usuario->id});"; 

      $res_query_log = sql($query_log) or die($db->ErrorMsg());

      $data_array =  array(
        "cuil"  => $cuil,
        "documento" => $firmahash,
        "metadata" => array(
          "Firma"       => "$id_firma"
        ),
        "type" => "HASH",
        "urlRedirect" => "$http_urlRedirect"."$id_firma"
      );

      $make_call_enviar = callAPI_enviar('POST', $html_firma.$html_firmador.$html_api, json_encode($data_array),json_encode($headers),$access_token);
     
        if (preg_match('~Location: (.*)~i', $make_call_enviar, $match)) {
          $location = trim($match[1]);
                    
          $query="UPDATE firma.firmas SET 
                    metadata = '{\"Firma\": ".$id_firma."}',
                    status = 'Enviado',
                    location = '".$location."'
                  WHERE              
                    id_firma = $id_firma;";

          $res = sql($query) or die($db->ErrorMsg());

          //$id_firma=$res->fields['id_firma'];
          $query_log="INSERT INTO 
                        simulador.log_pss
                      (id_pss, accion, fecha, usuario)
                      VALUES 
                      ($id_firma, 'Envio de Firma(Esperando Resultado)', current_timestamp, {$usuario->id});"; 

          $res_query_log = sql($query_log) or die($db->ErrorMsg());

          if ($res) {
            $ret .= 'OK';
          }
          else {
            $ret .= 'Ocurri&oacute; un error al agregar los datos de la Firma!';
            echo $ret;
            break;
          }

          // Redirect a la pagina del resultado
          echo '<script type="text/javascript">window.location = "'.$location.'"</script>';
        
        } else {
          $ret .= '<ul class="list-group"><li class="list-group-item alert alert-danger" role="alert">Error! Error en la Firma.<br>'.var_dump($make_call_enviar).'</li></ul>';
          echo $ret;
        }

    break;

    case 'listado_firmas':
      $res_json = array();

      $query = "SELECT 
                  f.id_firma AS id,
                  f.documento,
                  f.documento_enviado,
                  f.metadata, 
                  f.status, 
                  f.id_usuario, 
                  f.fecha_firma as fecha, 
                  f.location,
                  f.msg,
                  f.nrodoc,
                  f.nombres,
                  f.apellido,
                  f.sexo,
                  f.diagnostico,
                  f.evolucion
                FROM 
                  firma.firmas f
                WHERE
                  f.id_usuario = ".$usuario->id.";
              ";
           
      $res = sql($query) or die($db->ErrorMsg());

      while (!$res->EOF) {
        $res_json[] = array(
          "id"       => $res->fields["id"],
          "fecha" => $res->fields["fecha"],
          "paciente"   => $res->fields["nrodoc"].' '.$res->fields["nombres"].' '.$res->fields["apellido"],
          "api"     => str_replace($html_firma.$html_firmador.$html_api.'/', "", $res->fields["location"]),
          "status"    => str_replace("{\"success\":true}","Firmado",$res->fields["status"])
        );
        $res->MoveNext();
      }
      echo json_encode(array("data" =>$res_json));
    break;

    case 'ver_firma':
      $id_firma = intval($_POST["id"]);
  
      if ($id_firma > 0) {
        
        $query = "SELECT 
            f.id_firma AS id,
            f.documento,
            f.documento_enviado,
            f.metadata, 
            f.status, 
            f.id_usuario, 
            f.fecha_firma as fecha, 
            f.location,
            f.msg,
            f.nrodoc,
            f.nombres,
            f.apellido,
            f.sexo,
            f.diagnostico,
            f.evolucion
          FROM 
            firma.firmas f
          WHERE
            f.id_firma = ".$id_firma.";
        ";

        $res = sql($query) or die($db->ErrorMsg());
        if ($res !== false && $res->recordCount() == 1) {
          $form_id = $res->fields["id_firma"];
          //$form_documento = base64_decode($res->fields["documento"]);
          $form_documento = $res->fields["documento"];
          //$form_documento_enviado = base64_decode($res->fields["documento_enviado"]);
          $form_documento_enviado = $res->fields["documento_enviado"];
          $form_metadata = $res->fields["metadata"];
          $form_status = str_replace("{\"success\":true}","Firmado",$res->fields["status"]);
          $form_id_usuario = $res->fields["id_usuario"];
          $form_paciente = $res->fields["nrodoc"].' '.$res->fields["nombres"].' '.$res->fields["apellido"];
          $form_fecha = $res->fields["fecha"];
          $form_location = $res->fields["location"];
          $form_msg = $res->fields["msg"];
          $form_nrodoc = $res->fields["nrodoc"];
          $form_nombres = $res->fields["nombres"];
          $form_apellido = $res->fields["apellido"];
          $form_sexo = $res->fields["sexo"];
          $form_diagnostico = $res->fields["diagnostico"];
          $form_evolucion = $res->fields["evolucion"];
			  
		  echo '<form id="firma_form" method="POST" onsubmit="return firma_form_submit(this);">';
		  include_once("firma_form.php");
		  echo '</form>';         
          
        }
        else {
          echo '<ul class="list-group"><li class="list-group-item alert alert-warning" role="alert">Firma incorrecta</li></ul>';
        }
      }
      else {
        echo '<ul class="list-group"><li class="list-group-item alert alert-warning" role="alert">Par&aacute;metros incorrectos</li></ul>';
      }
      break;
	  
	case 'ver_firma_callback':
      $id_firma = intval($_POST["id"]);
      
      if ($id_firma > 0) {
        
        $query = "SELECT 
            f.id_firma AS id,
            f.documento,
            f.documento_enviado,
            f.metadata, 
            f.status, 
            f.id_usuario, 
            f.fecha_firma as fecha, 
            f.location,
            f.msg,
            f.nrodoc,
            f.nombres,
            f.apellido,
            f.sexo,
            f.diagnostico,
            f.evolucion
          FROM 
            firma.firmas f
          WHERE
            f.id_firma = ".$id_firma.";
        ";

        $res = sql($query) or die($db->ErrorMsg());
        if ($res !== false && $res->recordCount() == 1) {
          $form_id = $res->fields["id_firma"];
          //$form_documento = base64_decode($res->fields["documento"]);
          $form_documento = $res->fields["documento"];
          //$form_documento_enviado = base64_decode($res->fields["documento_enviado"]);
          $form_documento_enviado = $res->fields["documento_enviado"];
          $form_metadata = $res->fields["metadata"];
          $form_status = str_replace("{\"success\":true}","Firmado",$res->fields["status"]);
          $form_id_usuario = $res->fields["id_usuario"];
          $form_paciente = $res->fields["nrodoc"].' '.$res->fields["nombres"].' '.$res->fields["apellido"];
          $form_fecha = $res->fields["fecha"];
          $form_location = $res->fields["location"];
          $form_msg = $res->fields["msg"];
          $form_nrodoc = $res->fields["nrodoc"];
          $form_nombres = $res->fields["nombres"];
          $form_apellido = $res->fields["apellido"];
          $form_sexo = $res->fields["sexo"];
          $form_diagnostico = $res->fields["diagnostico"];
          $form_evolucion = $res->fields["evolucion"];
          
			echo '<form id="modal_form" method="POST" onsubmit="return modal_form_submit(this);">';
			include_once("firma_form.php");
			echo '</form>';
       
        }
        else {
          echo '<ul class="list-group"><li class="list-group-item alert alert-warning" role="alert">Firma incorrecta</li></ul>';
        }
      }
      else {
        echo '<ul class="list-group"><li class="list-group-item alert alert-warning" role="alert">Par&aacute;metros incorrectos</li></ul>';
      }
      break;
	  
    default:
      echo 'Comando no v&aacute;lido';
      break;
  }
}
?>