<?php 

function callAPI_token($method, $url, $data, $headers, $atoken){

  $curl = curl_init();

  switch ($method){
     case "POST":
        curl_setopt($curl, CURLOPT_POST, 1);
        if ($data)
           curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        break;
     case "PUT":
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
        if ($data)
           curl_setopt($curl, CURLOPT_POSTFIELDS, $data);			 					
        break;
     default:
        if ($data)
           $url = sprintf("%s?%s", $url, http_build_query($data));
  }

  // OPTIONS:
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
      'Accept: application/json',
      'Authorization: Basic '.$atoken, // token:secret base64
      //$headers,
  ));

  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
  //support https
  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);  
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); 
  //debug
  curl_setopt($curl, CURLOPT_VERBOSE, true);  
  //curl_setopt($curl, CURLOPT_STDERR, fopen('php://stderr', 'w'));

  // EXECUTE:
  $result = curl_exec($curl);
  
  // Comprobar el código de estado HTTP
  if (!curl_errno($curl)) {
    switch ($http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE)) {
      case 200:  # OK
        // No es error pero muestro informacion para debug
        /*echo 'Código HTTP : ', $http_code, "\n";
        $info = curl_getinfo($curl);
        echo 'Se tardó ', $info['total_time'], ' segundos en enviar una petición a ', $info['url'], "<br>\n\n";
        print_r($info);
        echo '<br>';
        printf("cUrl error (#%d): %s<br>\n", curl_errno($curl),
        htmlspecialchars(curl_error($curl)));*/
        break;
      default:
        //echo 'Código: ', $http_code, "\n<br>";
        $info = curl_getinfo($curl);
        //echo 'Se tardó ', $info['total_time'], ' segundos en enviar una petición a ', $info['url'], "\n";
        /*printf("<br>cUrl error (#%d): %s<br>\n", curl_errno($curl),
        htmlspecialchars(curl_error($curl)));*/
        //var_dump($result);
    }
  }
  
  if(!$result){die("Error en el token.");}
  curl_close($curl);
  return $result;
}

function callAPI_enviar($method, $url, $data, $headers, $atoken){

  $curl = curl_init();

  switch ($method){
     case "POST":
        curl_setopt($curl, CURLOPT_POST, 1);
        if ($data)
           curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        break;
     case "PUT":
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
        if ($data)
           curl_setopt($curl, CURLOPT_POSTFIELDS, $data);			 					
        break;
     default:
        if ($data)
           $url = sprintf("%s?%s", $url, http_build_query($data));
  }

  // OPTIONS:
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_HEADER, 1);
  //curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization', 'OAuth '+$atoken));
  curl_setopt($curl, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json', 
      //'Accept: application/json',
      'Authorization: Bearer '.$atoken,
      $headers,
  ));

  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
  //support https
  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);  
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); 
  
  // si todo va bien redirigir a location
  curl_setopt($curl, CURLOPT_FOLLOWLOCATION,true);
  curl_setopt($curl, CURLOPT_UNRESTRICTED_AUTH, true);

  //debug
  curl_setopt($curl, CURLOPT_VERBOSE, true);  
  
  // EXECUTE:
  $result = curl_exec($curl);
 
  // Comprobar el código de estado HTTP
  if (!curl_errno($curl)) {
    switch ($http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE)) {
      case 200:  # OK
        // No es error pero muestro informacion
        break;
      default:
        
        $info = curl_getinfo($curl);
        
    }
  }
  
  if(!$result){die("Error! no pudimos realizar la firma.");}
  curl_close($curl);
  return $result;
}


function limpiarString($texto)
{
      $textoLimpio = preg_replace('([^A-Za-z0-9])', '', $texto);                
      return $textoLimpio;
}

function datos_reportables($id_pss) {
  $query = "SELECT 
              a.id,
              a.descri
            FROM
              simulador.datos_reportables a
            WHERE
              a.id_pss = $id_pss
            ORDER BY
              a.descri ASC
            ";
  $res = sql($query, "al traer los datos de la Medicaci&oacute;n de Uso Continuo") or fin_pagina();

  $ret = '';
  if ($res->recordCount() > 0) {
    if ($res->recordCount() > 5) {
      $ret .= '<ul class="list-group scroller" data-height="201" data-always-visible="1" data-rail-visible="1">';
    }
    else {
      $ret .= '<ul class="list-group">';
    }
    while (!$res->EOF) {
      $ret .= '<li class="list-group-item"><b>';
      $ret .= $res->fields["descri"];
      $ret .= '</b>';
      $ret .= '<a class="info tooltips pull-right" title="" href="javascript:elimina_report('.$res->fields["id"].');" data-original-title="Eliminar Dato"> <i class="fa fa-trash-o"></i> </a> ';
      $ret .= '</li>';
      $res->MoveNext();
    }
    $ret .= '</ul>';
  } else {
    $ret .= '<div class="alert alert-danger"><strong>No hay datos</strong></div>';
  }
  return $ret;
}

function comentarios($id_pss) {
  $query = "SELECT 
              a.id,
              a.descri
            FROM
              simulador.comentarios a
            WHERE
              a.id_pss = $id_pss
            ORDER BY
              a.descri ASC
            ";
  $res = sql($query, "al traer los datos de la Medicaci&oacute;n de Uso Continuo") or fin_pagina();

  $ret = '';
  if ($res->recordCount() > 0) {
    if ($res->recordCount() > 5) {
      $ret .= '<ul class="list-group scroller" data-height="201" data-always-visible="1" data-rail-visible="1">';
    }
    else {
      $ret .= '<ul class="list-group">';
    }
    while (!$res->EOF) {
      $ret .= '<li class="list-group-item"><b>';
      $ret .= $res->fields["descri"];
      $ret .= '</b>';
      $ret .= '<a class="info tooltips pull-right" title="" href="javascript:elimina_coment('.$res->fields["id"].');" data-original-title="Eliminar Dato"> <i class="fa fa-trash-o"></i> </a> ';
      $ret .= '</li>';
      $res->MoveNext();
    }
    $ret .= '</ul>';
  } else {
    $ret .= '<div class="alert alert-danger"><strong>No hay datos</strong></div>';
  }
  return $ret;
}

?>