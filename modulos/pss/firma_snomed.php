<?php require_once(dirname(__FILE__)."/../../config.php"); 
require_once("funciones.php");

?>

<div class="portlet light portlet-fit calendar">
  <div class="portlet-title">
    <div class="caption">
      <i class="fa fa-list-alt font-green"></i>
      <span class="caption-subject font-green sbold uppercase">Firma Digital</span>
    </div> 
  </div>
  
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-cogs"></i>Ingreso de Datos del paciente
            </div>
            
            <div class="tools">
                <a href="javascript:;" class="collapse" data-original-title="" title=""> </a>
                <a href="javascript:;" class="reload" data-original-title="" title=""> </a>
            </div>
			
        </div>

        <div class="portlet-body flip-scroll col-sm-12">
            <div class="portlet-body flip-scroll col-sm-12">
                <label for="nrodoc"><label class="text-danger">(*)</label> N&uacute;mero de Documento</label>
                <input type="number" id="nrodoc" class="form-control">
            </div>
        </div>

        <div class="portlet-body flip-scroll col-sm-12">
            <div class="portlet-body flip-scroll col-sm-6">
                <label for="nombre"><label class="text-danger">(*)</label> Nombres</label>
                <input type="text" id="nombre" class="form-control">
            </div>
            <div class="portlet-body flip-scroll col-sm-6">
                <label for="apellido"><label class="text-danger">(*)</label> Apellido</label>
                <input type="text" id="apellido" class="form-control">
            </div>
        </div>
		
		<div class="portlet-body flip-scroll col-sm-12">
            <div class="portlet-body flip-scroll col-sm-6">
            <label for="sexo"> <label class="text-danger">(*)</label> Sexo</label>
            <select name="sexo" id="sexo" class="form-control">
                <option value="M"<?php echo ($sexo == 'M' ? 'selected="selected"' : ''); ?>>Masculino</option>
                <option value="F"<?php echo ($sexo == 'F' ? 'selected="selected"' : ''); ?>>Femenino</option>
            </select>
            </div>
			
			<div class="portlet-body flip-scroll col-sm-6">
                <label for="diag"><label class="text-danger">(*)</label> Diagnostico</label>
                <select id="diag" class="busca_snomed form-control"></select>
            </div>
		</div>
		
        <div class="portlet-body flip-scroll col-sm-12">
            <div class="portlet-body flip-scroll col-sm-12">
                <label for="obs"><label class="text-danger">(*)</label> Evoluci&oacute;n</label>
                <textarea id="obs" class="form-control"></textarea>
            </div>
        </div>

        <div class="portlet-body flip-scroll col-sm-12">
            <div class="portlet-body flip-scroll col-sm-6">
                <span class="label label-danger">(*) Criterios de Busqueda y Requeridos</span>                                      
            </div>
            <div class="portlet-body flip-scroll col-sm-6">                                      
            </div>
        </div>
        
        
        <div class="portlet-body flip-scroll">
            <input type="button" class="btn green-sharp btn-outline btn-block" value="Firmar" name="consultar" id="consultar" onclick="return consultar ()" />
        </div>
        <div class="portlet-body flip-scroll">
            <div id="res_pss" class="portlet-body flip-scroll">
            </div>
        </div>
    </div>
</div>
 
<script type="text/javascript">

$(document).ready(function() {
    $('.js-example-basic-multiple').select2()

    $(".busca_snomed").select2({
        ajax: {
          url: '<?php echo encode_link($html_root."/pss/listado_datos", array("accion" => "busca_sn")); ?>',
          dataType: 'json',
          delay: 250,
          data: function (params) {
            return {
              q: params.term, // search term
              page: params.page
            };
          },
          processResults: function (data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
              results: data.items,
              /*pagination: {
                more: (params.page * 30) < data.total_count
              }*/
            };
          },
          cache: true
        },
        placeholder: 'Buscar en SNOMED',
        escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
        minimumInputLength: 4,
        templateResult: formatRepo,
        templateSelection: formatRepoSelection
    });

});

function formatRepo (repo) {
  if (repo.loading) {
    return repo.text;
  }
  var markup =  "<div class='select2-result-repository clearfix'>" +                  
                  "<div class='select2-result-repository__meta'>" +
                    "<div class='select2-result-repository__title'>" + repo.term + "</div>"+
                    "<div class='select2-result-repository__statistics'>" +
                      "<div class='select2-result-repository__forks'><i class='fa fa-flash'></i> " + repo.id + " ConceptID</div>" +
                      "<div class='select2-result-repository__stargazers'><i class='fa fa-star'></i> " + repo.id_tabla + " ID</div>" +
                    "</div>" +
                  "</div>"+
                "</div>";
  return markup;
}

function formatRepoSelection (repo) {
  return repo.term || repo.text;
}

function consultar() {
    var nrodoc=$("#nrodoc").val();
    var nombre=$("#nombre").val();
    var apellido=$("#apellido").val();
    var diag=$("#diag").select2("val");
    var obs=$("#obs").val();
    var sexo=$('#sexo').val();
    var grupo_etareo=$('#grupo_etareo').val();

    if (!diag || !nrodoc || !nombre || !apellido || !obs){
      toastr.error('Debe completar los campos!');
      return 0;
    }

    var url = '<?php echo encode_link($html_root."/pss/listado_datos", array("accion" => "firmar_snomed_res")); ?>';
    $.post(url, {
      nrodoc: nrodoc,
      nombre: nombre,
      apellido: apellido,
      diag: diag,
      sexo: sexo,
      obs: obs
    },
    function(data) {
      $('#res_pss').html(data);
    }
    ).fail(function() {
      toastr.error('Error!');
    });
    return 0;
}

</script>
