<?php 
function generar_options_proc($selected = "") {
  global $db;
  $ret = '';
  $query="SELECT id_prod, descri
          FROM simulador.proc
          ORDER BY
            descri ASC";
  $res = sql($query) or die($db->ErrorMsg());

  if ($res->recordCount() > 0) {
    while (!$res->EOF) {
      $ret .= '<option value="'.$res->fields["id_prod"].'"';
      if ($selected == $res->fields["descri"]) {
        $ret .= ' selected="selected"';
      }
      $ret .= '>'.$res->fields["descri"].'</option>';
      $res->MoveNext();
    }
  }
  else {
    $ret .= '<option value="0">Noy hay datos</option>';
  }
  return $ret;
}

function generar_options_esp($selected = "") {
  global $db;
  $ret = '';
  $query="SELECT id_esp, descri
          FROM simulador.esp
          ORDER BY
            descri ASC";
  $res = sql($query) or die($db->ErrorMsg());

  if ($res->recordCount() > 0) {
    while (!$res->EOF) {
      $ret .= '<option value="'.$res->fields["id_esp"].'"';
      if ($selected == $res->fields["descri"]) {
        $ret .= ' selected="selected"';
      }
      $ret .= '>'.$res->fields["descri"].'</option>';
      $res->MoveNext();
    }
  }
  else {
    $ret .= '<option value="0">Noy hay datos</option>';
  }
  return $ret;
}

function generar_options_amb($selected = "") {
  global $db;
  $ret = '';
  $query="SELECT id_amb, descri
          FROM simulador.amb
          ORDER BY
            descri ASC";
  $res = sql($query) or die($db->ErrorMsg());

  if ($res->recordCount() > 0) {
    while (!$res->EOF) {
      $ret .= '<option value="'.$res->fields["id_amb"].'"';
      if ($selected == $res->fields["descri"]) {
        $ret .= ' selected="selected"';
      }
      $ret .= '>'.$res->fields["descri"].'</option>';
      $res->MoveNext();
    }
  }
  else {
    $ret .= '<option value="0">Noy hay datos</option>';
  }
  return $ret;
}

function generar_options_diag($selected = "") {
  global $db;
  $ret = '';
  $query="SELECT id_diag, descri
          FROM simulador.diag
          ORDER BY
            descri ASC";
  $res = sql($query) or die($db->ErrorMsg());

  if ($res->recordCount() > 0) {
    while (!$res->EOF) {
      $ret .= '<option value="'.$res->fields["id_diag"].'"';
      if ($selected == $res->fields["descri"]) {
        $ret .= ' selected="selected"';
      }
      $ret .= '>'.$res->fields["descri"].'</option>';
      $res->MoveNext();
    }
  }
  else {
    $ret .= '<option value="0">Noy hay datos</option>';
  }
  return $ret;
}

