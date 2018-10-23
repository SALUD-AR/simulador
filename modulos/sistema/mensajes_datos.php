<?php
require_once(dirname(__FILE__)."/../../config.php");

function get_respuestas($id_mensaje) {
  global $db, $usuario;
  $ret = '';
  $query = "SELECT
              r.fecha,
              re.fecha_leido,
              r.respuesta
            FROM sistema.mensajes_respuestas r 
            LEFT OUTER JOIN sistema.mensajes m ON (r.id_mensaje = m.id)
            LEFT OUTER JOIN sistema.mensajes_respuestas_estados re ON (r.id = re.id_respuesta AND re.id_usuario = {$usuario->id})
            WHERE
              re.fecha_eliminado IS NULL AND
              r.id_mensaje = {$id_mensaje} AND 
              (m.id_tipo IS NOT NULL OR m.id_usuario_destino IS NOT NULL)";
  $res = sql($query) or die($db->ErrorMsg());
  $respuestas = array();
  $respuestas_nuevas = false;
  if ($res->recordCount() > 0) {
    while (!$res->EOF) {
      if (empty($res->fields["fecha_leido"])) {
        $icono = 'fa-envelope-o';
        $respuestas_nuevas = true;
      }
      else {
        $icono = 'fa-envelope-open-o';
      }
      $respuestas[] = array(
        "respuesta" => $res->fields["respuesta"],
        "fecha"     => date("d/m/Y H:i:s", strtotime($res->fields["fecha"])),
        "icono"     => $icono
      );
      $res->MoveNext();
    }

    if ($respuestas_nuevas) {
      $respuestas_nuevas_class = 'done';
      $respuestas_cant_class = 'green';
    }
    else {
      $respuestas_nuevas_class = '';
      $respuestas_cant_class = 'dark';
    }
    $ret .= '<div class="mt-element-list" id="lista-respuestas-mensajes">
              <div class="mt-list-container list-simple ext-1 group">
                <a class="list-toggle-container collapsed" data-toggle="collapse" href="#completed-simple" aria-expanded="false">
                  <div class="list-toggle '.$respuestas_nuevas_class.' uppercase"> Respuestas
                    <span class="badge badge-default pull-right bg-white font-'.$respuestas_cant_class.' bold">'.count($respuestas).'</span>
                  </div>
                </a>
                <div class="panel-collapse collapse" id="completed-simple" aria-expanded="false" style="height: 0px;">
                    <ul>';
    foreach ($respuestas as $respuesta) {
      if ($respuesta["icono"] == 'fa-envelope-o') {
        $respuestas_nuevas_class = 'done';
      }
      else {
        $respuestas_nuevas_class = '';
      }
      $ret .= '<li class="mt-list-item '.$respuestas_nuevas_class.'">
                  <div class="list-icon-container">
                    <i class="fa '.$respuesta["icono"].'" aria-hidden="true"></i>
                  </div>
                  <div class="list-datetime">'.$respuesta["fecha"].'</div>
                  <div class="list-item-content">
                      '.$respuesta["respuesta"].'
                  </div>
                </li>';
    }
    $ret .= '       </ul>
                  </div>
                </div>
              </div>';
  }
  return $ret;
}

if (isset($parametros["accion"])) {
  switch ($parametros["accion"]) {
    case 'listado':
      $res_json = array();
      $query = "SELECT 
                  m.id,
                  m.fecha,
                  m.titulo,
                  m.mensaje,
                  m.id_usuario_origen,
                  m.id_usuario_destino,
                  e.fecha_leido,
                  e.fecha_eliminado,
                  t.descripcion AS tipo_destino,
                  o.apellido AS origen_apellido,
                  o.nombre AS origen_nombre,
                  d.apellido AS destino_apellido,
                  d.nombre AS destino_nombre,
                  count(r.id) AS respuestas,
                  count(rl.id) AS respuestas_leidas,
                  count(re.id) AS respuestas_eliminadas
                FROM
                  sistema.mensajes m
                  LEFT OUTER JOIN sistema.mensajes_estados e ON (m.id = e.id_mensaje AND e.id_usuario = {$usuario->id})
                  LEFT OUTER JOIN sistema.mensajes_respuestas r ON (m.id = r.id_mensaje AND (m.id_tipo IS NOT NULL OR m.id_usuario_destino IS NOT NULL))
                  LEFT OUTER JOIN sistema.mensajes_respuestas_estados rl ON (r.id = rl.id_respuesta AND rl.id_usuario = {$usuario->id} AND rl.fecha_leido IS NOT NULL)
                  LEFT OUTER JOIN sistema.mensajes_respuestas_estados re ON (r.id = re.id_respuesta AND re.id_usuario = {$usuario->id} AND re.fecha_eliminado IS NOT NULL)
                  LEFT OUTER JOIN sistema.usuarios_tipos t ON (m.id_tipo = t.id)
                  LEFT OUTER JOIN sistema.usuarios o ON (m.id_usuario_origen = o.id)
                  LEFT OUTER JOIN sistema.usuarios d ON (m.id_usuario_destino = d.id)
                WHERE
                  e.fecha_eliminado IS NULL AND
                  (
                    (m.id_tipo IS NULL AND m.id_usuario_destino IS NULL) OR
                    m.id_usuario_origen = {$usuario->id} OR m.id_usuario_destino = {$usuario->id} OR m.id_tipo = {$usuario->id_tipo}
                  )
                GROUP BY
                  m.id,
                  m.fecha,
                  m.titulo,
                  m.mensaje,
                  m.id_usuario_origen,
                  m.id_usuario_destino,
                  e.fecha_leido,
                  e.fecha_eliminado,
                  t.descripcion,
                  o.apellido,
                  o.nombre,
                  d.apellido,
                  d.nombre
                ORDER BY
                  m.fecha DESC
      ";
      $res = sql($query) or die($db->ErrorMsg());

      while (!$res->EOF) {
        if (!empty($res->fields["destino_apellido"])) {
          $destino = $res->fields["destino_apellido"].', '.$res->fields["destino_nombre"];
        }
        elseif (!empty($res->fields["tipo_destino"])) {
          $destino = $res->fields["tipo_destino"];
          // $destino = 'Grupo';
        }
        else {
          $destino = 'Todos';
        }
        
        $fecha_leido = empty($res->fields["fecha_leido"]) ? '' : date("d/m/Y H:i:s", strtotime($res->fields["fecha_leido"]));
        $respuestas_nuevas = intval($res->fields["respuestas"]) - intval($res->fields["respuestas_leidas"]) - intval($res->fields["respuestas_eliminadas"]);

        $res_json[] = array(
          "id"                    => $res->fields["id"],
          "id_origen"             => $res->fields["id_usuario_origen"],
          "id_destino"            => $res->fields["id_usuario_destino"],
          "fecha"                 => date("d/m/Y H:i:s", strtotime($res->fields["fecha"])),
          "fecha_leido"           => $fecha_leido,
          "titulo"                => $res->fields["titulo"],
          "mensaje"               => nl2br($res->fields["mensaje"]),
          "remitente"             => $res->fields["origen_apellido"].', '.$res->fields["origen_nombre"],
          "destino"               => $destino,
          "respuestas"            => intval($res->fields["respuestas"]),
          "respuestas_leidas"     => intval($res->fields["respuestas_leidas"]),
          "respuestas_eliminadas" => intval($res->fields["respuestas_eliminadas"]),
          "respuestas_nuevas"     => $respuestas_nuevas
        );
        $res->MoveNext();
      }
      echo json_encode(array("data" =>$res_json));
      break;

    case 'mensajes_nuevos':
      $ret = '';
      $query = "SELECT 
                  m.id,
                  m.fecha,
                  m.titulo,
                  m.mensaje,
                  e.fecha_leido,
                  e.fecha_eliminado,
                  t.descripcion AS tipo_destino,
                  o.apellido AS origen_apellido,
                  o.nombre AS origen_nombre,
                  d.apellido AS destino_apellido,
                  d.nombre AS destino_nombre,
                  count(r.id) AS respuestas,
                  count(rl.id) AS respuestas_leidas,
                  count(re.id) AS respuestas_eliminadas
                FROM
                  sistema.mensajes m
                  LEFT OUTER JOIN sistema.mensajes_estados e ON (m.id = e.id_mensaje AND e.id_usuario = {$usuario->id})
                  LEFT OUTER JOIN sistema.mensajes_respuestas r ON (m.id = r.id_mensaje AND (m.id_tipo IS NOT NULL OR m.id_usuario_destino IS NOT NULL))
                  LEFT OUTER JOIN sistema.mensajes_respuestas_estados rl ON (r.id = rl.id_respuesta AND rl.id_usuario = {$usuario->id} AND rl.fecha_leido IS NOT NULL)
                  LEFT OUTER JOIN sistema.mensajes_respuestas_estados re ON (r.id = re.id_respuesta AND re.id_usuario = {$usuario->id} AND re.fecha_eliminado IS NOT NULL)
                  LEFT OUTER JOIN sistema.usuarios_tipos t ON (m.id_tipo = t.id)
                  LEFT OUTER JOIN sistema.usuarios o ON (m.id_usuario_origen = o.id)
                  LEFT OUTER JOIN sistema.usuarios d ON (m.id_usuario_destino = d.id)
                WHERE
                  e.fecha_eliminado IS NULL AND
                  (
                    (m.id_tipo IS NULL AND m.id_usuario_destino IS NULL) OR
                    m.id_usuario_origen = {$usuario->id} OR m.id_usuario_destino = {$usuario->id} OR m.id_tipo = {$usuario->id_tipo}
                  )
                GROUP BY
                  m.id,
                  m.fecha,
                  m.titulo,
                  m.mensaje,
                  m.id_usuario_origen,
                  m.id_usuario_destino,
                  e.fecha_leido,
                  e.fecha_eliminado,
                  t.descripcion,
                  o.apellido,
                  o.nombre,
                  d.apellido,
                  d.nombre
                ORDER BY
                  m.fecha DESC
      ";
      $res = sql($query) or die($db->ErrorMsg());
      if ($res->recordCount() > 0) {
        $ret .= '<ul class="dropdown-menu-list scroller" style="height: 275px;" data-handle-color="#637283">';

        while (!$res->EOF) {
          if ($res->fields["fecha_leido"] == '' || $res->fields["respuestas_leidas"] < $res->fields["respuestas"]) {
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
            $fecha_actual = new DateTime();
            $fecha = new DateTime($res->fields["fecha_alta"]);
            $titulo = $res->fields["titulo"];

            $fecha_msg = '';
            if ($fecha_actual->format("d/m/Y") != $fecha->format("d/m/Y")) {
              $fecha_msg .= $fecha->format("d/m/Y").'<br/>';
            }
            $fecha_msg .= $fecha->format("H:i:s");
            
            $url = encode_link($html_root."/sistema/mensajes", array("id_mensaje" => $res->fields["id"]));
            $ret .= '<li><a href="'.$url.'" class="ajaxify">
                        <span class="photo"><img src="'.$html_root.'/assets/img/avatar.png" class="img-circle" alt=""></span>
                        <span class="subject">
                          <span class="from">'.$origen.'</span>
                          <span class="time">'.$fecha_msg.'</span>
                        </span>
                        <span class="message">'.$titulo.'</span>
                      </a>
                    </li>';
          }
          $res->MoveNext();
        }
        $ret .= '</ul>';
      }
      echo $ret;
      break;

    case 'responder_mensaje_form':
      $id_mensaje = intval($_POST["id_mensaje"]);
      $id_usuario = $usuario->id;
      $ret = '';
      if ($id_mensaje > 0) {
        $query = "SELECT
                    m.fecha,
                    m.titulo,
                    m.mensaje,
                    t.descripcion AS tipo_destino,
                    o.apellido AS origen_apellido,
                    o.nombre AS origen_nombre,
                    d.apellido AS destino_apellido,
                    d.nombre AS destino_nombre
                  FROM sistema.mensajes m 
                  LEFT OUTER JOIN sistema.mensajes_estados e ON (m.id = e.id_mensaje AND e.id_usuario = {$usuario->id})
                  LEFT OUTER JOIN sistema.usuarios_tipos t ON (m.id_tipo = t.id)
                  LEFT OUTER JOIN sistema.usuarios o ON (m.id_usuario_origen = o.id)
                  LEFT OUTER JOIN sistema.usuarios d ON (m.id_usuario_destino = d.id)
                  WHERE
                    e.fecha_eliminado IS NULL AND
                    m.id = {$id_mensaje} AND
                    (
                      (m.id_tipo IS NULL AND m.id_usuario_destino IS NULL) OR
                      m.id_usuario_origen = {$usuario->id} OR m.id_usuario_destino = {$usuario->id} OR m.id_tipo = {$usuario->id_tipo}
                    )";
        $res = sql($query) or die($db->ErrorMsg());
        if ($res->recordCount() == 1) {
          $origen = $res->fields["origen_apellido"].', '.$res->fields["origen_nombre"];

          $ret .= '<form id="mensaje_leido_form" method="POST" onsubmit="return responder_mensaje_form_submit(this);">';
          $ret .= '<input type="hidden" name="responder_mensaje_form_id" id="responder_mensaje_form_id" value="'.$id_mensaje.'">';

          $ret .= '<div class="row">';
          $ret .= '<div class="col-xs-12">';
          $ret .= '<b>Remitente:</b> '.$origen;
          $ret .= '<div class="pull-right"><b>Fecha:</b> '.date("d/m/Y H:i:s", strtotime($res->fields["fecha"])).'</div>';
          $ret .= '</div>';
          $ret .= '</div>';
          $ret .= '<br/>';
          $ret .= '<div class="row">';
          $ret .= '<div class="col-xs-12">';
          $ret .= '<b>Asunto:</b> '.nl2br($res->fields["titulo"]);
          $ret .= '</div>';
          $ret .= '</div>';
          $ret .= '<br/>';
          $ret .= '<div class="row">';
          $ret .= '<div class="col-xs-12">';
          $ret .= '<b>Mensaje:</b> '.nl2br($res->fields["mensaje"]);
          $ret .= '</div>';
          $ret .= '</div>';
          $ret .= '<br/>';
          $ret .= '<div class="row">';
          $ret .= '<div class="col-xs-12">';
          $ret .= get_respuestas($id_mensaje);
          $ret .= '</div>';
          $ret .= '</div>';
          $ret .= '<br/>';
          $ret .= '<div class="row">';
          $ret .= '<div class="col-xs-12">';
          $ret .= '<label class="control-label bold" for="responder_mensaje_form_respuesta">Nueva respuesta:</label>';
          $ret .= '<textarea class="form-control" id="responder_mensaje_form_respuesta" name="responder_mensaje_form_respuesta" rows="5"></textarea>';
          $ret .= '</div>';
          $ret .= '</div>';
          $ret .= '</form>';
        }
        else {
          $ret .= '<ul class="list-group"><li class="list-group-item alert alert-warning" role="alert">Mensaje no encontrado!</li></ul>';
        }
      }
      else {
        $ret .= 'Par&aacute;metros incorrectos';
      }
      echo $ret;
      break;

    case 'responder_mensaje':
      $id_mensaje = intval($_POST["responder_mensaje_form_id"]);
      $respuesta = $_POST["responder_mensaje_form_respuesta"];
      $id_usuario = $usuario->id;
      $ret = '';
      if ($id_mensaje > 0 && !empty($respuesta)) {
        $query = "SELECT m.id 
                  FROM sistema.mensajes m 
                  LEFT OUTER JOIN sistema.mensajes_estados e ON (m.id = e.id_mensaje AND e.id_usuario = {$usuario->id})
                  WHERE
                    e.fecha_eliminado IS NULL AND
                    m.id = {$id_mensaje} AND
                    (
                      (m.id_tipo IS NULL AND m.id_usuario_destino IS NULL) OR
                      m.id_usuario_origen = {$usuario->id} OR m.id_usuario_destino = {$usuario->id} OR m.id_tipo = {$usuario->id_tipo}
                    )";
        $res = sql($query) or die($db->ErrorMsg());
        if ($res->recordCount() == 1) {
          $db->StartTrans();
          $query = "INSERT INTO sistema.mensajes_respuestas (id_mensaje, id_usuario, respuesta) 
                    VALUES ({$id_mensaje}, {$id_usuario}, {$db->Quote($respuesta)})";
          $res = sql($query);
          if ($res === false) {
            $db->FailTrans();
            $ret .= 'Error al guardar la respuesta del mensaje!';
          }
          else {
            $query = "UPDATE sistema.mensajes_estados SET fecha_leido = NULL 
                      WHERE id_mensaje = {$id_mensaje} AND id_usuario = {$usuario->id}";
            $res = sql($query);
            if ($res === false) {
              $db->FailTrans();
              $ret .= 'Error al cambiar el estado del mensaje!';
            }
          }

          if ($db->CompleteTrans()) {
            $ret .= "OK";
          }
        }
        else{
          $ret .= "Mensaje no encontrado";
        }
      }
      else {
        $ret .= 'Par&aacute;metros incorrectos';
      }
      echo $ret;
      break;

    case 'mensaje_leido':
      $id_mensaje = intval($_POST["id_mensaje"]);
      $ret = '';
      if ($id_mensaje > 0) {
        $query = "SELECT id FROM sistema.mensajes_estados WHERE id_mensaje = {$id_mensaje} AND id_usuario = {$usuario->id}";
        $res = sql($query) or die($db->ErrorMsg());

        $db->StartTrans();

        if ($res->recordCount() == 0) {
          $query = "INSERT INTO sistema.mensajes_estados (id_mensaje, id_usuario, fecha_leido) 
                    VALUES ({$id_mensaje}, {$usuario->id}, CURRENT_TIMESTAMP)";
          $res = sql($query);
          if ($res === false) {
            $ret .= 'Error al marcar como leido al mensaje!';
          }
        }
        else {
          $query = "UPDATE sistema.mensajes_estados SET fecha_leido = CURRENT_TIMESTAMP
                    WHERE id_mensaje = {$id_mensaje} AND id_usuario = {$usuario->id}";
          $res = sql($query);
          if ($res === false) {
            $ret .= 'Error al actualizar el estado del mensaje!';
          }
        }

        $query = "SELECT 
                    r.id AS id_respuesta,
                    re.id AS id_estado,
                    re.fecha_leido
                  FROM
                    sistema.mensajes_respuestas r
                    LEFT OUTER JOIN sistema.mensajes_respuestas_estados re ON (re.id_respuesta = r.id AND re.id_usuario = {$usuario->id})
                  WHERE
                    re.fecha_eliminado IS NULL AND
                    r.id_mensaje = {$id_mensaje}";
        $res = sql($query) or die($db->ErrorMsg());
        if ($res->recordCount() > 0) {
          while (!$res->EOF) {
            $query = "";
            if (empty($res->fields["id_estado"])) {
              $query .= "INSERT INTO sistema.mensajes_respuestas_estados (id_respuesta, id_usuario, fecha_leido) 
                         VALUES ({$res->fields["id_respuesta"]}, {$usuario->id}, CURRENT_TIMESTAMP)";
            }
            elseif (empty($res->fields["fecha_leido"])) {
              $query .= "UPDATE sistema.mensajes_respuestas_estados SET fecha_leido = CURRENT_TIMESTAMP 
                         WHERE id_respuesta = {$res->fields["id_respuesta"]} AND id_usuario = {$usuario->id}";
            }
            if (!empty($query)) {
              $res_leido = sql($query);
              if ($res_leido === false) {
                $ret .= 'Error al actualizar el estado de las respuestas!';
              }
            }
            $res->MoveNext();
          }
        }

        if ($db->CompleteTrans()) {
          $ret .= "OK";
        }
      }
      else {
        $ret .= 'Par&aacute;metros incorrectos';
      }
      echo $ret;
      break;

    case 'mensaje_eliminar':
      $id_mensaje = intval($_POST["id_mensaje"]);
      $ret = '';
      if ($id_mensaje > 0) {
        $db->StartTrans();

        $query = "SELECT id FROM sistema.mensajes_estados WHERE id_mensaje = {$id_mensaje} AND id_usuario = {$usuario->id}";
        $res = sql($query) or die($db->ErrorMsg());


        if ($res->recordCount() == 0) {
          $query = "INSERT INTO sistema.mensajes_estados (id_mensaje, id_usuario, fecha_eliminado) VALUES ({$id_mensaje}, {$id_usuario}, CURRENT_TIMESTAMP)";
        }
        else {
          $query = "UPDATE sistema.mensajes_estados SET fecha_eliminado = CURRENT_TIMESTAMP WHERE id = {$res->fields["id"]}";
        }
        $res = sql($query);
        if ($res === false) {
          $ret .= 'Error al marcar como leido al mensaje!';
        }

        if ($db->CompleteTrans()) {
          $ret .= "OK";
        }
      }
      else {
        $ret .= 'Par&aacute;metros incorrectos';
      }
      echo $ret;
      break;

    case 'mensaje_form':
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

      include_once("mensajes_form.php");
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

    case 'agregar_mensaje':
      $titulo = $_POST["mensaje_form_titulo"];
      $mensaje = $_POST["mensaje_form_mensaje"];
      $ret = '';
      $error = '';

      
      if (empty($error) && empty($titulo)) {
        $error = 'Debe ingresar el asunto del mensaje!';
      }
      if (empty($error) && empty($mensaje)) {
        $error = 'Debe ingresar el mensaje!';
      }

      if (permiso_check('/sistema/mensajes', "editar")) {
        $id_tipo  = isset($_POST["mensaje_form_tipo"]) ? intval($_POST["mensaje_form_tipo"]) : 0;
        $id_usuario = isset($_POST["mensaje_form_usuario"]) ? intval($_POST["mensaje_form_usuario"]) : 0;
        if ($id_tipo <= 0) {
          $id_tipo = 'NULL';
        }
        if ($id_usuario <= 0) {
          $id_usuario = 'NULL';
        }
      }
      else {
        $query = "SELECT 
                    t.id
                  FROM
                    sistema.usuarios_tipos t
                  WHERE
                    t.nombre = 'Sistema'
        ";
        $res = sql($query) or die($db->ErrorMsg());
        if ($res->recordCount() == 0) {
          $id_tipo = 'NULL';
        }
        else {
          $id_tipo = intval($res->fields["id"]);
        }
        $id_usuario = 'NULL';
      }


      if (empty($error)) {
        $query = "INSERT INTO sistema.mensajes (
          id_usuario_origen,
          id_usuario_destino,
          id_tipo,
          titulo,
          mensaje
        ) VALUES (";
        $query .= $usuario->id.", ".
                  $id_usuario.", ".
                  $id_tipo.", ".
                  $db->Quote($titulo).", ".
                  $db->Quote($mensaje).")";

        $res = sql($query) or die($db->ErrorMsg());
        if ($res === false) {
          $ret .= 'Ocurri&oacute; un error al guardar los datos del mensaje!';
        }
        else {
          $ret .= "OK";
        }
      }
      else {
        $ret .= 'ERROR: '.$error;
      }
      echo $ret;
      break;

    case 'mensaje_detalle':
      $id_mensaje  = intval($_POST["id_mensaje"]);
      $ret = '';
      if ($id_mensaje > 0) {
        $query = "SELECT 
                    m.fecha,
                    m.titulo,
                    m.mensaje,
                    e.fecha_leido,
                    t.descripcion AS tipo_destino,
                    o.apellido AS origen_apellido,
                    o.nombre AS origen_nombre,
                    d.apellido AS destino_apellido,
                    d.nombre AS destino_nombre,
                    count(r.id) AS respuestas,
                    count(rl.id) AS respuestas_leidas,
                    count(re.id) AS respuestas_eliminadas
                  FROM
                    sistema.mensajes m
                    LEFT OUTER JOIN sistema.mensajes_estados e ON (m.id = e.id_mensaje AND e.id_usuario = {$usuario->id})
                    LEFT OUTER JOIN sistema.mensajes_respuestas r ON (m.id = r.id_mensaje AND (m.id_tipo IS NOT NULL OR m.id_usuario_destino IS NOT NULL))
                    LEFT OUTER JOIN sistema.mensajes_respuestas_estados rl ON (r.id = rl.id_respuesta AND rl.id_usuario = {$usuario->id} AND rl.fecha_leido IS NOT NULL)
                    LEFT OUTER JOIN sistema.mensajes_respuestas_estados re ON (r.id = re.id_respuesta AND re.id_usuario = {$usuario->id} AND re.fecha_eliminado IS NOT NULL)
                    LEFT OUTER JOIN sistema.usuarios_tipos t ON (m.id_tipo = t.id)
                    LEFT OUTER JOIN sistema.usuarios o ON (m.id_usuario_origen = o.id)
                    LEFT OUTER JOIN sistema.usuarios d ON (m.id_usuario_destino = d.id)
                  WHERE
                    e.fecha_eliminado IS NULL AND 
                    m.id = {$id_mensaje}
                  GROUP BY
                    m.fecha,
                    m.titulo,
                    m.mensaje,
                    e.fecha_leido,
                    t.descripcion,
                    o.apellido,
                    o.nombre,
                    d.apellido,
                    d.nombre
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
          
          if (empty($res->fields["fecha_leido"]) || intval($res->fields["respuestas_leidas"]) < intval($res->fields["respuestas"])) {
            $ret .= '<form id="mensaje_leido_form" method="POST" onsubmit="return mensaje_leido_form_submit(this);">';
            $ret .= '<input type="hidden" name="mensaje_leido_form_url" id="mensaje_leido_form_url" value="'.encode_link($html_root."/sistema/mensajes_datos", array("accion" => "mensaje_leido")).'">';
            $ret .= '<input type="hidden" name="mensaje_leido_form_id" id="mensaje_leido_form_id" value="'.$id_mensaje.'">';
          }
          $ret .= '<div class="row">';
          $ret .= '<div class="col-xs-12">';
          $ret .= '<b>Remitente:</b> '.$origen;
          $ret .= '<div class="pull-right"><b>Fecha:</b> '.date("d/m/Y H:i:s", strtotime($res->fields["fecha"])).'</div>';
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
          $ret .= '<b>Asunto:</b> '.nl2br($res->fields["titulo"]);
          $ret .= '</div>';
          $ret .= '</div>';
          $ret .= '<br/>';
          $ret .= '<div class="row">';
          $ret .= '<div class="col-xs-12">';
          $ret .= '<b>Mensaje:</b> '.nl2br($res->fields["mensaje"]);
          $ret .= '</div>';
          $ret .= '</div>';
          $ret .= '<br/>';
          $ret .= '<div class="row">';
          $ret .= '<div class="col-xs-12">';
          $ret .= get_respuestas($id_mensaje);
          $ret .= '</div>';
          $ret .= '</div>';
          if (empty($res->fields["fecha_leido"]) || intval($res->fields["respuestas_leidas"]) < intval($res->fields["respuestas"])) {
            $ret .= '</form>';
          }
        }
        else {
          $ret .= '<ul class="list-group"><li class="list-group-item alert alert-warning" role="alert">Error al cargar los datos del mensaje!</li></ul>';
        }
      }
      else {
        $ret .= '<ul class="list-group"><li class="list-group-item alert alert-warning" role="alert">Par&aacute;metros incorrectos</li></ul>';
      }
      echo $ret;
      break;

    default:
      echo 'Comando no v&aacute;lido';
      break;
  }
}
?>