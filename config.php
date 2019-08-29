<?php

/*******************************************
 ** Constantes para usar en los include/require
 ** (Directorios relativos al sistema)
 *******************************************/

define("ROOT_DIR", dirname(__FILE__));      // Directorio raiz
define("LIB_DIR", ROOT_DIR."/lib");       // Librerias del sistema
define("MOD_DIR", ROOT_DIR."/modulos");     // Modulos del sistema
define("UPLOADS_DIR", ROOT_DIR."/uploads");   // Directorio para uploads

date_default_timezone_set("America/Argentina/Buenos_Aires");

require_once(ROOT_DIR."/db.php");

/*******************************************
 ** Headers para que el explorador no guarde
 ** las páginas en cache.
 *******************************************/

header("Cache-control: no-cache");
header("Expires: ".gmdate("D, d M Y H:i:s")." GMT");

require_once LIB_DIR."/Browser.php";
$browser = new Browser();
if( $browser->isBrowser("Chrome") ||
  ($browser->isBrowser("Internet Explorer") && $browser->getVersion() >= 11)
  )
{
  define("BROWSER_OK", true);
}
else
{
  define("BROWSER_OK", false);
}

$ADODB_CACHE_DIR = LIB_DIR."/adodb/cache";    // Directorio para cache de consultas
// Arreglo que contiene los nombres de los esquemas en la
// base de datos para poder acceder a las tablas sin tener
// que usar en nombre del esquema.
$db_schemas = array(  
  "general",
  "administracion",
  "permisos",
  "firma"
);
$db_debug = FALSE;          // Debugger de las consultas.

/*******************************************
 ** Limite de tiempo de inactividad para la
 ** expiración de la sesión (en minutos).
 *******************************************/
 $session_timeout = 90;

/*******************************************
 ** Libreria principal del sistema.
 *******************************************/
require LIB_DIR."/lib.php";
?>