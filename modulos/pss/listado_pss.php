<?php require_once(dirname(__FILE__)."/../../config.php"); ?>
<div class="portlet light portlet-fit calendar">
  <div class="portlet-title">
    <div class="caption">
      <i class="fa fa-list-alt font-green"></i>
      <span class="caption-subject font-green sbold uppercase">Listado de pacientes</span>
    </div>
    <div class="actions">
        <?php if (permiso_check('/pss/agregar', "editar")) { ?>
        <a href="<?php echo $html_root."/pss/agregar"; ?>" class="btn btn-circle btn-success ajaxify tooltips" title="Agregar PSS">
          <i class="fa fa-plus-circle"></i> Agregar
        </a>
        <?php } ?>
    </div>
  </div>
  <div class="portlet-body">
    <div class="table-responsive">  
      <table id="listado-pss" class="table table-striped table-condensed" width="100%">
        <thead>
          <tr>
            <th>ID</th>
            <th>Codigo</th>
            <th>Prestacion</th>
            <th>C Procedimiento</th>
            <th>C Especialidad</th>
            <th>C Ambito</th>
            <th>C Diagnostico</th>
            <th>Grupo</th>
            <th>Sexo</th>
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

  function eliminar_paciente_confirm() {
    var url = '<?php echo encode_link($html_root."/pss/listado_datos", array("accion" => "eliminar_pss")); ?>';
    $.post(url, {
        id_pss: $(this).data('id'),
      },
      function(data) {
        if (data.trim() == "OK") {
          toastr.success('Registro eliminado correctamente');
          if (typeof(tabla) != 'undefined') {
            tabla.ajax.reload();
          }
        }
        else {
          toastr.error(data);
        }
      }
    ).fail(function() {
        toastr.error('Ocurri&oacute; un error al eliminar el Registro!');
    });
  }

  function clonar_paciente_confirm() {
    var url = '<?php echo encode_link($html_root."/pss/listado_datos", array("accion" => "clonar_pss")); ?>';
    $.post(url, {
        id_pss: $(this).data('id'),
      },
      function(data) {
        if (data.trim() == "OK") {
          toastr.success('Registro clonado correctamente');
          if (typeof(tabla) != 'undefined') {
            tabla.ajax.reload();
          }
        }
        else {
          toastr.error(data);
        }
      }
    ).fail(function() {
        toastr.error('Ocurri&oacute; un error al clonado el Registro!');
    });
  }

  $(document).ready(function() {
    var url = '<?php echo encode_link($html_root."/pss/listado_datos", array("accion" => "listado")); ?>';
    tabla = $('#listado-pss').DataTable({
      ajax: url,
      order: [[ 1, 'asc' ], [ 2, 'asc' ]],
      columns: [
        { data: "id", className: 'link', width: "5%" },
        { data: "cod", className: 'link', width: "10%" },
        { data: "prestacion", className: 'link', width: "10%" },
        { data: "cod_procedimiento", className: 'link', width: "10%" },
        { data: "cod_especialidad", className: 'link', width: "10%" },
        { data: "cod_ambito", className: 'link', width: "15%" },
        { data: "cod_diagnostico", className: 'link', width: "15%" },
        { data: "grupo_etareo", className: 'link', width: "5%" },
        { data: "sexo", className: 'link', width: "5%" },
        { data: "id", searchable: false, orderable: false, className: 'text-center', width: "15%" }
      ],
      columnDefs: [ {
        targets: -1,
        render: function ( data, type, full, meta ) {
          var ret = '';
          <?php if (permiso_check('/pss/modificar', "editar")) { ?>
          ret += '<button class="btn btn-icon-only purple btn-outline" data-toggle="confirmation" data-on-confirm="clonar_paciente_confirm" data-copy-attributes="data-id" data-id="'+data+'" title="Desea Clonar el Registro Seleccionado?"><i class="fa fa-clone"></i></button>';
          ret += '<button class="btn-editar btn btn-icon-only yellow btn-outline ajax-load tooltips" data-id="'+data+'" data-url="<?php echo $html_root; ?>/pss/modificar" title="Editar o Ver el Registro"><i class="fa fa-pencil"></i></button>';
          ret += '<button class="btn btn-icon-only red btn-outline" data-toggle="confirmation" data-on-confirm="eliminar_paciente_confirm" data-copy-attributes="data-id" data-id="'+data+'" title="Eliminar el Registro seleccionado?"><i class="fa fa-trash-o"></i></button>';
          <?php } ?>
          return ret;
        }
      }],
    }).on('draw', function() {
      App.initConfirmation();
    });
  });  
</script>
