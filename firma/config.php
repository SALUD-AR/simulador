<?php

/*******************************************
 ** Constantes para usar en los include/require
 ** (Directorios relativos al sistema)
 *******************************************/

define("ROOT_DIR", dirname(__FILE__));      // Directorio raiz
define("LIB_DIR", ROOT_DIR."/lib");       // Librerias del sistema
//define("MOD_DIR", ROOT_DIR."/modulos");     // Modulos del sistema
//define("UPLOADS_DIR", ROOT_DIR."/uploads");   // Directorio para uploads

date_default_timezone_set("America/Argentina/Buenos_Aires");

require_once(ROOT_DIR."/db.php");

$ADODB_CACHE_DIR = LIB_DIR."/adodb/cache";    // Directorio para cache de consultas
// Arreglo que contiene los nombres de los esquemas en la
// base de datos para poder acceder a las tablas sin tener
// que usar en nombre del esquema.
$db_schemas = array(  
  "general",
  "administracion",
  "permisos"
);

$db_debug = FALSE;          // Debugger de las consultas.

?>