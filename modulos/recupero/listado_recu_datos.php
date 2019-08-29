<?php
require_once(dirname(__FILE__)."/../../config.php");

if (isset($parametros["accion"])) {
  switch ($parametros["accion"]) {    
    
    case 'listado':
      $res_json = array();
      $query="SELECT id_nom_nac, descri, term
              FROM recupero.nom_nac
              ORDER BY id_nom_nac";
      $res = sql($query) or die($db->ErrorMsg());

      while (!$res->EOF) {   
        $res_json[] = array(
          "id"                => trim($res->fields["id_nom_nac"]),
          "descri"            => trim($res->fields["descri"]),
          "snomed"              => trim($res->fields["term"])
        );
        $res->MoveNext();
      }
      // echo json_encode(array("data" => $res_json, "query" => $query));
      echo json_encode(array("data" => $res_json));
    break;  

    case 'busca_snomed_res':
      $proc  = trim($_POST["proc"]);
                
      $sql="select * from snomed.ansestros_OUT($proc)";
      $res_proc_q=sql($sql) or die($db->ErrorMsg());
      
      $ret_proc='';
      $i=0;
      $i_fin=$res_proc_q->recordCount();
      while (!$res_proc_q->EOF) {          
          $ret_proc .= "trim(regexp_replace(term,'[^0-9]','','g')) LIKE '%".$res_proc_q->fields['conceptid']."%'";          
          $res_proc_q->MoveNext();
          $i++;
          if ($i<=$i_fin-1) $ret_proc .= " or ";
      }

      $sql_final="SELECT 
                    id_nom_nac, 
                    descri, 
                    term
                  FROM 
                    recupero.nom_nac
                  WHERE
                  ($ret_proc)                 
                  ORDER BY
                  id_nom_nac";
      //echo $sql_final; die();
      $res_final=sql($sql_final) or die($db->ErrorMsg());

      $ret = '';
      if ($res_final->recordCount() > 0) {
        $ret .= '<table class="table table-condensed table-hover">
                      <thead>
                        <tr>     
                          <th class="small">ID</th>
                          <th class="small">Descripcion</th>
                          <th class="small">SNOMED</th>                          
                        </tr>
                      </thead>
                      <tbody >';
        while (!$res_final->EOF) {
         
          $ret .= '<tr>';
          $ret .= '<td>'.trim($res_final->fields['id_nom_nac']).'</td>';
          $ret .= '<td>'.trim($res_final->fields['descri']).'</td>';
          $ret .= '<td>'.trim($res_final->fields['term']).'</td>';                  
          $ret .= '</tr>';          
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

    default:
      echo 'Comando no v&aacute;lido';
      break;
  }
}
?>