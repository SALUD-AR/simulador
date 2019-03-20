<?php
require_once(dirname(__FILE__)."/../../config.php");

if (isset($parametros["accion"])) {
  switch ($parametros["accion"]) {
    case 'listado':
      $res_json = array();
      $query = "SELECT 
                  u.id,
                  u.apellido,
                  u.nombre,
                  t.descripcion AS tipo,
                  u.telefono,
                  u.email,
				          u.cuil
                FROM
                  sistema.usuarios u
                  LEFT OUTER JOIN sistema.usuarios_tipos t ON (u.id_tipo = t.id)
                WHERE 
                  u.activo = 't'
      ";

      $res = sql($query) or die($db->ErrorMsg());

      while (!$res->EOF) {
        $res_json[] = array(
          "id"       => $res->fields["id"],
          "apellido" => $res->fields["apellido"],
          "nombre"   => $res->fields["nombre"],
          "tipo"     => $res->fields["tipo"],
          "telefono" => $res->fields["telefono"],
          "email"    => $res->fields["email"],
		      "cuil"    => $res->fields["cuil"]
        );
        $res->MoveNext();
      }
      echo json_encode(array("data" =>$res_json));
      break;

    case 'eliminar_usuario':
      $id_usuario = intval($_POST["id_usuario"]);
      $ret = '';
      if ($id_usuario > 0) {
        $query = "UPDATE sistema.usuarios u SET activo = 'f' WHERE u.id = $id_usuario";
        $res = sql($query) or die($db->ErrorMsg());
        if ($res !== false) {
          $ret .= "OK";
        }
      }
      else {
        $ret .= '<ul class="list-group"><li class="list-group-item alert alert-warning" role="alert">Par&aacute;metros incorrectos</li></ul>';
      }
      echo $ret;
      break;

    case 'agregar_form':
      $form_id = "";
      $form_login = "";
      $form_tipo = "";
      $form_apellido = "";
      $form_nombre = "";
      $form_direccion = "";
      $form_telefono = "";
      $form_email = "";
	    $form_cuil = "";
      $form_observaciones = "";

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

      echo '<form id="usuario_form" method="POST" onsubmit="return usuario_form_submit(this);">';
      include_once("usuarios_form.php");
      echo '</form>';
      break;

    case 'modificar_form':
      $id_usuario = intval($_POST["id"]);
      if ($id_usuario > 0) {
        $query = "SELECT 
                    u.id,
                    u.login,
                    u.apellido,
                    u.nombre,
                    u.id_tipo,
                    u.direccion,
                    u.telefono,
                    u.email,
					          u.cuil,
                    u.observaciones
                  FROM
                    sistema.usuarios u
                  WHERE 
                    u.activo = 't' AND
                    u.id = $id_usuario
        ";
        $res = sql($query) or die($db->ErrorMsg());
        if ($res !== false && $res->recordCount() == 1) {
          $form_id = $res->fields["id"];
          $form_login = $res->fields["login"];
          $form_tipo = $res->fields["id_tipo"];
          $form_apellido = $res->fields["apellido"];
          $form_nombre = $res->fields["nombre"];
          $form_direccion = $res->fields["direccion"];
          $form_telefono = $res->fields["telefono"];
          $form_email = $res->fields["email"];
		      $form_cuil = $res->fields["cuil"];
          $form_observaciones = $res->fields["observaciones"];

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

          echo '<form id="usuario_form" method="POST" onsubmit="return usuario_form_submit(this);">';
          include_once("usuarios_form.php");
          echo '</form>';
        }
        else {
          echo '<ul class="list-group"><li class="list-group-item alert alert-warning" role="alert">Usuario incorrecto</li></ul>';
        }
      }
      else {
          echo '<ul class="list-group"><li class="list-group-item alert alert-warning" role="alert">Par&aacute;metros incorrectos</li></ul>';
      }
      break;

    case 'agregar_usuario':
      $id_tipo  = intval($_POST["usuario_form_tipo"]);
      $login = $_POST["usuario_form_login"];
      $apellido = $db->Quote($_POST["usuario_form_apellido"]);
      $nombre = $db->Quote($_POST["usuario_form_nombre"]);
      $passwd1 = $_POST["usuario_form_passwd1"];
      $passwd2 = $_POST["usuario_form_passwd2"];
      $direccion = $db->Quote($_POST["usuario_form_direccion"]);
      $telefono = $db->Quote($_POST["usuario_form_telefono"]);
      $email = $db->Quote($_POST["usuario_form_email"]);
      $cuil = $db->Quote($_POST["usuario_form_cuil"]);
      $observaciones = $db->Quote($_POST["usuario_form_observaciones"]);
      $ret = '';
      $error = '';
      if ($id_tipo <= 0) {
        $error = 'Debe seleccionar el tipo de usuario!';
      }
      if (empty($error) && empty($login)) {
        $error = 'Debe ingresar el nombre de usuario!';
      }
      if (empty($error) && (empty($passwd1) || empty($passwd2))) {
        $error = 'Debe ingresar la contrase&ntilde;a y la confirmaci&oacute;n de la contrase&ntilde;a para el usuario!';
      }
      if (empty($error) && $passwd1 != $passwd2) {
        $error = 'La contrase&ntilde;a y la confirmaci&oacute;n de la contrase&ntilde;a no coinciden!';
      }
      if (empty($error)) {
        $query = "INSERT INTO sistema.usuarios (
          id_tipo,
          login,
          passwd,
          apellido,
          nombre,
          direccion,
          telefono,
          email,
          cuil,
          observaciones
        ) VALUES (";
        $query .= $id_tipo.", ".
                  $db->Quote($login).", ".
                  $db->Quote(md5($passwd1)).", ".
                  $apellido.", ".
                  $nombre.", ".
                  $direccion.", ".
                  $telefono.", ".
                  $email.", ".
                  $cuil.", ".
                  $observaciones.")";

        $res = sql($query) or die($db->ErrorMsg());
        if ($res === false) {
          $ret .= '<ul class="list-group"><li class="list-group-item alert alert-warning" role="alert">Ocurri&oacute; un error al guardar los datos del usuario</li></ul>';
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

    case 'modificar_usuario':
      $id_usuario  = intval($_POST["usuario_form_id"]);
      $id_tipo  = intval($_POST["usuario_form_tipo"]);
      $login = $_POST["usuario_form_login"];
      $apellido = $db->Quote($_POST["usuario_form_apellido"]);
      $nombre = $db->Quote($_POST["usuario_form_nombre"]);
      $passwd1 = $_POST["usuario_form_passwd1"];
      $passwd2 = $_POST["usuario_form_passwd2"];
      $direccion = $db->Quote($_POST["usuario_form_direccion"]);
      $telefono = $db->Quote($_POST["usuario_form_telefono"]);
      $email = $db->Quote($_POST["usuario_form_email"]);
      $cuil = $db->Quote($_POST["usuario_form_cuil"]);
      $observaciones = $db->Quote($_POST["usuario_form_observaciones"]);
      $ret = '';
      $error = '';
      if ($id_usuario <= 0) {
        $error = 'Debe seleccionar el usuario!';
      }
      if ($id_tipo <= 0) {
        $error = 'Debe seleccionar el tipo de usuario!';
      }
      if (empty($error) && empty($login)) {
        $error = 'Debe ingresar el nombre de usuario!';
      }
      if (empty($error) && $passwd1 != $passwd2) {
        $error = 'La contrase&ntilde;a y la confirmaci&oacute;n de la contrase&ntilde;a no coinciden!';
      }
      if (empty($error)) {
        $query = "UPDATE sistema.usuarios SET ";
        $query .= "id_tipo = $id_tipo,";
        $query .= "login = ".$db->Quote($login).",";
        if (!empty($passwd1) && !empty($passwd2)) {
          $query .= "passwd = ".$db->Quote(md5($passwd1)).",";
        }
        $query .= "apellido = $apellido,";
        $query .= "nombre = $nombre,";
        $query .= "direccion = $direccion,";
        $query .= "telefono = $telefono,";
        $query .= "email = $email,";
		    $query .= "cuil = $cuil,";
        $query .= "observaciones = $observaciones ";
        $query .= "WHERE id = $id_usuario";

        $res = sql($query) or die($db->ErrorMsg());
        if ($res === false) {
          $ret .= '<ul class="list-group"><li class="list-group-item alert alert-warning" role="alert">Ocurri&oacute; un error al guardar los datos del usuario</li></ul>';
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

    default:
      echo '<div class="alert alert-warning text-center" role="alert"><span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span> Comando no v&aacute;lido</div>';
      break;
  }
}
?>