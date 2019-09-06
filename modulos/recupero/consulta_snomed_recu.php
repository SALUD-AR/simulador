<?php require_once(dirname(__FILE__)."/../../config.php"); ?>

<div class="portlet light portlet-fit calendar">
  <div class="portlet-title">
    <div class="caption">
      <i class="fa fa-list-alt font-green"></i>
      <span class="caption-subject font-green sbold uppercase">Mapeo de Codigos</span>
    </div> 
  </div>
  
  <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-cogs"></i>Ingreso de Datos
            </div>
            
            <div class="tools">
                <a href="javascript:;" class="collapse" data-original-title="" title=""> </a>
                <a href="javascript:;" class="reload" data-original-title="" title=""> </a>
            </div>
        </div>                                

        <div class="portlet-body flip-scroll col-sm-12">
          <div class="portlet-body flip-scroll col-sm-12">
              <label for="procedimientos"><label class="text-danger">(*)</label> Procedimiento</label>
              <select id="proc" class="busca_snomed form-control"></select>                                  
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
          <input type="button" class="btn green-sharp btn-outline  btn-block" value="Consultar" name="consultar" id="consultar" onclick="return consultar ()" />
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
    var proc=$("#proc").select2("val");    

    if (!proc){
      toastr.error('Debe completar los campos!');
      return 0;
    }

    $('#res_pss').html("");
    $('#res_pss').html('<div class="loading">Un momento, por favor...</div>');

    var url = '<?php echo encode_link($html_root."/recupero/listado_recu_datos", array("accion" => "busca_snomed_res")); ?>';
    $.post(url, {
      proc: proc
    },
    function(data) {
      $('#res_pss').html(data);
    }
    ).fail(function() {
      toastr.error('Error al Buscar Nomenclador Nacional!');
    });
    return 0;
}

</script>
