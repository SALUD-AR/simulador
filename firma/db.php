<?php

/*******************************************
 ** Configuración de la base de datos.
 *******************************************/

$db_type = 'postgres8';       // Tipo de base de datos.
$db_host = '192.168.6.181';   // Host para desarrollo.
$db_user = 'postgres';        // Usuario.
$db_password = 'Cache8080';      // Contraseña.
$db_name = 'simulador';

define("DB_HOST", $db_host);  
define("DB_USER", $db_user);  
define("DB_PASSWORD", $db_paswword);  
define("DB_NAME", $db_name);  

$html_root = '/firma';

$html_firma = 'https://tst.firmar.gob.ar/';
$html_RA = 'RA/';
$html_firmador = 'firmador/';
$http_token_key = 'afasdfasdfasdfasdfasdfsdafasdf';
$http_secret = '12a1213132132123132121231';
$http_cuil_firmador = ''; // Deberia estar ligado a un usuario logueado en el sistema
$http_urlRedirect = 'http://localhost/simulador/?firma=1';

$ADODB_CACHE_DIR = LIB_DIR."/adodb/cache";    // Directorio para cache de consultas
// Arreglo que contiene los nombres de los esquemas en la
// base de datos para poder acceder a las tablas sin tener
// que usar en nombre del esquema.

$db_debug = FALSE; 
?>