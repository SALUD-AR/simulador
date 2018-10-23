<?php require_once(dirname(__FILE__)."/../../config.php"); ?>

<div class="portlet box blue-hoki">
  <div class="portlet-title">
      <div class="caption">
          <i class="fa fa-list-alt"></i> Listado de permisos </div>
      <div class="actions">

      </div>
  </div>
  <div class="portlet-body">
    <div class="table-responsive">  
      <table id="listado-permisos" class="table table-striped table-condensed" width="100%">
        <thead>
          <tr>
            <th>ID</th>
            <th>Permiso</th>
            <th>URL</th>
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
$(document).ready(function() {
  // var url = '<?php echo encode_link($html_root."/sistema/usuarios_datos", array("accion" => "listado")); ?>';
  // tabla = $('#listado-usuarios').DataTable({
  //   ajax: url,
  //   order: [[ 1, 'asc' ], [ 2, 'asc' ]],
  //   columns: [
  //     { data: "id", className: 'link', width: "5%" },
  //     { data: "apellido", className: 'link', width: "20%" },
  //     { data: "nombre", className: 'link', width: "20%" },
  //     { data: "tipo", className: 'link', width: "15%" },
  //     { data: "telefono", className: 'text-center link', width: "15%" },
  //     { data: "email", className: 'link', width: "10%" },
  //     { data: "id", searchable: false, orderable: false, className: 'text-center', width: "15%" }
  //   ],
  //   columnDefs: [ {
  //     targets: -1,
  //     render: function ( data, type, full, meta ) {
  //       var ret = '';
  //       <?php if (permiso_check('/sistema/usuarios', "editar")) { ?>
  //       <?php $url_edit = encode_link($html_root."/sistema/usuarios_datos", array("accion" => "modificar_form")); ?>
  //       ret += '<button href="#popup_modal" class="btn btn-icon-only yellow btn-outline" data-toggle="modal" data-id="'+data+'" data-url="<?php echo $url_edit; ?>" title="Modificar Usuario">';
  //       ret += '<i class="fa fa-pencil" data-tooltip="true" title="Modificar Usuario"></i>';
  //       ret += '</button>&nbsp;&nbsp;&nbsp;';
  //       ret += '<button href="#" class="btn-borrar btn btn-icon-only red btn-outline" data-id="'+data+'" data-tooltip="true" title="Eliminar Usuario">';
  //       ret += '<i class="fa fa-trash"></i>';
  //       ret += '</button>';
  //       <?php } ?>
  //       return ret;
  //     }
  //   }],
  //   createdRow: function( row, data, dataIndex ) {
  //     $(row).prop('data-id', data.id);
  //   }
  // });

  // $('#listado-usuarios').on('click', 'a.btn-borrar', function(e) {
  //   e.preventDefault();
  //   $('#confirm-delete .btn-borrar').data('id',  $(this).data('id'));
  //   $('#confirm-delete').modal();
  // });

//   $('#confirm-delete .btn-borrar').click(function(e) {
//     event.preventDefault();

//     var url = '<?php echo encode_link($html_root."/sistema/usuarios_datos", array("accion" => "eliminar_usuario")); ?>';
//     var id = $(this).data('id');
//     if (id) {
//       loading_fade = false;
//       $.post(url,
//         { id_usuario: id },
//         function(data) {
//           if (data.trim() == "OK") {
//             notify_success('Usuario eliminado correctamente');
//             if (typeof(tabla) != 'undefined') {
//               tabla.ajax.reload();
//             }
//           }
//           else {
//             BootstrapDialog.alert(data);
//           }
//         }
//       ).fail(function() {
//           BootstrapDialog.alert('Ocurrio un error al eliminar el usuario!');
//         }
//       ).always(function() {
//         $('#confirm-delete').modal('hide');
//       });
//     }
//   });
});  
</script>
