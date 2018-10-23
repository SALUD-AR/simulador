<?php require_once(dirname(__FILE__)."/../../config.php"); ?>

<div class="panel panel-primary">
  <div class="panel-heading">
    <?php $url = encode_link($html_root."/sistema/alertas_datos", array("accion" => "alerta_form")); ?>
    <a href="#popup_modal" data-toggle="modal" data-url="<?php echo $url; ?>" title="Agregar Alerta">
      <i class="btn btn-primary btn-panel-header pull-right fa fa-plus-square" data-tooltip="true" title="Agregar Alerta"></i>
    </a>
    <span class="lead">Listado de alertas</span>
  </div>
  <div class="panel-body">
    <div class="table-responsive">  
      <table id="listado-alertas" class="table table-condensed" width="100%">
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Remitente</th>
            <th>Destino</th>
            <th>Mensaje</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script type="text/javascript">
var tabla = null;

function alerta_leida(id) {
  var url = '<?php echo encode_link($html_root."/sistema/alertas_datos", array("accion" => "alerta_leida")); ?>';

  loading_fade = false;
  $.post(url,
    { id_alerta: id },
    function(data) {
      if (data.trim() == "OK") {
        notify_success('Alerta marcada como leida');
        if (typeof(tabla) != 'undefined') {
          tabla.ajax.reload();
        }
      }
      else {
        BootstrapDialog.alert(data);
      }
    }
  ).fail(function() {
      BootstrapDialog.alert('Ocurrio un error al marcar como leida la alerta!');
  });
  return false;
}

$(document).ready(function() {
  var url = '<?php echo encode_link($html_root."/sistema/alertas_datos", array("accion" => "listado")); ?>';
  tabla = $('#listado-alertas').DataTable({
    ajax: url,
    order: [[ 0, 'desc' ]],
    columns: [
      { data: "fecha", className: 'link', width: "15%" },
      { data: "remitente", className: 'link', width: "15%" },
      { data: "destino", className: 'link', width: "15%" },
      { data: "mensaje", className: 'link', width: "40%" },
      { data: "id", searchable: false, orderable: false, className: 'text-center', width: "15%" }
    ],
    columnDefs: [ {
      targets: -1,
      render: function ( data, type, full, meta ) {
        var ret = '';
        if (full.fecha_leido == '') {
          ret += '<a data-tooltip="true" href="#" title="Marcar como leido" onclick="return alerta_leida('+full.id+');" class="btn btn-success btn-sm"><i class="fa fa-check-circle-o" aria-hidden="true"></i></a>';
        }
        return ret;
      }
    }],
    createdRow: function( row, data, dataIndex ) {
      if ( data.fecha_leido == '' ) {
        $(row).addClass('bg-warning');
      }
      else {
        $(row).addClass('bg-success');
      }
    }
  });
  $('.dropdown.open .dropdown-toggle').dropdown('toggle');
});  
</script>
