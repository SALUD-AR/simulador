<h3 class="page-title">Inicio</h3>
<div class="row">
  <div class="col-md-6 col-sm-6">
    <div class="portlet box green">
      <div class="portlet-title">
        <div class="caption">
          <i class="icon-calendar"></i>Turnos</div>
        <div class="tools">
          <a href="javascript:;" class="collapse" data-original-title="" title=""> </a>
          <a href="" class="fullscreen" data-original-title="" title=""> </a>
        </div>
      </div>
      <div class="portlet-body">
        <div id="calendario-inicio"></div>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-sm-6">
    <div class="portlet box green">
      <div class="portlet-title">
        <div class="caption">
          <i class="icon-list"></i>Detalles de Turnos</div>
        <div class="tools">
          <a href="javascript:;" class="collapse" data-original-title="" title=""> </a>
          <a href="" class="fullscreen" data-original-title="" title=""> </a>
        </div>
      </div>
      <div class="portlet-body">
        <div id="calendario-inicio-detalles"></div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript" src="<?php echo $html_root; ?>/assets/js/calendario.js"></script>
<script type="text/javascript">
$(function() {
  CalendarioTurnos.init({
    selector: '#calendario-inicio',
    url_turnos: '<?php echo encode_link($html_root."/turnos/agenda_datos", array("accion" => "agenda_events")); ?>',
    url_agendas: '<?php echo encode_link($html_root."/configuracion/agendas_datos", array("accion" => "agendas_disponibles")); ?>'
  });
});
</script>