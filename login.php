<?php require_once("db.php"); ?>
<!DOCTYPE html>
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
    <link href="<?php echo $html_root; ?>/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $html_root; ?>/assets/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $html_root; ?>/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $html_root; ?>/assets/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $html_root; ?>/assets/css/components-md.min.css" rel="stylesheet" id="style_components" type="text/css" />
    <link href="<?php echo $html_root; ?>/assets/css/plugins-md.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $html_root; ?>/assets/css/login.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $html_root; ?>/assets/css/fonts.css" rel="stylesheet" type="text/css" />
    <link rel="shortcut icon" href="favicon.png" />
  </head>
  <body class="login">
    <div class="logo">
      <!--<a href="index.html">
      <img src="<?php echo $html_root; ?>/assets/img/logo_trans1.png" alt="" /> </a>-->
    </div>
    <div class="content">
      <form class="login-form" action="<?php echo $html_root; ?>/" method="post">
        <h3 class="form-title">Ingreso al sistema</h3>
        <div class="alert alert-danger display-hide">
          <button class="close" data-close="alert"></button>
          <span> Ingrese su usuario y contrase&ntilde;a. </span>
        </div>
        <?php if ( $_GET['log']=='err_log'){?>
          <div class="alert alert-danger">
            <button class="close" data-close="alert"></button>
            <span> Error en su usuario o contrase&ntilde;a. </span>
          </div>
        <?php }?>
        <div class="form-group">
          <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
          <label class="control-label visible-ie8 visible-ie9">Usuario</label>
          <div class="input-icon">
            <i class="fa fa-user"></i>
            <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Usuario" name="username" /> 
          </div>
        </div>
        <div class="form-group">
          <label class="control-label visible-ie8 visible-ie9">Contrase&ntilde;a</label>
          <div class="input-icon">
            <i class="fa fa-lock"></i>
            <input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="Contrase&ntilde;a" name="password" />
          </div>
        </div>
        <div class="form-actions">
          <button type="submit" class="btn green"> Ingresar </button>
        </div>
      </form>
    </div>
    <div class="copyright"><?php echo date("Y"); ?> &copy; Servicio de Consultas de Padrones.</div>
    <!--[if lt IE 9]>
    <script src="<?php echo $html_root; ?>/assets/plugins/respond.min.js"></script>
    <script src="<?php echo $html_root; ?>/assets/plugins/excanvas.min.js"></script> 
    <![endif]-->
    <script src="<?php echo $html_root; ?>/assets/plugins/jquery.min.js" type="text/javascript"></script>
    <script src="<?php echo $html_root; ?>/assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="<?php echo $html_root; ?>/assets/plugins/js.cookie.min.js" type="text/javascript"></script>
    <script src="<?php echo $html_root; ?>/assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <script src="<?php echo $html_root; ?>/assets/plugins/jquery.blockui.min.js" type="text/javascript"></script>
    <script src="<?php echo $html_root; ?>/assets/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
    <script src="<?php echo $html_root; ?>/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
    <script src="<?php echo $html_root; ?>/assets/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
    <script src="<?php echo $html_root; ?>/assets/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
    <script src="<?php echo $html_root; ?>/assets/plugins/backstretch/jquery.backstretch.min.js" type="text/javascript"></script>
    <script src="<?php echo $html_root; ?>/assets/js/app.min.js" type="text/javascript"></script>
    <script src="<?php echo $html_root; ?>/assets/js/login.min.js" type="text/javascript"></script>
  </body>
</html>