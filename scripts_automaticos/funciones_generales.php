<?

define(LIB_DIR, dirname(__FILE__)."/../lib");                          // Librerias del sistema
require_once(LIB_DIR."/adodb/adodb.inc.php");
require_once(LIB_DIR."/adodb/adodb-pager.inc.php");


$db_type = 'postgres8';       // Tipo de base de datos.
$db_host = 'localhost';   // Host para desarrollo.
$db_user = 'postgres';        // Usuario.
$db_password = 'cache8080';      // Contraseña.
$db_name = 'padron-fact';

$db = &ADONewConnection($db_type) or die("Error al conectar a la base de datos");
$db->Connect($db_host, $db_user, $db_password, $db_name);
$db->cacheSecs = 3600;
?>