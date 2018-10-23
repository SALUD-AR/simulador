<?
require_once "config.php";
// require_once("./modulos/permisos/permisos.class.php");

// Cargar el menu viejo
// $old_menu = BROWSER_OK ? false : true;
if (is_array($parametros) && array_key_exists('mode', $parametros)) {
  if ($parametros['mode'] == "logout") {
    $usuario->destroy();
    // phpss_logout();
    header("Location: ${html_root}/login.php");
    die();
  }

  // if ($parametros['mode'] == "debug") {
  //    if (permisos_check("inicio","debug")) {
  //     $_ses_user["debug"] = $parametros["debug_status"];
  //     phpss_svars_set("_ses_user", $_ses_user);
  //   }
  // }
}

$id_usuario = $usuario->id;

?><!DOCTYPE html>
<!--[if IE 8]> <html lang="es" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="es" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="es">
  <!--<![endif]-->
  <head>
    <meta charset="utf-8" />
    <title>Consulta de Codigos</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta content="" name="description" />
    <meta content="" name="author" />
    <link rel="shortcut icon" href="<?php echo $html_root; ?>/favicon.png">
    <link href="<?php echo $html_root; ?>/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $html_root; ?>/assets/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $html_root; ?>/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $html_root; ?>/assets/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $html_root; ?>/assets/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $html_root; ?>/assets/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $html_root; ?>/assets/plugins/bootstrap-toastr/toastr.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $html_root; ?>/assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $html_root; ?>/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $html_root; ?>/assets/plugins/fullcalendar/fullcalendar.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $html_root; ?>/assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $html_root; ?>/assets/css/components-md.min.css" rel="stylesheet" id="style_components" type="text/css" />
    <link href="<?php echo $html_root; ?>/assets/css/plugins-md.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $html_root; ?>/assets/css/layout.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $html_root; ?>/assets/css/themes/blue.min.css" rel="stylesheet" type="text/css" id="style_color" />
    <link href="<?php echo $html_root; ?>/assets/css/custom.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $html_root; ?>/assets/css/fonts.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $html_root; ?>/assets/css/general.css" rel="stylesheet" type="text/css" />
    <script src="<?php echo $html_root; ?>/assets/plugins/jquery.min.js" type="text/javascript"></script>
    <script type="text/javascript">
      var html_root = '<?php echo $html_root; ?>';
      var url_menu_consultorios = '<?php echo encode_link($html_root."/configuracion/consultorios_datos", array("accion" => "cambiar_consultorio")); ?>';
    </script>
  </head>

  <body class="page-header-fixed page-sidebar-closed-hide-logo page-container-bg-solid page-md">
    <!-- BEGIN HEADER -->
    <div class="page-header navbar navbar-fixed-top">
      <!-- BEGIN HEADER INNER -->
      <div class="page-header-inner ">
        <!-- BEGIN LOGO -->
        <div class="page-logo">
          <img src="<?php echo $html_root; ?>/assets/img/logo_solo_trans.png" alt="logo" class="logo-default" />            
          <div class="menu-toggler sidebar-toggler">
            <!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
          </div>
        </div>
        <!-- END LOGO -->
        <!-- BEGIN RESPONSIVE MENU TOGGLER -->
        <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse"> </a>
        <!-- END RESPONSIVE MENU TOGGLER -->
        <!-- BEGIN PAGE ACTIONS -->
        <!-- DOC: Remove "hide" class to enable the page header actions -->
        <div class="page-actions">
          <img src="<?php echo $html_root; ?>/assets/img/logo_cab_index.png" alt="logo" class="logo-default" />
        </div>
        <!-- END PAGE ACTIONS -->
        <!-- BEGIN PAGE TOP -->
        <div class="page-top">
          <!-- BEGIN TOP NAVIGATION MENU -->
          <div class="top-menu">
            <ul class="nav navbar-nav pull-right">
              <!-- BEGIN NOTIFICATION DROPDOWN -->
              <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
              
              <!-- END NOTIFICATION DROPDOWN -->
              <!-- BEGIN USER LOGIN DROPDOWN -->
              <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->

              <li class="dropdown dropdown-user">
                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                  <img alt="" class="img-circle" src="<?php echo $html_root; ?>/assets/img/avatar.png" />
                  <span class="username username-hide-on-mobile"> <?php echo $usuario->apellido.', '.$usuario->nombre; ?> </span>
                  <i class="fa fa-angle-down"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-default">                  
                  <li>
                    <a href="<?php echo encode_link($html_root."/index.php",array("mode" => "logout")); ?>"><i class="icon-key"></i> Salir </a>
                  </li>
                </ul>
              </li>
              <!-- END USER LOGIN DROPDOWN -->
            </ul>
          </div>
          <!-- END TOP NAVIGATION MENU -->
        </div>
        <!-- END PAGE TOP -->
      </div>
      <!-- END HEADER INNER -->
    </div>
    <!-- END HEADER -->
    <!-- BEGIN HEADER & CONTENT DIVIDER -->
    <div class="clearfix"> </div>
    <!-- END HEADER & CONTENT DIVIDER -->

    <!-- BEGIN CONTAINER -->
    <div class="page-container">
      <!-- BEGIN SIDEBAR -->
      <div class="page-sidebar-wrapper">
        <!-- END SIDEBAR -->
        <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
        <!-- DOC: Change data-auto-speed="200" to adjust the sub menu slide up/down speed -->
        <div class="page-sidebar navbar-collapse collapse">
          <!-- BEGIN SIDEBAR MENU -->
          <!-- DOC: Apply "page-sidebar-menu-light" class right after "page-sidebar-menu" to enable light sidebar menu style(without borders) -->
          <!-- DOC: Apply "page-sidebar-menu-hover-submenu" class right after "page-sidebar-menu" to enable hoverable(hover vs accordion) sub menu mode -->
          <!-- DOC: Apply "page-sidebar-menu-closed" class right after "page-sidebar-menu" to collapse("page-sidebar-closed" class must be applied to the body element) the sidebar sub menu mode -->
          <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
          <!-- DOC: Set data-keep-expand="true" to keep the submenues expanded -->
          <!-- DOC: Set data-auto-speed="200" to adjust the sub menu slide up/down speed -->
        <ul class="page-sidebar-menu page-header-fixed" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="500">          
            <li class="nav-item start active open">
              <a href="<?php echo $html_root; ?>/pss/listado" class="nav-link nav-toggle ajaxify">
                <i class="icon-bulb"></i>
                <span class="title">Simulador Codigos PSS</span>
              </a>              
            </li>

            <li class="nav-item start">
              <a href="<?php echo $html_root; ?>/pss/consulta_snomed" class="nav-link nav-toggle ajaxify">
                <i class="icon-bulb"></i>
                <span class="title">Simulador Codigos SNOMED</span>
              </a>              
            </li>

            <li class="nav-item start">
              <a href="<?php echo $html_root; ?>/pss/listado_pss" class="nav-link nav-toggle ajaxify">
                <i class="icon-puzzle"></i> 
                <span class="title">Listado PSS</span>
              </a>             
            </li>
            
            <li class="nav-item">
              <a href="javascript:;" class="nav-link nav-toggle">
                <i class="icon-wrench"></i>
                <span class="title">Sistema</span>
                <span class="arrow"></span>
              </a>
              <ul class="sub-menu">
                <li class="nav-item start">
                  <a href="<?php echo $html_root; ?>/sistema/usuarios" class="nav-link ajax-load" data-titulo="<i class='icon-users'></i> <span>Usuarios</span>"><i class="icon-users"></i> <span class="title">Usuarios</span></a>
                </li>
                <li class="nav-item start">
                  <a href="<?php echo $html_root; ?>/sistema/mensajes" class="nav-link ajax-load" data-titulo="<i class='fa fa-commenting-o'></i> <span>Mensajes</span>"><i class="fa fa-commenting-o"></i> <span class="title">Mensajes</span></a>
                </li>
              </ul>
            </li>
          </ul>
          <!-- END SIDEBAR MENU -->
        </div>
        <!-- END SIDEBAR -->
      </div>
      <!-- END SIDEBAR -->
      <!-- BEGIN CONTENT -->
      <div class="page-content-wrapper">
        <!-- BEGIN CONTENT BODY -->
        <div class="page-content">
          <!-- BEGIN PAGE HEADER-->
          <div class="pull-right"><img src="<?php echo $html_root; ?>/assets/img/ajax-loading.gif" id="loading-indicator" style="display:none" /></div>
          <div class="page-content-body">
            <?php include(MOD_DIR."/pss/listado.php"); ?>
          </div>
          <!-- END PAGE HEADER-->
        </div>
        <!-- END CONTENT BODY -->
      </div>
      <!-- END CONTENT -->
    </div>
    <!-- END CONTAINER -->
    <!-- BEGIN FOOTER -->
    <div class="page-footer">
      <div class="page-footer-inner">Copyright &copy; <?php echo date("Y"); ?></div>
      <div class="scroll-to-top"><i class="icon-arrow-up"></i></div>
    </div>
    <!-- END FOOTER -->
    <div id="popup_modal" class="modal fade">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"></h4>
          </div>
          <div class="modal-body"></div>
          <div class="modal-footer">
            <button type="button" class="btn btn-success" id="popup_modal_aceptar">Aceptar</a>
            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="delete_modal" tabindex="-1" role="dialog" aria-labelledby="delete_modal_label" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="delete_modal_label">Confirmar la eliminaci&oacute;n</h4>
          </div>
          <div class="modal-body">
            <p>&iquest;Est&aacute; seguro que desea eliminar el elemento seleccionado?</p>
          </div>
          <div class="modal-footer">
            <button class="btn btn-success btn-borrar">Aceptar</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
          </div>
        </div>
      </div>
    </div>
    <!--[if lt IE 9]>
    <script src="<?php echo $html_root; ?>/assets/plugins/respond.min.js"></script>
    <script src="<?php echo $html_root; ?>/assets/plugins/excanvas.min.js"></script>
    <![endif]-->
    <!-- BEGIN CORE PLUGINS -->
    <script src="<?php echo $html_root; ?>/assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="<?php echo $html_root; ?>/assets/plugins/js.cookie.min.js" type="text/javascript"></script>
    <script src="<?php echo $html_root; ?>/assets/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
    <script src="<?php echo $html_root; ?>/assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <script src="<?php echo $html_root; ?>/assets/plugins/jquery.blockui.min.js" type="text/javascript"></script>
    <script src="<?php echo $html_root; ?>/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
    <script src="<?php echo $html_root; ?>/assets/plugins/bootstrap-confirmation/bootstrap-confirmation.min.js" type="text/javascript"></script>
    <!-- END CORE PLUGINS -->
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <script src="<?php echo $html_root; ?>/assets/plugins/bootstrap-toastr/toastr.min.js" type="text/javascript"></script>
    <script src="<?php echo $html_root; ?>/assets/plugins/bootstrap-typeahead/bootstrap3-typeahead.min.js" type="text/javascript"></script>
    <script src="<?php echo $html_root; ?>/assets/plugins/datatables/datatables.min.js" type="text/javascript"></script>
script>
    <script src="<?php echo $html_root; ?>/assets/plugins/moment.min.js" type="text/javascript"></script>
    <script src="<?php echo $html_root; ?>/assets/plugins/bootstrap-daterangepicker/daterangepicker.min.js" type="text/javascript"></script>
    <script src="<?php echo $html_root; ?>/assets/plugins/ion.rangeslider/js/ion.rangeSlider.min.js" type="text/javascript"></script>
    <script src="<?php echo $html_root; ?>/assets/plugins/select2/js/select2.full.js" type="text/javascript"></script>
    <script src="<?php echo $html_root; ?>/assets/plugins/select2/js/i18n/es.js" type="text/javascript"></script>
    <script src="<?php echo $html_root; ?>/assets/plugins/fullcalendar/fullcalendar.min.js" type="text/javascript"></script>
    <script src="<?php echo $html_root; ?>/assets/plugins/fullcalendar/lang/es.js" type="text/javascript"></script>
    <script src="<?php echo $html_root; ?>/assets/plugins/autosize/autosize.min.js" type="text/javascript"></script>
    <script src="<?php echo $html_root; ?>/assets/plugins/icheck/icheck.min.js" type="text/javascript"></script>
    <script src="<?php echo $html_root; ?>/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="<?php echo $html_root; ?>/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="<?php echo $html_root; ?>/assets/plugins/bootstrap-datetimepicker/js/locales/bootstrap-datetimepicker.es.js" type="text/javascript"></script>
    <script src="<?php echo $html_root; ?>/assets/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js" type="text/javascript"></script>
    <script src="<?php echo $html_root; ?>/assets/plugins/bootstrap-datepaginator/bootstrap-datepaginator.min.js" type="text/javascript"></script>
    <script src="<?php echo $html_root; ?>/assets/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js" type="text/javascript"></script>
    <script src="<?php echo $html_root; ?>/assets/plugins/fancybox/source/jquery.fancybox.js" type="text/javascript"></script>
    <script src="<?php echo $html_root; ?>/assets/plugins/fine-uploader/jquery.fine-uploader.js" type="text/javascript"></script>
    <script src="<?php echo $html_root; ?>/assets/plugins/bootbox/bootbox.min.js" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script src="<?php echo $html_root; ?>/assets/js/app.js" type="text/javascript"></script>
    <!-- END THEME GLOBAL SCRIPTS -->
    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <script src="<?php echo $html_root; ?>/assets/js/layout.js" type="text/javascript"></script>
    <!-- <script src="<?php echo $html_root; ?>/assets/js/demo.min.js" type="text/javascript"></script> -->
    <!-- <script src="<?php echo $html_root; ?>/assets/js/quick-sidebar.min.js" type="text/javascript"></script> -->
    <!-- END THEME LAYOUT SCRIPTS -->
    <script src="<?php echo $html_root; ?>/assets/js/general.js" type="text/javascript"></script>
    <script type="text/javascript">
      var url_reloj = '<?php echo encode_link($html_root."/configuracion/agendas_datos", array("accion" => "reloj_turno")); ?>';
      var url_mensajes = '<?php echo encode_link($html_root."/sistema/mensajes_datos", array("accion" => "mensajes_nuevos")); ?>';
      var url_consultorios_form = '<?php echo encode_link($html_root."/configuracion/consultorios_datos", array("accion" => "guardar_consultorio")); ?>';
      var turno_activo = 0;

      $(window).load(function() {
        if (typeof initCalendar == 'function') {
          initCalendar();
        }
      });
    </script>
    <?php // var_dump($usuario); ?>
  </body>
</html>
