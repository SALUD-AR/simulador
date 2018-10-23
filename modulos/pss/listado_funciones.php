<?php 
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