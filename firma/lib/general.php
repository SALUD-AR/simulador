<?php
date_default_timezone_set("America/Argentina/Buenos_Aires");

require_once(LIB_DIR."/adodb/adodb.inc.php");
require_once(LIB_DIR."/adodb/adodb-pager.inc.php");
require_once(LIB_DIR."/class.phpmailer.php");

$GLOBALS["parametros"] = isset($_GET["p"]) ? decode_link($_GET["p"]) : array();

function comprimir_variable($var) {
  $ret = "";
  if ($var != "") {
    $var = serialize($var);
    if ($var != "") {
      $gz = @gzcompress($var);
      if ($gz != "") {
        $ret = base64_encode($gz);
      }
    }
  }
  return $ret;
}

function descomprimir_variable($var) {
  $ret = "";
  if ($var != "") {
    $var = base64_decode($var);
    if ($var != "") {
      $gz = @gzuncompress($var);
      if ($gz != "") {
        $ret = unserialize($gz);
      }
    }
  }
  return $ret;
}

function mix_string($string) {
  $split = 4;    // mezclar cada $split caracteres
  $str = str_replace("=","",$string);
  $string = "";
  $str_tmp = explode(":",chunk_split($str,$split,":"));
  for ($i=0;$i<count($str_tmp);$i+=2) {
    if (strlen($str_tmp[$i+1]) != $split) {
      $string .= $str_tmp[$i] . $str_tmp[$i+1];
    }
    else {
      $string .= $str_tmp[$i+1] . $str_tmp[$i];
    }
  }
  return str_replace(" ","+",$string);
}

function encode_link() {
  $args = func_num_args();
  if ($args == 2) {
    $link = func_get_arg(0);
    $p = func_get_arg(1);
  }
  elseif ($args == 1) {
    $p = func_get_arg(0);
  }
  $str = comprimir_variable($p);
  $string = mix_string($str);
  if (isset($link)) {
    return $link."?p=".$string;
  }
  else {
    return $string;
  }
}

function decode_link($link) {
  $str = mix_string($link);
  $cant = strlen($str)%4;
  if ($cant > 0) {
    $cant = 4 - $cant;
  }
  for ($i=0;$i < $cant;$i++) {
   $str .= "=";
  }
  return descomprimir_variable($str);
}

function sql($sql, $error = -1) {
  global $db,$contador_consultas,$debug_datos;
  $msg = "";
  $result = null;
  if (count($sql) > 1 or is_array($sql)) {
    $db->StartTrans();
    foreach ($sql as $indice => $sql_str) {
      $debug_datos_temp["sql"] = $sql_str;
      if ($db->Execute($sql_str) === false) {
        $msg .= "(Consulta ".($indice + 1)."): ".$db->ErrorMsg()."<br>";
        $debug_datos_temp["error"] = $db->ErrorMsg();
        // echo $db->ErrorMsg();
        // sql_error($error,$sql_str,$db->ErrorMsg());
      }
      else {
        $debug_datos_temp["affected"] = $db->Affected_Rows();
        // $debug_datos_temp["count"] = $result->RecordCount();
      }
      $debug_datos[] = $debug_datos_temp;
      $contador_consultas++;
    }
    $db->CompleteTrans();
  }
  else {
    $result = $db->Execute($sql);
    $debug_datos_temp["sql"] = $sql;
    if (!$result) {
      $msg .= $db->ErrorMsg()."<br>";
      $debug_datos_temp["error"] = $db->ErrorMsg();
      // echo $db->ErrorMsg();
      // sql_error($error,$sql,$db->ErrorMsg());
    }
    else {
      // $debug_datos_temp["affected"] = $db->Affected_Rows();
      $debug_datos_temp["count"] = $result->RecordCount();
    }
    $debug_datos[] = $debug_datos_temp;
    $contador_consultas++;
  }
  if ($msg) {
    if ($error != -1) {
      echo "</form></center></table><br><font color=#ff0000 size=3><b>ERROR $error: No se pudo ejecutar la consulta en la base de datos.</font><br>Descripción:<br>$msg</b>";
    }
    return false;
  }
  if ($result)
    return $result;
  else
    return true;
}

function sql_error($error,$sql_error,$db_msg) {
  global $usuario,$db;
  $error = addslashes($error);
  $sql_error = encode_link($sql_error);
  $db_msg = encode_link($db_msg);
  $sql = "INSERT INTO errores_sql (codigo_error, sql, msg_error, fecha, usuario) ";
  $sql .= "VALUES ('$error', '$sql_error', '$db_msg', '".date("Y-m-d H:i:s")."', ";
  $sql .= "'".$usuario->login."')";
  $result = $db->Execute($sql);
}

function form_login($error = 0, $extra = array()) {
  global $html_root, $parametros, $db;
  $url = $html_root.'/';
  if (is_array($extra) && !empty($extra)) {
    $parametros = $extra;
  }
  if (intval($error) > 0) {
    $parametros["e"] = intval($error);
  }
  if (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && $_SERVER["HTTP_X_REQUESTED_WITH"] == "XMLHttpRequest") {
    echo '<script type="text/javascript">window.top.location="'.$url.'";</script>';
  }
  else {
    header('Location: '.$html_root.'/login.php?log=err_log');
  }
  die();
}

function enviar_mail($destino, $nombre, $subject, $body) {
  global $html_root;
  $server = (empty($_SERVER["SERVER_NAME"]) ? "186.33.221.1" : $_SERVER["SERVER_NAME"]); 

  $mail_content = '
    <table width="560" border="0" cellspacing="0" cellpadding="0">
      <tbody>
        <tr>
          <td colspan="3" height="15" style="font-size:1px;line-height:1px"></td>
        </tr>
        <tr>
          <td width="15">&nbsp;</td>
          <td width="530" valign="top">
            <table width="530" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td width="65" valign="top"><a href="http://'.$server.$html_root.'/" target="_blank"><img src="http://'.$server.$html_root.'/assets/img/logo-big.png" alt="Historia Cl&iacute;nica" width="80" border="0"></a></td>
                  <td width="15" valign="top">&nbsp;</td>
                  <td width="1" valign="top" bgcolor="#cecece"></td>
                  <td width="15" valign="top">&nbsp;</td>
                  <td width="434" valign="top" style="font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#666">
                    '.$body.'
                  </td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>
      </tbody>
    </table>
  ';

  $mail = new PHPMailer;

  $mail->setLanguage('es');

  $mail->CharSet = 'UTF-8';

  //Tell PHPMailer to use SMTP
  $mail->isSMTP();
  //Enable SMTP debugging
  // 0 = off (for production use)
  // 1 = client messages
  // 2 = client and server messages
  $mail->SMTPDebug = 0;
  //Ask for HTML-friendly debug output
  $mail->Debugoutput = 'html';
  //Set the hostname of the mail server
  $mail->Host = 'smtp.gmail.com';
  // use
  // $mail->Host = gethostbyname('smtp.gmail.com');
  // if your network does not support SMTP over IPv6
  //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
  $mail->Port = 587;
  //Set the encryption system to use - ssl (deprecated) or tls
  $mail->SMTPSecure = 'tls';
  //Whether to use SMTP authentication
  $mail->SMTPAuth = true;
  //Username to use for SMTP authentication - use full email address for gmail
  $mail->Username = "historia.clinica.informacion@gmail.com";
  //Password to use for SMTP authentication
  $mail->Password = "Historia/Clinica/Admin";
  //Set who the message is to be sent from
  $mail->setFrom('historia.clinica.informacion@gmail.com', 'Sistema de Historia Clínica');
  //Set an alternative reply-to address
  // $mail->addReplyTo('replyto@example.com', 'First Last');
  //Set who the message is to be sent to
  $mail->addAddress($destino, $nombre);
  //Set the subject line
  $mail->Subject = $subject;
  //Read an HTML message body from an external file, convert referenced images to embedded,
  //convert HTML into a basic plain-text alternative body
  $mail->msgHTML($mail_content);
  //Replace the plain text body with one created manually
  // $mail->AltBody = 'This is a plain-text message body';
  //Attach an image file
  // $mail->addAttachment('images/phpmailer_mini.png');
  //send the message, check for errors
  return $mail->send();
}

$db = ADONewConnection($db_type) or die("Error al conectar a la base de datos");
$db->Connect($db_host, $db_user, $db_password, $db_name);
$db->cacheSecs = 3600;
$result=$db->Execute("SET search_path=".join(",",$db_schemas)) or die($db->ErrorMsg());
unset($result);
$db->debug = $db_debug;
$db->SetFetchMode(ADODB_FETCH_BOTH);
