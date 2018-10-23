<?php

/*******************************************
 ** Configuración de la base de datos.
 *******************************************/

$db_type = 'postgres8';       // Tipo de base de datos.
$db_host = 'localhost';   // Host para desarrollo.
$db_user = 'postgres';        // Usuario.
$db_password = 'cache8080';      // Contraseña.
$db_name = 'simulador';

$html_root = '/simulador';

$ADODB_CACHE_DIR = LIB_DIR."/adodb/cache";    // Directorio para cache de consultas
// Arreglo que contiene los nombres de los esquemas en la
// base de datos para poder acceder a las tablas sin tener
// que usar en nombre del esquema.

$db_debug = FALSE; 
?>