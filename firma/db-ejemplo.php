<?php

/*******************************************
 ** Configuración de la base de datos.
 *******************************************/

$db_type = 'postgres8';       // Tipo de base de datos.
$db_host = 'localhost';   // Host para desarrollo.
$db_user = 'postgres';        // Usuario.
$db_password = 'password';      // Contraseña.
$db_name = 'simulador';

define("DB_HOST", "$db_host");  
define("DB_USER", $db_user);  
define("DB_PASSWORD", $db_paswword);  
define("DB_NAME", $db_name);  

/*******************************************
 ** Configuración Directorio Raiz.
 *******************************************/

$html_root = '/firma';

/*******************************************
 ** Configuración Firma Digital.
 *******************************************/

$html_firma = 'https://tst.firmar.gob.ar/';
$html_RA = 'RA/';
$html_firmador = 'firmador/';
$http_token_key = 'AFDAFASFAKZXCKLZJZJXVJZX45454545';
$http_secret = '12345566676788889967643623525423';
$http_cuil_firmador = 'XX-XXXXXXXX-X'; // Deberia estar ligado a un usuario logueado en el sistema
$http_urlRedirect = 'http://localhost/simulador/?firma=1';


$ADODB_CACHE_DIR = LIB_DIR."/adodb/cache";    // Directorio para cache de consultas
// Arreglo que contiene los nombres de los esquemas en la
// base de datos para poder acceder a las tablas sin tener
// que usar en nombre del esquema.

$db_debug = FALSE; 
?>