<?php
    require_once(dirname(__FILE__)."/../../config.php"); 
    require_once("funciones.php");

    $firma  = trim($_GET["firma"]);
?>

<div class="portlet box blue-hoki">
  <div class="portlet-title">
      <div class="caption">
          <i class="fa fa-list-alt"></i> Listado de envio de firmas </div>
		  
		<div class="tools">
			<a href="javascript:;" class="collapse" data-original-title="" title=""> </a>
			<a href="javascript:;" class="reload" data-original-title="" title=""> </a>
		</div>
      
  </div>
  <div class="portlet-body">
    <div class="table-responsive">  
      <table id="listado-firmas" class="table table-striped table-condensed" width="100%">
        <thead>
          <tr>
            <th>ID</th>
            <th>Fecha</th>
            <th>Paciente</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <a href="/" class="close">x</a>
				<h4 class="modal-title">Estado de Env&iacute;o de Firma</h4>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                
				<a href="/" class="btn btn-default"> Cerrar </a>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    var tabla = null;
    var firma='<?php echo $firma?>';
    
    $(document).ready(function() {

        //setInterval(cargar_datos, 10000); //llamo a la funcion cada 10 segundos para visualizar los cambios de estado de las firmas
        cargar_datos();

        function cargar_datos(){

            //$('#listado-firmas').dataTable().fnDestroy();// Destruyo para volver a hacer el render

            var url = '<?php echo encode_link($html_root."/pss/listado_datos", array("accion" => "listado_firmas")); ?>';
            tabla = $('#listado-firmas').DataTable({
				"language": {
				"emptyTable":			"No hay datos disponibles en la tabla.",
				"info":		   		"Del _START_ al _END_ de _TOTAL_ ",
				"infoEmpty":			"Mostrando 0 registros de un total de 0.",
				"infoFiltered":			"(filtrados de un total de _MAX_ registros)",
				"infoPostFix":			"(registros)",
				"lengthMenu":			"Mostrar _MENU_ registros",
				"loadingRecords":		"Cargando...",
				"processing":			"Procesando...",
				"search":			"Buscar:",
				"searchPlaceholder":		"Firma a buscar",
				"zeroRecords":			"No se han encontrado coincidencias.",
				"paginate": {
					"first":			"Primera",
					"last":				"Última",
					"next":				"Siguiente",
					"previous":			"Anterior"
				},
				"aria": {
					"sortAscending":	"Ordenación ascendente",
					"sortDescending":	"Ordenación descendente"
				}
			},
			"lengthMenu":		[[5, 10, 20, 25, 50, -1], [5, 10, 20, 25, 50, "Todos"]],
			"iDisplayLength":	10,
                ajax: url,
                order: [[ 1, 'asc' ], [ 2, 'asc' ]],
                columns: [
                { data: "id", className: 'link', width: "5%" },
                { data: "fecha", className: 'link', width: "10%" },
                { data: "paciente", className: 'link', width: "20%" },
                { data: "status", className: 'text-center link', width: "10%" },
                { data: "id", searchable: false, orderable: false, className: 'text-center', width: "15%" }
                ],
                columnDefs: [ {
                targets: -1,
                render: function ( data, type, full, meta ) {
                    var ret = '';
                    <?php if (permiso_check('/pss/firma', "editar")) { ?>
                    <?php $url_edit = encode_link($html_root."/pss/listado_datos", array("accion" => "ver_firma")); ?>
                    ret += '<button href="#popup_modal" class="btn btn-icon-only yellow btn-outline" data-toggle="modal" data-id="'+data+'" data-url="<?php echo $url_edit; ?>" title="Ver Firma">';
                    ret += '<i class="fa fa-pencil" data-tooltip="true" title="Ver Firma"></i>';
                    ret += '</button>';
                    <?php } ?>
                    return ret;
                }
                }],
                createdRow: function( row, data, dataIndex ) {
					if ( data.status=='Firmado') {
                        $('td', row).eq(3).addClass("success");
                    } else {
                        $('td', row).eq(3).addClass("warning");
                    }
                    $(row).prop('data-id', data.id);
                }
            });
        }

        if (firma=='nothing'||firma==null||firma==''){
            
        } else {

            <?php if (permiso_check('/pss/firma', "editar")) { ?>
                
                toastr.success('Envio de firma exitoso!', 'Por favor espere para visualizar el resultado');
                
				var url = '<?php echo encode_link($html_root."/pss/listado_datos", array("accion" => "ver_firma_callback")); ?>';
				
				setTimeout(cargar_modal, 3000); 
				
            <?php } ?>
            
        }
		
		function cargar_modal(){
			
			$('.modal-body').load(url,{id: firma},function(){
				$('#myModal').modal({show:true});
			});	
				
		}
    
    });  

</script>






