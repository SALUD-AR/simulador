<?php

/*******************************************
 ** Configuración de la base de datos.
 *******************************************/

$db_type = 'postgres8';             // Tipo de base de datos.
$db_host = 'localhost';             // Host para desarrollo.
$db_user = 'historia_clinica';      // Usuario.
$db_password = 'historia_clinica';  // Contraseña.
$db_name = 'historia_clinica';

/*******************************************
 ** Configuración Directorio Raiz.
 *******************************************/

$html_root = '';

/*******************************************
 ** Configuración Firma Digital.
 *******************************************/

$html_firma = 'https://tst.firmar.gob.ar/'; // Web Service Firma Digital
$html_RA = 'RA/';
$html_firmador = 'firmador/';
$html_api ='api/signatures';
$http_token_key = 'AFDAFASFAKZXCKLZJZJXVJZX45454545'; // Token provisto por firmar.gob.ar para la aplicacion
$http_secret = '12345566676788889967643623525423';    // secret provisto por firmar.gob.ar para la aplicacion
$http_cuil_firmador = 'XX-XXXXXXXX-X'; // esta ligado a un usuario logueado en el sistema
$http_urlRedirect = 'http://localhost/simulador/?firma=1';

?>
