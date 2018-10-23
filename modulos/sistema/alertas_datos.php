<?php
require_once(dirname(__FILE__)."/../../config.php");

if (isset($parametros["accion"])) {
  switch ($parametros["accion"]) {
    case 'listado':
      $res_json = array();
      $query = "SELECT 
                  a.id,
                  a.fecha_alta,
                  a.mensaje,
                  l.fecha AS fecha_leido,
                  t.descripcion AS tipo_destino,
                  o.apellido AS origen_apellido,
                  o.nombre AS origen_nombre,
                  d.apellido AS destino_apellido,
                  d.nombre AS destino_nombre
                FROM
                  sistema.alertas a
                  LEFT OUTER JOIN sistema.alertas_lecturas l ON (a.id = l.id_alerta AND l.id_usuario = ".$usuario->id.")
                  LEFT OUTER JOIN sistema.usuarios_tipos t ON (a.id_tipo = t.id)
                  LEFT OUTER JOIN sistema.usuarios o ON (a.id_usuario_origen = o.id)
                  LEFT OUTER JOIN sistema.usuarios d ON (a.id_usuario_destino = d.id)
                WHERE
                  a.activo = 't' AND
                  (
                    (a.id_tipo IS NULL AND a.id_usuario_destino IS NULL) OR
                    a.id_usuario_destino = ".$usuario->id." OR a.id_tipo = ".$usuario->id_tipo."
                  )
      ";
      $res = sql($query) or die($db->ErrorMsg());

      while (!$res->EOF) {
        if (!empty($res->fields["destino_apellido"])) {
          $destino = $res->fields["destino_apellido"].', '.$res->fields["destino_nombre"];
        }
        elseif (!empty($res->fields["tipo_destino"])) {
          $destino = $res->fields["tipo_destino"];
        }
        else {
          $destino = 'Todos';
        }
        
        $fecha_leido = empty($res->fields["fecha_leido"]) ? '' : date("d/m/Y H:i:s", strtotime($res->fields["fecha_leido"]));

        $res_json[] = array(
          "id"          => $res->fields["id"],
          "fecha"       => date("d/m/Y H:i:s", strtotime($res->fields["fecha_alta"])),
          "fecha_leido" => $fecha_leido,
          "mensaje"     => nl2br($res->fields["mensaje"]),
          "remitente"   => $res->fields["origen_apellido"].', '.$res->fields["origen_nombre"],
          "destino"     => $destino
        );
        $res->MoveNext();
      }
      echo json_encode(array("data" =>$res_json));
      break;

    case 'alertas':
      $ret = '';
      $query = "SELECT 
                  a.id,
                  a.fecha_alta,
                  a.mensaje,
                  l.fecha AS fecha_leido,
                  t.descripcion AS tipo_destino,
                  o.apellido AS origen_apellido,
                  o.nombre AS origen_nombre,
                  d.apellido AS destino_apellido,
                  d.nombre AS destino_nombre
                FROM
                  sistema.alertas a
                  LEFT OUTER JOIN sistema.alertas_lecturas l ON (a.id = l.id_alerta AND l.id_usuario = ".$usuario->id.")
                  LEFT OUTER JOIN sistema.usuarios_tipos t ON (a.id_tipo = t.id)
                  LEFT OUTER JOIN sistema.usuarios o ON (a.id_usuario_origen = o.id)
                  LEFT OUTER JOIN sistema.usuarios d ON (a.id_usuario_destino = d.id)
                WHERE
                  a.activo = 't' AND
                  l.fecha IS NULL AND
                  (
                    (a.id_tipo IS NULL AND a.id_usuario_destino IS NULL) OR
                    a.id_usuario_destino = ".$usuario->id." OR a.id_tipo = ".$usuario->id_tipo."
                  )
      ";
      $res = sql($query) or die($db->ErrorMsg());
      if ($res->recordCount() > 0) {
        while (!$res->EOF) {
          $origen = $res->fields["origen_apellido"].', '.$res->fields["origen_nombre"];

          if (!empty($res->fields["tipo_destino"])) {
            $destino = $res->fields["tipo_destino"];
          }
          elseif (!empty($res->fields["destino_apellido"])) {
            $destino = $res->fields["destino_apellido"].', '.$res->fields["destino_nombre"];
          }
          else {
            $destino = 'Todos';
          }
          
          $url = encode_link($html_root."/sistema/alertas_datos", array("accion" => "alerta_detalles", "id_alerta" => $res->fields["id"]));
          $ret .= '<li><a href="#popup_modal" data-toggle="modal" data-url="'.$url.'" title="Detalles de la alerta">';
          $ret .= '<i class="fa fa-bell text-aqua"></i> ';
          $ret .= '<b>Fecha:</b> '.date("d/m/Y H:i:s", strtotime($res->fields["fecha_alta"])).'<br/>';
          $ret .= '<b>Remitente:</b> '.$origen.'<br/>';
          $ret .= '<b>Destino:</b> '.$destino.'<br/>';
          $ret .= '</a>';
          $ret .= '</li>';
          $res->MoveNext();
        }
      }
      echo $ret;
      break;

    case 'alerta_leida':
      $id_alerta = intval($_POST["id_alerta"]);
      $id_usuario = $usuario->id;
      $ret = '';
      if ($id_alerta > 0) {
        $query = "INSERT INTO sistema.alertas_lecturas (id_alerta, id_usuario) VALUES ($id_alerta, $id_usuario)";
        $res = sql($query) or die($db->ErrorMsg());
        if ($res !== false) {
          $ret .= "OK";
        }
        else {
          $ret .= '<ul class="list-group"><li class="list-group-item alert alert-warning" role="alert">Error al marcar como leida la alerta!</li></ul>';
        }
      }
      else {
        $ret .= '<ul class="list-group"><li class="list-group-item alert alert-warning" role="alert">Par&aacute;metros incorrectos</li></ul>';
      }
      echo $ret;
      break;

    case 'alerta_form':
      $form_tipos = array();
      $query = "SELECT 
                  t.id,
                  t.nombre,
                  t.descripcion
                FROM
                  sistema.usuarios_tipos t
                ORDER BY
                  t.id ASC
      ";
      $res = sql($query) or die($db->ErrorMsg());

      while (!$res->EOF) {
        $form_tipos[] = array(
          "id"          => $res->fields["id"],
          "nombre"      => $res->fields["nombre"],
          "descripcion" => $res->fields["descripcion"]
        );
        $res->MoveNext();
      }

      echo '<form id="alerta_form" method="POST" onsubmit="return alerta_form_submit(this);">';
      include_once("alertas_form.php");
      echo '</form>';
      break;

    case 'options_usuarios':
      $id_tipo = intval($_POST["id_tipo"]);
      if ($id_tipo > 0) {
        $query="SELECT 
          u.id, 
          u.apellido,
          u.nombre
        FROM 
          sistema.usuarios u
        WHERE
          u.activo = 't' AND
          u.id_tipo = $id_tipo
        ORDER BY
          u.apellido ASC,
          u.nombre ASC
        ";
        $res = sql($query) or die($db->ErrorMsg());

        if ($res->recordCount() > 0) {
          $ret .= '<option value="">Todos</option>';
          while (!$res->EOF) {
            $ret .= '<option value="'.$res->fields["id"].'"';
            $ret .= '>'.$res->fields["apellido"].', '.$res->fields["nombre"].'</option>';
            $res->MoveNext();
          }
        }
        else {
          $ret .= '<option value="">Todos</option>';
        }
      }
      else {
          $ret .= '<option value="">Todos</option>';
      }
      echo $ret;
      break;

    case 'agregar_alerta':
      $id_tipo  = intval($_POST["alerta_form_tipo"]);
      $id_usuario = intval($_POST["alerta_form_usuario"]);
      $mensaje = $db->Quote($_POST["alerta_form_mensaje"]);
      $ret = '';
      $error = '';
      if ($id_tipo == 0) {
        $id_tipo = 'NULL';
      }
      if ($id_usuario == 0) {
        $id_usuario = 'NULL';
      }
      if (empty($error) && empty($mensaje)) {
        $error = 'Debe ingresar el mensaje!';
      }
      if (empty($error)) {
        $query = "INSERT INTO sistema.alertas (
          id_usuario_origen,
          id_usuario_destino,
          id_tipo,
          mensaje
        ) VALUES (";
        $query .= $usuario->id.", ".
                  $id_usuario.", ".
                  $id_tipo.", ".
                  $mensaje.")";

        $res = sql($query) or die($db->ErrorMsg());
        if ($res === false) {
          $ret .= '<ul class="list-group"><li class="list-group-item alert alert-warning" role="alert">Ocurri&oacute; un error al guardar los datos de la alerta!</li></ul>';
        }
        else {
          $ret .= "OK";
        }
      }
      else {
        $ret .= '<ul class="list-group"><li class="list-group-item alert alert-warning" role="alert">ERROR: '.$error.'</li></ul>';
      }
      echo $ret;
      break;

    case 'alerta_detalles':
      $id_alerta  = intval($parametros["id_alerta"]);
      $ret = '';
      if ($id_alerta > 0) {
        $query = "SELECT 
                    a.fecha_alta,
                    a.mensaje,
                    t.descripcion AS tipo_destino,
                    o.apellido AS origen_apellido,
                    o.nombre AS origen_nombre,
                    d.apellido AS destino_apellido,
                    d.nombre AS destino_nombre
                  FROM
                    sistema.alertas a
                    LEFT OUTER JOIN sistema.usuarios_tipos t ON (a.id_tipo = t.id)
                    LEFT OUTER JOIN sistema.usuarios o ON (a.id_usuario_origen = o.id)
                    LEFT OUTER JOIN sistema.usuarios d ON (a.id_usuario_destino = d.id)
                  WHERE
                    a.activo = 't' AND 
                    a.id = $id_alerta
        ";
        $res = sql($query) or die($db->ErrorMsg());

        if ($res->recordCount() == 1) {
          $origen = $res->fields["origen_apellido"].', '.$res->fields["origen_nombre"];

          if (!empty($res->fields["destino_apellido"])) {
            $destino = $res->fields["destino_apellido"].', '.$res->fields["destino_nombre"];
          }
          elseif (!empty($res->fields["tipo_destino"])) {
            $destino = $res->fields["tipo_destino"];
          }
          else {
            $destino = 'Todos';
          }
          
          $fecha_leido = empty($res->fields["fecha_leido"]) ? '' : date("d/m/Y H:i:s", strtotime($res->fields["fecha_leido"]));

          $ret .= '<form id="alerta_leida_form" method="POST" onsubmit="return alerta_leida_form_submit(this);">';
          $ret .= '<input type="hidden" name="alerta_leida_form_url" id="alerta_leida_form_url" value="'.encode_link($html_root."/sistema/alertas_datos", array("accion" => "alerta_leida")).'">';
          $ret .= '<input type="hidden" name="alerta_leida_form_id" id="alerta_leida_form_id" value="'.$id_alerta.'">';
          $ret .= '<div class="row">';
          $ret .= '<div class="col-xs-12">';
          $ret .= '<b>Fecha:</b> '.date("d/m/Y H:i:s", strtotime($res->fields["fecha_alta"]));
          $ret .= '</div>';
          $ret .= '</div>';
          $ret .= '<br/>';
          $ret .= '<div class="row">';
          $ret .= '<div class="col-xs-12">';
          $ret .= '<b>Remitente:</b> '.$origen;
          $ret .= '</div>';
          $ret .= '</div>';
          $ret .= '<br/>';
          $ret .= '<div class="row">';
          $ret .= '<div class="col-xs-12">';
          $ret .= '<b>Destino:</b> '.$destino;
          $ret .= '</div>';
          $ret .= '</div>';
          $ret .= '<br/>';
          $ret .= '<div class="row">';
          $ret .= '<div class="col-xs-12">';
          $ret .= '<b>Mensaje:</b> '.nl2br($res->fields["mensaje"]);
          $ret .= '</div>';
          $ret .= '</div>';
          $ret .= '</form>';
        }
        else {
          $ret .= '<ul class="list-group"><li class="list-group-item alert alert-warning" role="alert">Error al cargar los datos de la alerta!</li></ul>';
        }
      }
      else {
        $ret .= '<ul class="list-group"><li class="list-group-item alert alert-warning" role="alert">Par&aacute;metros incorrectos</li></ul>';
      }
      echo $ret;
      break;
    default:
      echo '<div class="alert alert-warning text-center" role="alert"><span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span> Comando no v&aacute;lido</div>';
      break;
  }
}
?>