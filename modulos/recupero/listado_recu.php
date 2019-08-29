<?php require_once(dirname(__FILE__)."/../../config.php"); ?>
<div class="portlet light portlet-fit calendar">
  <div class="portlet-title">
    <div class="caption">
      <i class="fa fa-list-alt font-green"></i>
      <span class="caption-subject font-green sbold uppercase">Listado de Conceptos</span>
    </div>    
  </div>
  <div class="portlet-body">
    <div class="table-responsive">  
      <table id="listado-pss" class="table table-striped table-condensed" width="100%">
        <thead>
          <tr>
            <th>ID</th>
            <th>Descripcion</th>
            <th>SNOMED CT</th>            
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
    var url = '<?php echo encode_link($html_root."/recupero/listado_recu_datos", array("accion" => "listado")); ?>';
    tabla = $('#listado-pss').DataTable({
      ajax: url,
      order: [[ 1, 'asc' ], [ 2, 'asc' ]],
      columns: [
        { data: "id", className: 'link', width: "10%" },
        { data: "descri", className: 'link', width: "45%" },
        { data: "snomed", className: 'link', width: "45%" }
      ],      
    }).on('draw', function() {
      App.initConfirmation();
    });
  });  
</script>
