<?php require_once(dirname(__FILE__)."/../../config.php"); ?>

<div class="portlet light portlet-fit calendar">
  <div class="portlet-title">
    <div class="caption">
      <i class="fa fa-list-alt font-green"></i>
      <span class="caption-subject font-green sbold uppercase">Mensajes</span>
    </div>
    <div class="actions">
      <?php $url = encode_link($html_root."/sistema/mensajes_datos", array("accion" => "mensaje_form")); ?>
        <a href="#popup_modal" class="btn btn-circle btn-success tooltips" data-toggle="modal" data-url="<?php echo $url; ?>" title="Enviar un nuevo mensaje">
          <i class="fa fa-plus-circle"></i> Enviar mensaje
        </a>
    </div>
  </div>
  <div class="portlet-body">
    <div class="table-responsive">  
      <table id="listado-mensajes" class="table table-condensed" width="100%">
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Remitente</th>
            <th>Asunto</th>
            <th>Mensaje</th>
            <th>Respuestas</th>
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

function mensaje_leido(id) {
  var url = '<?php echo encode_link($html_root."/sistema/mensajes_datos", array("accion" => "mensaje_leido")); ?>';
  $.post(url,
    { id_mensaje: id },
    function(data) {
      if (data.trim() == "OK") {
        toastr.success('Mensaje marcado como leido');
        if (typeof(tabla) != 'undefined') {
          tabla.ajax.reload();
        }
      }
      else {
        toastr.alert(data);
      }
    }
  ).fail(function() {
      toastr.alert('Ocurrio un error al marcar como leido el mensaje!');
  });
  return false;
}

function responder_mensaje(id) {
  var url = '<?php echo encode_link($html_root."/sistema/mensajes_datos", array("accion" => "responder_mensaje_form")); ?>';
  App.showPopupModal({
    title: 'Responder mensaje',
    url: url,
    form_data: { 
      id_mensaje: id
    }
  });
  return false;
}


function responder_mensaje_form_submit(form) {
  var url_submit = '<?php echo encode_link($html_root."/sistema/mensajes_datos", array("accion" => "responder_mensaje")); ?>';
  $.post(url_submit, $(form).serialize(), function(data) {
    if (data.trim() == "OK") {
      if (typeof(tabla) != 'undefined') {
        tabla.ajax.reload();
      }
      toastr.success('Respuesta enviada correctamente.');
      $('#popup_modal').modal('hide');
    }
    else {
      toastr.error(data);
    }
  }).fail(function() {
      toastr.error('Ocurrio un error al guardar los datos!');
  });
  return false;
}

$(document).ready(function() {
  var url = '<?php echo encode_link($html_root."/sistema/mensajes_datos", array("accion" => "listado")); ?>';
  var url_detalles = '<?php echo encode_link($html_root."/sistema/mensajes_datos", array("accion" => "mensaje_detalle")); ?>';

  tabla = $('#listado-mensajes').DataTable({
    ajax: url,
    pageLength: 10,
    ordering: false,
    columns: [
      { data: "fecha", orderable: false, lassName: 'link', width: "15%" },
      { data: "remitente", orderable: false, className: 'link', width: "15%" },
      { data: "titulo", orderable: false, className: 'link', width: "20%" },
      { data: "mensaje", orderable: false, className: 'link', width: "25%" },
      { data: "respuestas", orderable: false, className: 'link text-center', width: "10%" },
      { data: "id", searchable: false, orderable: false, className: 'text-center', width: "15%" }
    ],
    columnDefs: [ {
      targets: -1,
      render: function ( data, type, full, meta ) {
        var ret = '';
        if (full.destino != 'Todos' || full.destino == 'Administrador de Sistema') {
          ret += '<a href="#" title="Responder" onclick="return responder_mensaje('+full.id+');" class="btn btn-icon-only green tooltips"><i class="fa fa-reply" aria-hidden="true"></i></a>';
        }
        if (full.fecha_leido == '') {
          ret += '<a href="#" title="Marcar como leido" onclick="return mensaje_leido('+full.id+');" class="btn btn-icon-only blue tooltips"><i class="fa fa-envelope-open-o" aria-hidden="true"></i></a>';
        }
        ret += '<a href="#" title="Eliminar" class="btn btn-icon-only red tooltips btn-eliminar" data-toggle="confirmation" title="Eliminar?" data-id="'+full.id+'"><i class="fa fa-times" aria-hidden="true"></i></a>';
        return ret;
      }
    }],
    createdRow: function( row, data, dataIndex ) {
      $(row).data('id', data.id);
      if (data.fecha_leido == '' || data.respuestas_leidas < data.respuestas) {
        $(row).addClass('bg-success');
      }
    }
  });

  $('#listado-mensajes').on( 'draw.dt', function () {
    $("#listado-mensajes .link")
      .off("click")
      .on("click", function() {
        App.showPopupModal({
          title: 'Detalles del mensaje',
          url: url_detalles,
          form_data: { 
            id_mensaje: $(this).closest('tr').data('id') 
          }
        });
    });
    App.initConfirmation();
    $("#listado-mensajes .btn-eliminar")
      .off("confirmed.bs.confirmation")
      .on("confirmed.bs.confirmation", function () {
        var url = '<?php echo encode_link($html_root."/sistema/mensajes_datos", array("accion" => "mensaje_eliminar")) ?>';
        $.post(url, { 
          id_mensaje: $(this).data("id"),
        }, function(data) {
          if (data == "OK") {
            toastr.success("Mensaje eliminado correctamente.");
            if (typeof(tabla) != 'undefined') {
              tabla.ajax.reload();
            }
          }
          else {
            toastr.error(data);
          }
        }).fail(function() {
          toastr.error("Ocurrió un error al eliminar el mensaje!");
        });
    });
  });

  <?php if (isset($parametros["id_mensaje"])) { ?>
  App.showPopupModal({
    title: 'Detalles del mensaje',
    url: url_detalles,
    form_data: { 
      id_mensaje: <?php echo $parametros["id_mensaje"]; ?>
    }
  });
  <?php } ?>

});  
</script>
