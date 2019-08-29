<?php
require_once(LIB_DIR."/adodb/adodb.inc.php");
require_once(LIB_DIR."/adodb/adodb-pager.inc.php");
require_once(LIB_DIR."/class.phpmailer.php");

require_once(LIB_DIR."/general.php");

// ini_set('display_errors',1);
// ini_set('display_startup_errors',1);
// error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

define("TIEMPO_INICIO", getmicrotime());

// Chequea la version del sistema operativo en el que se esta
// ejecutando la pagina y define la constante SERVER_OS
if (preg_match("/Win32/i", $_SERVER["SERVER_SOFTWARE"]) ||
    preg_match("/Microsoft/i", $_SERVER["SERVER_SOFTWARE"]))
	define("SERVER_OS", "windows");
else
	define("SERVER_OS", "linux");

$db = ADONewConnection($db_type) or die("Error al conectar a la base de datos");
$db->Connect($db_host, $db_user, $db_password, $db_name);
$db->cacheSecs = 3600;
$result=$db->Execute("SET search_path=".join(",",$db_schemas)) or die($db->ErrorMsg());
unset($result);
$db->debug = $db_debug;

use Phalcon\Acl;
use Phalcon\Acl\Role;
use Phalcon\Acl\Resource;
use Phalcon\Acl\Adapter\Memory as AclList;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Di;
use Phalcon\Session\Adapter\Files as Session;
use Phalcon\Session\Bag as SessionBag;

$di = new Di();
$di->set('session', function () {
  $session = new Session(
    array(
      'uniqueId' => 'simulador'
    )
  );
  session_name('simulador');
  $session->start();
  return $session;
});

$usuario = new SessionBag('usuario');
$usuario->setDI($di);

// unlink("acl.data");
if (!is_file(ROOT_DIR."/acl.data")) {
  $acl = new AclList();

  $acl->setDefaultAction(Acl::DENY);

  $roles = array(
    'Sistema'         => new Role('Sistema', 'Administrador de Sistema'),
    'Consulta'          => new Role('Consulta', 'Consulta al Sistema'),
  );

  $recursos = array(
    '/pss/listado'                 => array('ver', 'editar'),
	'/pss/listado_pss'             => array('ver', 'editar'),
	'/pss/firma'			       => array('ver', 'editar'),
	'/pss/firmas'			       => array('ver', 'editar'),
	'/pss/firma_snomed'		       => array('ver', 'editar'),
    '/pss/agregar'                 => array('ver', 'editar'),
    '/pss/informe'                 => array('ver', 'editar'),
    '/pss/modificar'               => array('ver', 'editar'),
    '/pss/consulta_snomed'         => array('ver', 'editar'),
    '/recupero/listado_recu'       => array('ver', 'editar'),
    '/recupero/listado_recu_datos' => array('ver', 'editar'),
    '/recupero/consulta_snomed_recu' => array('ver', 'editar'),
    '/sistema/usuarios'            => array('ver', 'editar'),
    '/sistema/mensajes'            => array('ver', 'editar'),
  );

  foreach ($roles as $rol) {
    $acl->addRole($rol);
  }

  foreach ($recursos as $recurso => $acciones) {
    $acl->addResource(new Resource($recurso), $acciones);
    $acl->allow("Sistema", $recurso, "*");
  }

  $acl->allow("Consulta", "/pss/listado", "ver");
  $acl->allow("Consulta", "/pss/listado_pss", "ver");

  $acl->allow("Consulta", "/pss/informe", "ver");
  $acl->allow("Consulta", "/pss/consulta_snomed", "ver");
  $acl->allow("Consulta", "/recupero/listado_recu", "ver");
  $acl->allow("Consulta", "/recupero/listado_recu_datos", "ver");
  $acl->allow("Consulta", "/recupero/consulta_snomed_recu", "ver");
  $acl->allow("Consulta", "/sistema/mensajes", "ver");
  
  // Firma Digital 
  $acl->allow("Consulta", "/pss/firma", "ver");
  $acl->allow("Consulta", "/pss/firmas", "ver");
  $acl->allow("Consulta", "/pss/firma_snomed", "ver");
  // Firma Digital

  file_put_contents(ROOT_DIR."/acl.data", serialize($acl));
} else {
  $acl = unserialize(file_get_contents(ROOT_DIR."/acl.data"));
}

function permiso_check($recurso, $permiso) {
  global $acl, $usuario;
  return $acl->isAllowed($usuario->tipo, $recurso, $permiso);
}
/***********************************
 ** Funciones de ambito general
 ***********************************/

function getmicrotime() {
	list($useg, $seg) = explode(" ",microtime());
	return ((float)$useg + (float)$seg);
}
// Funcion que devuelve el tiempo que se demora en generarse la pagina
function tiempo_de_carga () {
	$tiempo_fin = getmicrotime();
	$tiempo = sprintf('%.4f', $tiempo_fin - TIEMPO_INICIO);
	return $tiempo;
}



function db_tipo_res($tipo="d") {
	global $db;
	switch ($tipo) {
	   case "a":   // tipo asociativo
		   $db->SetFetchMode(ADODB_FETCH_ASSOC);
		   break;
	   case "n":   // tipo numerico
		   $db->SetFetchMode(ADODB_FETCH_NUM);
		   break;
	   case "d":
		   $db->SetFetchMode(ADODB_FETCH_BOTH);
		   break;
   }
}

/*
 * Funcion para cambiar un color por otro alternativo
 * cuando los colores son parecidos o no contrastan mucho.
 * los parametros son de la forma: #ffffff
*/
function contraste($fondo, $frente, $reemplazo) {
	$brillo = 125;
   $diferencia = 400;
	$bg = ereg_replace("#","",$fondo);
	$fg = ereg_replace("#","",$frente);
	$bg_r = hexdec(substr($bg,0,2));
	$bg_g = hexdec(substr($bg,2,2));
	$bg_b = hexdec(substr($bg,4,2));
	$fg_r = hexdec(substr($fg,0,2));
	$fg_g = hexdec(substr($fg,2,2));
	$fg_b = hexdec(substr($fg,4,2));
	$bri_bg = (($bg_r * 299) + ($bg_g * 587) + ($bg_b * 114)) / 1000;
	$bri_fg = (($fg_r * 299) + ($fg_g * 587) + ($fg_b * 114)) / 1000;
	$dif = max(($fg_r - $bg_r),($bg_r - $bg_r)) + max(($fg_g - $bg_g),($bg_g - $fg_g)) + max(($fg_b - $bg_b),($bg_b - $fg_b));
	if(intval($bri_bg - $bri_fg) > $brillo or $dif > $diferencia) {
   	return $frente;
   }
   else {
   	return $reemplazo;
   }
}

function Error($msg,$num="") {
	global $error;
	echo "<center><font size=4 color=#FF0000>Error $num: $msg</font><br></center>\n";
	$error = 1;
}

function link_calendario($control_pos) {
	global $html_root;
	return "<img src=$html_root/imagenes/cal.gif border=0 align=middle style='cursor:hand;' alt='Haga click aqui para\nseleccionar la fecha'  onClick=\"javascript:popUpCalendar(this, $control_pos, 'dd/mm/yyyy');\">";
}

function Aviso($msg) {
	echo "<br><center><font size=4><b>$msg</b></font></center><br>\n";
}

/**
 * @return string
 * @param fecha_db string
 * @desc Convierte una fecha de la forma AAAA-MM-DD
 *       a la forma DD/MM/AAAA
 */
function Fecha($fecha_db) {
		$m = substr($fecha_db,5,2);
		$d = substr($fecha_db,8,2);
		$a = substr($fecha_db,0,4);
		if (is_numeric($d) && is_numeric($m) && is_numeric($a)) {
				return "$d/$m/$a";
		}
		else {
				return "";
		}
}
//funcion que devuelve la diferencia en dias entre dos fechas
//hay que pasar las fechas a la funcion en la forma dd/mm/aaaa
function restaFechas($dFecIni, $dFecFin)
{
    $dFecIni = str_replace("/","",$dFecIni);
    $dFecFin = str_replace("/","",$dFecFin);

    ereg( "([0-9]{1,2})([0-9]{1,2})([0-9]{2,4})", $dFecIni, $aFecIni);
    ereg( "([0-9]{1,2})([0-9]{1,2})([0-9]{2,4})", $dFecFin, $aFecFin);

    $date1 = mktime(0,0,0,$aFecIni[2], $aFecIni[1], $aFecIni[3]);
    $date2 = mktime(0,0,0,$aFecFin[2], $aFecFin[1], $aFecFin[3]);

    return round(($date2 - $date1) / (60 * 60 * 24));
}

function hora_ok($hora) {
    if ($hora) {
         $hora_arr = explode(":", $hora);
         if ( (is_numeric($hora_arr[0])) && ($hora_arr[0]>=0 && $hora_arr[0]<=23))
             $hora_apertura = $hora_arr[0];
         else
             return 0;
         if ( (is_numeric($hora_arr[1]))  && ($hora_arr[1]>=0 && $hora_arr[1]<=59) )
            $hora_apertura .= ":".$hora_arr[1];
        else
            return 0;
        if ( (is_numeric($hora_arr[2]))  && ($hora_arr[2]>=0 && $hora_arr[2]<=59))
            $hora_apertura .= ":".$hora_arr[2];
        else
           return 0;
    }

return $hora_apertura;

}


function Hora($hora_db) {
	if (ereg("([0-9]{2}:[0-9]{2}:[0-9]{2})",$hora_db,$hora))
		return $hora[0];
	else
		return "00:00:00";
}



/**
 * @return string
 * @param fecha string
 * @desc Convierte una fecha de la forma DD/MM/AAAA
 *       a la forma AAAA-MM-DD
 */

//funcion defectuosa
//cuidado
function Fecha_db($fecha) {
	if (strstr($fecha,"/"))
		list($d,$m,$a) = explode("/",$fecha);
	elseif (strstr($fecha,"-"))
		list($d,$m,$a) = explode("-",$fecha);
	else
		return "";
return "$a-$m-$d";
}




/**
 * @return 1 o 0
 * @param fecha date
 * @desc Devuelve 1 si es fecha y 0 si no lo es.
 */
function FechaOk($fecha) {
	if (ereg("-",$fecha))
		list($dia,$mes,$anio)=split("-", $fecha);
	elseif (ereg("/",$fecha))
		list($dia,$mes,$anio)=split("/", $fecha);
	else
		return 0;
	return checkdate($mes,$dia,$anio);
}

/**
 * @return date
 * @param fecha date
 * @desc Convierte una fecha del formato dd-mm-aaaa al
 *       formato aaaa-mm-dd que usa la base de datos.
 */
function ConvFecha($fecha) {
	list($dia,$mes,$anio)=split("-", $fecha);
	return "$anio-$mes-$dia";
}

/**
 * @return int
 * @param fecha date
 * @desc Compara la fecha $fecha con la fecha actual.
 *       Retorna:
 *               0 si $fecha es mayor de 7 dias.
 *               1 si $fecha esta entre 0 y 7 dias.
 *               2 si $fecha es anterior a la fecha actual.
 */
function check_fecha($fecha) {
	$fecha2=strtotime($fecha);
	$num1=($fecha2-intval(time()))/60/60/24;
//    $res=0;
	if ($num1 > 7) {
	   $res=0;
    } elseif ($num1>=0 and $num1<=7) {
       $res=1;
    } else {
	   $res=2;
    }
	return($res);
}
// Manejo de div flotantes
/**
 * @Nombre inicio_barra
 * @param nombre String
 * @param titulo String
 * @param contenido String
 * @param color String
 * @param top integer
 * @param left integer
 * @param height integer
 * @param width integer
 * @param ocultar integer 0 o 1
 * @desc Inserta un div flotante
 *		 Si el top y left no son insertado,
 *		 El div flotante estara en la posicion
 *		 inferior central.
 **/
function inicio_barra($nombre,$titulo,$contenido,$height,$width,$top=null,$left=null,$color="#B7C7D0",$ocultar=1) {
	$he=$height-18;
	echo "<style type='text/css'>
		<!--
		#$nombre	{position: absolute;overflow: hidden; width: $width; height: $height;
			border: 2 outset black; margin: 5px;}
		#title		{background: #006699;padding: 0px; margin: 0px;}
		#inner		{background: $color;border: 2 inset white;overflow: auto; margin: 0px;width: 100%; height: $he;}
		-->
	</style>\n";


	echo "<div id='$nombre'>\n";
	echo "<div class='handle' handlefor='$nombre' id='title'>\n";
	echo "<table width=100% cellspacing=0 cellpadding=0 border=0>\n";
	echo "<tr>\n";
	echo "<td align=center width=90%>\n";
	echo "<font size=2 color='#cdcdcd'><b>$titulo</b></font>\n";
	echo "</td>\n";
	echo "<td align=right width=30%>\n";
	if ($ocultar==1) {
		echo "<img style='cursor: hand;' src='../../imagenes/dropdown2.gif' onClick='ocultar(this,\"$nombre\");'>\n";
		echo "<img style='cursor: hand;' src='../../imagenes/salir.gif' onClick='mini(this,\"$nombre\");'>\n";
	}
	echo "</td></tr></table></div>\n";
	echo "<div id='inner'";
	if ($color) echo " bgcolor=$color";
	echo ">\n";
	echo $contenido;
	echo "</div></div>\n";
	echo "<script>\n";
	//echo "$nombre.style.width=$width;\n";
	//echo "$nombre.style.height=$height;\n";
	if ($top==""){
		echo "$nombre.style.top=(document.body.clientHeight-$height)-5;\n";
		echo "$nombre.top=(document.body.clientHeight)-((document.body.clientHeight-$height)-5);\n";
	}
	else {
		echo "$nombre.style.top=$top;\n";
		echo "$nombre.top=(document.body.clientHeight-$top);\n";
	}
	if ($left=="")
		echo "$nombre.style.left=((document.body.clientWidth/2)-($width/2));\n";
	else
		echo "$nombre.style.left=$left;\n";
	//echo "alert($nombre.style.top);\n";
	echo "</script>\n";
}
// Fin de div flotantes
function html_out($outstr){
  $string=$outstr;
  if ($string <> "") {
	$string=ereg_replace("\"","&#34;",$string);
	$string=ereg_replace("'","&#39;",$string);
	$string=ereg_replace(">","&#62;",$string);
	$string=ereg_replace("<","&#60;",$string);
	$string=ereg_replace("\n","<br>",$string);
  }
  return $string;
}

function formato_money($num) {
	return number_format($num, 2, ',', '.');
}

function es_numero(&$num) {
	if (strstr($num,",")) {
		$num = ereg_replace("\.","",$num);
		$num = ereg_replace(",",".",$num);
	}
	return is_numeric($num);
}

function mkdirs($strPath, $mode = "0700") {
//	global $server_os;
	if (SERVER_OS == "windows") {
		$strPath = ereg_replace("/","\\",$strPath);
	}
	if (is_dir($strPath)) return true;
	$pStrPath = dirname($strPath);
	if (!mkdirs($pStrPath, $mode)) return false;
	return mkdir($strPath);
}

function cortar($text, $maxChars = 30, $splitter = '...') {
	$theReturn = $text;
	$lastSpace = false;

	// only do the rest if we're over the character limit
	if (strlen($text) > $maxChars)
	{
		$theReturn = substr($text, 0, $maxChars - 1);
		// add closing punctuation back in if found
		if (in_array(substr($text, $maxChars - 1, 1), array(' ', '.', '!', '?')))
		{
			$theReturn .= substr($text, $maxChars, 1);
		}
		else
		{
			// make room for splitter string and look for truncated words
			$theReturn = substr($theReturn, 0, $maxChars - strlen($splitter));
			$lastSpace = strrpos($theReturn, ' ');
			// Remove truncated words and trailing spaces
			if ($lastSpace !== false)
			{
				$theReturn = substr($theReturn, 0, $lastSpace);
			}
			// Remove trailing commas (add more array elements as desired)
			if (in_array(substr($theReturn, -1, 1), array(',')))
			{
				$theReturn = substr($theReturn, 0, -1);
			}
			// append the splitter string
			$theReturn .= $splitter;
		}
	}
	// all done!
	return $theReturn;
}

function cortar2($text, $maxChars = 30, $splitter = '...', $last = 0) {
	$theReturn = $text;

	// only do the rest if we're over the character limit
	if (strlen($text) > $maxChars)
	{
		if ($last)
			$theReturn = $splitter.substr($text, -$maxChars, $maxChars - 1);
		else
			$theReturn = substr($text, 0, $maxChars - 1).$splitter;
	}
	// all done!
	return $theReturn;
}



//toma una letra y un string como parametros y devuelve
//el numero de ocurrencias de es letra en ese string
function str_count_letra($letra,$string) {
 $largo=strlen($string);
 $counter=0;
 for($i=0;$i<$largo;$i++)
 {
  if($string[$i]==$letra)
   $counter++;
 }
 return $counter;

}

/**********************************************************************
FUNCION QUE ORDENA UN ARREGLO BIDIMENSIONAL POR EL CAMPO $campo
DE LA SEGUNDA DIMENSON DEL ARREGLO
@bi_array    El arreglo a ordenar
$campo       El campo de la segunda dimension del arreglo por el cual
			 se ordenara el mismo
$tipo_campo  Este parametro se pone con la palabra string,
			 si el $campo es de tipo string
**********************************************************************/
function qsort_second_dimension($bi_array,$campo,$tipo_campo=0)
{
	$i=0;
 $tam=sizeof($bi_array);
 while($i<$tam)
 {$j=$i+1;
  if($tipo_campo=="string")
   $i_item=$bi_array[$i][$campo];
  else
   $i_item=intval($bi_array[$i][$campo]);
  while($j<$tam)
   {
   	if($tipo_campo=="string")
   	{ $j_item=$bi_array[$j][$campo];
   	  if(strcmp($i_item,$j_item)>0)
      {$temp=$bi_array[$i];
       $bi_array[$i]=$bi_array[$j];
       $bi_array[$j]=$temp;
       $j=$tam;
       $i--;
      }
      else
       $j++;
   	}
   	else
   	{
   	  $j_item=intval($bi_array[$j][$campo]);
   	  if($i_item>$j_item)
      {$temp=$bi_array[$i];
       $bi_array[$i]=$bi_array[$j];
       $bi_array[$j]=$temp;
       $j=$tam;
        $i--;
      }
      else
       $j++;
   	}

   }//de while($j<$tam)
   $i++;
 }//de while($i<$tam)
 return $bi_array;
}//de function qsort_second_dimension($bi_array,$campo,$string=0)



/*********************************************************************************
function insertar_string($cadena,$str, $limite)
Proposito:
          Inserta en $cadena, el string $str cada $limite caracteres.

variables utilizadas:
          - $longitud = contador para la longitud de $cadena
          - $tok = division en palabras de $cadena.
          - $palabra = variable utilizada para armar nuevamente $cadena
          - $string = cadena retornada por la funcion es $cadena con $str insertado $limite
          veces.

Logica:
         La funcion recorre $cadena separando a dicha cadena en palabras con la ayuda
         de la funcion strtok().
         Si la longitud de las palabras procesadas hasta el momento supera a $limite entonces
         se concatena al final de dicha palabra $str y se resetea el contador de longitud.
         antes de procesar la proxima palabra se concatena en $string las palabras procesadas
         hasta el momento.

NOTA: funcion implementada para utilizarse en el modulo licitaciones, en pagina
      funciones.php.
**********************************************************************************/
function insertar_string($cadena,$str, $limite){
$longitud=0;
    $tok = strtok ($cadena," ");
    while ($tok) {
        $longitud+=strlen($tok);
        $palabra=$tok;
        $tok = strtok (" ");
        if($longitud>$limite) {$palabra.=$str;$longitud=0;}
        $string.=" ".$palabra;
    }
    return $string;
}
//final de insertar_string


/********************************************************************************
 Funcion que ajusta el texto pasado como parametro en $texto, agregando 'enters'
 donde corresponda para que cada linea de $texto no supere la cantidad de maxima
 de caracteres que se especifican en el parametro $max_long.
*********************************************************************************/
function ajustar_lineas_texto($texto,$max_long)
{
 //tomamos la longitud de la cadena
 $long_texto=strlen($texto);
 $texto_resultado="";
 $contador=0;
 for($i=0;$i<$long_texto;$i++)
 {
  if($texto[$i]=="\r" && $texto[$i+1]=="\n")
  {
   $contador=0;
  }
  else if($contador==$max_long)
  {
   $texto_resultado.="\n";
   $contador=0;
  }
  else
  {
   $contador++;
  }
  $texto_resultado.=$texto[$i];

 }

 return $texto_resultado;
}

function compara_fechas($fecha1, $fecha2) {
	if ($fecha1) {
		$fecha1 = strtotime($fecha1);
	}
	else {
		$fecha1 = 0;
	}
	if ($fecha2) {
		$fecha2 = strtotime($fecha2);
	}
	else {
		$fecha2 = 0;
	}
    if ($fecha1 > $fecha2) return 1;
    elseif ($fecha1 == $fecha2) return 0;
    else return -1; //fecha2 > fecha1
}

function diferencia_dias($fecha1,$fecha2,$h=0) {
 $dif_dias=0;
 $fecha_aux=$fecha1;
 $fecha_hasta=$fecha2;
 if ($h) {
         $hora=date("H");
         $minutos=date("i");
         $segundos=date("s");
        while(compara_fechas($fecha_aux,$fecha_hasta)==-1) //mientras la fecha2 sea mayor que la 1
         {
          $fecha_split=split("/",fecha($fecha_aux));
          $dif_dias++;
          $fecha_aux=date("Y-m-d H:i:s",mktime($hora,$minutos,$segundos,$fecha_split[1],$fecha_split[0]+1,$fecha_split[2]));
         }

} //del if
else
   {
   $fecha_hasta=fecha_db($fecha_hasta);
   while(compara_fechas(fecha_db($fecha_aux),$fecha_hasta)==-1) //mientras la fecha2 sea mayor que la 1
    {
     $fecha_split=split("/",$fecha_aux);
     $dif_dias++;
     $fecha_aux=date("d/m/Y",mktime(12,0,0,$fecha_split[1],$fecha_split[0]+1,$fecha_split[2]));
    }
   }

 return $dif_dias;

}//de la funcion dia habiles


///adaptacion de la funcion mariela en php
//funcion que  me convierte de numero a letra copia de la funcion de mariela
function Centenas($VCentena) {
$Numeros[0] = "cero";
$Numeros[1] = "uno";
$Numeros[2] = "dos";
$Numeros[3] = "tres";
$Numeros[4] = "cuatro";
$Numeros[5] = "cinco";
$Numeros[6] = "seis";
$Numeros[7] = "siete";
$Numeros[8] = "ocho";
$Numeros[9] = "nueve";
$Numeros[10] = "diez";
$Numeros[11] = "once";
$Numeros[12] = "doce";
$Numeros[13] = "trece";
$Numeros[14] = "catorce";
$Numeros[15] = "quince";
$Numeros[20] = "veinte";
$Numeros[30] = "treinta";
$Numeros[40] = "cuarenta";
$Numeros[50] = "cincuenta";
$Numeros[60] = "sesenta";
$Numeros[70] = "setenta";
$Numeros[80] = "ochenta";
$Numeros[90] = "noventa";
$Numeros[100] = "ciento";
$Numeros[101] = "quinientos";
$Numeros[102] = "setecientos";
$Numeros[103] = "novecientos";
If ($VCentena == 1) { return $Numeros[100]; }
Else If ($VCentena == 5) { return $Numeros[101];}
Else If ($VCentena == 7 ) {return ( $Numeros[102]); }
Else If ($VCentena == 9) {return ($Numeros[103]);}
Else {return $Numeros[$VCentena];}

}
function Unidades($VUnidad) {
$Numeros[0] = "cero";
$Numeros[1] = "uno";
$Numeros[2] = "dos";
$Numeros[3] = "tres";
$Numeros[4] = "cuatro";
$Numeros[5] = "cinco";
$Numeros[6] = "seis";
$Numeros[7] = "siete";
$Numeros[8] = "ocho";
$Numeros[9] = "nueve";
$Numeros[10] = "diez";
$Numeros[11] = "once";
$Numeros[12] = "doce";
$Numeros[13] = "trece";
$Numeros[14] = "catorce";
$Numeros[15] = "quince";
$Numeros[20] = "veinte";
$Numeros[30] = "treinta";
$Numeros[40] = "cuarenta";
$Numeros[50] = "cincuenta";
$Numeros[60] = "sesenta";
$Numeros[70] = "setenta";
$Numeros[80] = "ochenta";
$Numeros[90] = "noventa";
$Numeros[100] = "ciento";
$Numeros[101] = "quinientos";
$Numeros[102] = "setecientos";
$Numeros[103] = "novecientos";
$tempo=$Numeros[$VUnidad];
return $tempo;
}

function Decenas($VDecena) {
$Numeros[0] = "cero";
$Numeros[1] = "uno";
$Numeros[2] = "dos";
$Numeros[3] = "tres";
$Numeros[4] = "cuatro";
$Numeros[5] = "cinco";
$Numeros[6] = "seis";
$Numeros[7] = "siete";
$Numeros[8] = "ocho";
$Numeros[9] = "nueve";
$Numeros[10] = "diez";
$Numeros[11] = "once";
$Numeros[12] = "doce";
$Numeros[13] = "trece";
$Numeros[14] = "catorce";
$Numeros[15] = "quince";
$Numeros[20] = "veinte";
$Numeros[30] = "treinta";
$Numeros[40] = "cuarenta";
$Numeros[50] = "cincuenta";
$Numeros[60] = "sesenta";
$Numeros[70] = "setenta";
$Numeros[80] = "ochenta";
$Numeros[90] = "noventa";
$Numeros[100] = "ciento";
$Numeros[101] = "quinientos";
$Numeros[102] = "setecientos";
$Numeros[103] = "novecientos";
$tempo = ($Numeros[$VDecena]);
return $tempo;
}





function NumerosALetras($Numero){


list($Numero, $Decimales) = split("[,.]",$Numero);

$Numero = intval($Numero);
$Decimales = intval($Decimales);
$letras = "";

while ($Numero != 0){

// '*---> Validación si se pasa de 100 millones

If ($Numero >= 1000000000) {
$letras = "Error en Conversión a Letras";
$Numero = 0;
$Decimales = 0;
}

// '*---> Centenas de Millón
If (($Numero < 1000000000) And ($Numero >= 100000000)){
If ((Intval($Numero / 100000000) == 1) And (($Numero - (Intval($Numero / 100000000) * 100000000)) < 1000000)){
$letras .= (string) "cien millones ";
}
Else {
$letras = $letras & Centenas(Intval($Numero / 100000000));
If ((Intval($Numero / 100000000) <> 1) And (Intval($Numero / 100000000) <> 5) And (Intval($Numero / 100000000) <> 7) And (Intval($Numero / 100000000) <> 9)) {
$letras .= (string) "cientos ";
}
Else {
$letras .= (string) " ";
}
}
$Numero = $Numero - (Intval($Numero / 100000000) * 100000000);
}

// '*---> Decenas de Millón
If (($Numero < 100000000) And ($Numero >= 10000000)) {
If (Intval($Numero / 1000000) < 16) {
$tempo = Decenas(Intval($Numero / 1000000));
$letras .= (string) $tempo;
$letras .= (string) " millones ";
$Numero = $Numero - (Intval($Numero / 1000000) * 1000000);
}
Else {
$letras = $letras & Decenas(Intval($Numero / 10000000) * 10);
$Numero = $Numero - (Intval($Numero / 10000000) * 10000000);
If ($Numero > 1000000) {
$letras .= $letras & " y ";
}
}
}

// '*---> Unidades de Millón
If (($Numero < 10000000) And ($Numero >= 1000000)) {
$tempo=(Intval($Numero / 1000000));
If ($tempo == 1) {
$letras .= (string) " un millón ";
}
Else {
$tempo= Unidades(Intval($Numero / 1000000));
$letras .= (string) $tempo;
$letras .= (string) " millones ";
}
$Numero = $Numero - (Intval($Numero / 1000000) * 1000000);
}

// '*---> Centenas de Millar
If (($Numero < 1000000) And ($Numero >= 100000)) {
$tempo=(Intval($Numero / 100000));
$tempo2=($Numero - ($tempo * 100000));
If (($tempo == 1) And ($tempo2 < 1000)) {
$letras .= (string) "cien mil ";
}
Else {
$tempo=Centenas(Intval($Numero / 100000));
$letras .= (string) $tempo;
$tempo=(Intval($Numero / 100000));
If (($tempo <> 1) And ($tempo <> 5) And ($tempo <> 7) And ($tempo <> 9)) {
$letras .= (string) "cientos ";
}
Else {
$letras .= (string) " ";
}
}
$Numero = $Numero - (Intval($Numero / 100000) * 100000);
}

// '*---> Decenas de Millar
If (($Numero < 100000) And ($Numero >= 10000)) {
$tempo= (Intval($Numero / 1000));
If ($tempo < 16) {
$tempo = Decenas(Intval($Numero / 1000));
$letras .= (string) $tempo;
$letras .= (string) " mil ";
$Numero = $Numero - (Intval($Numero / 1000) * 1000);
}
Else {
$tempo = Decenas(Intval($Numero / 10000) * 10);
$letras .= (string) $tempo;
$Numero = $Numero - (Intval(($Numero / 10000)) * 10000);
If ($Numero > 1000) {
	$rest = substr($letras, -6);
    if ($rest!='veinte'){
	    $resto = substr($letras, -4);
 	    if ($resto!='diez')
           $letras .=(string) " y ";
    }
   if($rest=='veinte') {
      $letras= substr($letras,0, -1);
  	  $letras.='i';
    }
    if ($resto=='diez') {
    	$letras=substr($letras,0, -1);
        $letras.= 'ci';
    }

}
Else {
$letras .= (string) " mil ";

}
}
}


// '*---> Unidades de Millar
If (($Numero < 10000) And ($Numero >= 1000)) {
$tempo=(Intval($Numero / 1000));
If ($tempo == 1) {
$letras .= (string) "un";
}
Else {
$tempo = Unidades(Intval($Numero / 1000));
$letras .= (string) $tempo;
}
$letras .= (string) " mil ";
$Numero = $Numero - (Intval($Numero / 1000) * 1000);
}

// '*---> Centenas
If (($Numero < 1000) And ($Numero > 99)) {
If ((Intval($Numero / 100) == 1) And (($Numero - (Intval($Numero / 100) * 100)) < 1)) {
//$letras = $letras & "cien ";
$letras.="cien";
}
Else {
$temp=(Intval($Numero / 100));
$l2=Centenas($temp);
$letras .= (string) $l2;
If ((Intval($Numero / 100) <> 1) And (Intval($Numero / 100) <> 5) And (Intval($Numero / 100) <> 7) And (Intval($Numero / 100) <> 9)) {
$letras .= "cientos ";
}
Else {
$letras .= (string) " ";
}
}

$Numero = $Numero - (Intval($Numero / 100) * 100);

}

// '*---> Decenas
If (($Numero < 100) And ($Numero > 9) ) {
If ($Numero < 16 ) {
$tempo = Decenas(Intval($Numero));
$letras .= $tempo;
$Numero = $Numero - Intval($Numero);
}
Else {
$tempo= Decenas(Intval(($Numero / 10)) * 10);
$letras .= (string) $tempo;
$Numero = $Numero - (Intval(($Numero / 10)) * 10);
If ($Numero > 0.99) {

	$rest = substr($letras, -6);
   	if ($rest!='veinte'){
	    $resto = substr($letras, -4);
 	    if ($resto!='diez')
           $letras .=(string) " y ";
    }

   if($rest=='veinte') {
   	  $resto="";
      $letras= substr($letras,0, -1);
  	  $letras.='i';
  	}
    if ($resto=='diez') {
       $letras=substr($letras,0, -1);
 	   $letras.= 'ci';
    }

}
}
}

// '*---> Unidades
If (($Numero < 10) And ($Numero > 0.99)) {
$tempo=Unidades(Intval($Numero));
$letras .= (string) $tempo;

$Numero = $Numero - Intval($Numero);
}


// '*---> Decimales
If ($Decimales > 0) {
	If (($letras <> "Error en Conversión a Letras") And (strlen(Trim($letras)) > 0)) {
		$letras .= (string) " con ".$Decimales."/100";
	}
}
Else {
	If (($letras <> "Error en Conversión a Letras") And (strlen(Trim($letras)) > 0)) {
		$letras .= (string) " ";
	}
}
return $letras;
}
}


function firma_mail(){
	$confiden="\n\nNOTA DE CONFIDENCIALIDAD\n";
	$confiden.="Este mensaje (y sus anexos) es confidencial generado automaticamente, esta dirigido exclusivamente a ";
	$confiden.="las personas direccionadas en el mail, puede contener información de ";
	$confiden.="propiedad exclusiva y/o amparada por el secreto profesional.\n";
	$confiden.="El acceso no autorizado, uso, reproducción, o divulgación esta prohibido.\n";
	return $confiden;
}

function encabezado_mail(){
	$confiden="SISTEMA NOTIFICACIONES\n\n";	
	return $confiden;
}




function FileUpload($TempFile, $FileSize, $FileName, $FileType, $MaxSize, $Path, $ErrorFunction, $ExtsOk, $ForceFilename, $OverwriteOk,$comprimir=1,$mostrar_carteles=1) {
	global $ID,$id_archivo;
	//global $ID,$_ses_user_name,$id_archivo;
	$retorno["error"] = 0;
	if (strlen($ForceFilename)) { $FileName = $ForceFilename; }
	//$err=`mkdir -p '$Path'`;
	mkdirs (enable_path($Path));

	if (!function_exists($ErrorFunction)) {
		if (!function_exists('DoFileUploadDefErrorHandle')) {
			function DoFileUploadDefErrorHandle($ErrorNumber, $ErrorText) {
				echo "<tr><td colspan=2 align=center><font color=red><b>Error $ErrorNumber: $ErrorText</b></font><br><br></td></tr>";
			}
		}
		$ErrorFunction = 'DoFileUploadDefErrorHandle';
	}
        if($mostrar_carteles)
	{echo "<tr><td>Nombre:</td><td>$FileName</td></tr>\n";
	 echo "<tr><td>Tamaño:</td><td>$FileSize</td></tr>\n";
	 echo "<tr><td>Tipo MIME:</td><td>$FileType</td></tr>\n";
	}
	if($TempFile == 'none' || $TempFile == '') {
		$ErrorTxt = "No se especificó el nombre del archivo<br>";
		$ErrorTxt .= "o el archivo excede el máximo de tamaño de:<br>";
		$ErrorTxt .= ($MaxSize / 1024)." Kb.";
		$retorno["error"] = 1;
		$ErrorFunction($retorno["error"], $ErrorTxt);
		return $retorno;
	}

	if(!is_uploaded_file($TempFile)) {
		$ErrorTxt = "File Upload Attack, Filename: \"$FileName\"";
		$retorno["error"] = 2;
		$ErrorFunction($retorno["error"], $ErrorTxt);
		return $retorno;
	}

	if($FileSize == 0) {
		$ErrorTxt = 'El archivo que ha intentado subir, está vacio!';
		$retorno["error"] = 3;
		$ErrorFunction($retorno["error"], $ErrorTxt);
		return $retorno;
	}

	if($FileSize > $MaxSize) {
		$ErrorTxt = 'El archivo que ha intentado subir excede el máximo de ' . ($MaxSize / 1024) . 'kb.';
		$retorno["error"] = 5;
		$ErrorFunction($retorno["error"], $ErrorTxt);
		return $retorno;
	}

	$FileNameFull = enable_path($Path."/".$FileName);
	$FileNameFullComp = substr($FileNameFull,0,strlen($FileNameFull) - strpos(strrev($FileNameFull),".") - 1).".zip";

	clearstatcache();
	if((file_exists($FileNameFull) || file_exists($FileNameFullComp)) && !strlen($OverwriteOk)) {
		$ErrorTxt = 'El archivo que ha intentado subir ya existe. Por favor especifique un nombre distinto.';
		$retorno["error"] = 6;
		$ErrorFunction($retorno["error"], $ErrorTxt);
		return $retorno;
	}

	move_uploaded_file ($TempFile, $FileNameFull) or die("error al mover el temporal <br> $TempFile <br> hasta <br> $FileNameFull");

	if ($comprimir) {
		$ext = strtolower(GetExt($FileNameFull));
		if ($ext != "zip") {
			$FileNameOld = $FileNameFull;
			$FileNameFull = $FileNameFullComp;
	//			$err = `/bin/pkzip -add -dir=none "$FileNameFull" "$FileNameOld"`;
			if (SERVER_OS == "linux") {
				$err = `/usr/bin/zip -j -9 -q "$FileNameFull" "$FileNameOld"`;
			} elseif (SERVER_OS == "windows"){
				$paso = ROOT_DIR."\\lib\\zip";
				$err = shell_exec("$paso\\zip.exe -j -9 -q  \"$FileNameFull\" \"$FileNameOld\"");

			} else {
				die("Error en compresión.");
			}
			//echo "<br> $TempFile <br> $FileNameFull<br> $FileNameOld<br>";
			unlink($FileNameOld);

			if ($err) {
				$ErrorTxt = "No se pudo comprimir el archivo $FileName";
				$retorno["error"] = 8;
				$ErrorFunction($retorno["error"], $ErrorTxt);
				return $retorno;
			}
		}

		$FileSizeComp=filesize($FileNameFull);
		if($mostrar_carteles)
		 echo "<tr><td>Tamaño comprimido:</td><td>$FileSizeComp</td></tr>\n";
	}
	chmod ($FileNameFull, 0600);

	if (SERVER_OS == "linux") {
		$FileNameComp = substr($FileNameFull,strrpos($FileNameFull,"/") + 1);
	} elseif (SERVER_OS == "windows"){
		$FileNameComp = substr($FileNameFull,strrpos($FileNameFull,"\\") + 1);
	} else {
		die("Error en conocer el sistema operativo.");
	}

	$retorno["filenamecomp"] = $FileNameComp;
	$retorno["filesizecomp"] = $FileSizeComp;


	if($mostrar_carteles)
	 echo "<tr><td colspan=2 align=center><b>Archivo subido correctamente!</b><br><br></td></tr>\n";


	return $retorno;
}

function GetExt($Filename) {
	$RetVal = explode ( '.', $Filename);
	return $RetVal[count($RetVal)-1];
}


/***********************************************************************
FileDownload sirve para bajar archivos, ya sea comprimidos o no

@Comp Sirve para indicar que se quiere bajar el archivo sin descomprimir
************************************************************************/
function FileDownload($Comp, $FileName, $FileNameFull, $FileType, $FileSize, $zipguardado = 1){
     //si $zipguardado es 1 significa que el archivo esta almacenado en servidor como zip

	if ($zipguardado){
		if (($Comp) or (substr($FileName,strrpos($FileName,".")) == ".zip"))
		{
			if (file_exists($FileNameFull))
			{
				Mostrar_Header($FileName,$FileType,$FileSize);
				readfile($FileNameFull);
				exit();
			}
			else
			{
				Mostrar_Error("Se produjo un error al intentar abrir el archivo comprimido");
			}
		}
		else {
			$FileNameFull = substr($FileNameFull,0,strrpos($FileNameFull,"."));

			if(SERVER_OS == "linux")
			{
				$fp = popen("/usr/bin/unzip -p \"$FileNameFull\" 2> /dev/null","r");
			}
			elseif (SERVER_OS == "windows")
			{
			   	$fp = popen(enable_path(LIB_DIR)."\\zip\\unzip.exe -p \"$FileNameFull\"","rb");
		    }

			if (!$fp)
			{
				Mostrar_Error("Se produjo un error al intentar descomprimir el archivo");
			}
			else
			{
				//echo "NAME $FileName - TYPE $FileSize - SIZE $FileSize";
				Mostrar_Header($FileName,$FileType,$FileSize);
				fpassthru($fp);
				pclose($fp);
				exit();
			}
		}
	}
	else //guardado sin comprimir
	{
		if (file_exists($FileNameFull))
		    {
				Mostrar_Header($FileName,$FileType,$FileSize);
				readfile($FileNameFull);
			}
			else
			{
				Mostrar_Error("Se produjo un error al intentar abrir el archivo comprimido");
			}
	}
}

function fin_pagina($debug=true,$mostrar_tiempo=true,$mostrar_consultas=true) {
	global $_ses_user,$debug_datos,$parametros;
	if ($debug and $_ses_user["debug"] == "on") {
		echo "<pre>\$debug_datos=";
		print_r($debug_datos);
		echo "</pre>";
		echo "<pre>\$parametros=";
		print_r($parametros);
		echo "</pre>";
		echo "<pre>\$_GET=";
		print_r($_GET);
		echo "</pre>";
		echo "<pre>\$_POST=";
		print_r($_POST);
		echo "</pre>";
	}
	if ($mostrar_tiempo) {
		echo "Página generada en ".tiempo_de_carga()." segundos.<br>";
	}
	if ($mostrar_consultas) {
		echo "Se utilizaron ".(count($debug_datos))." consulta/s SQL.<br>";
	}
  // die("</body></html>\n");
	die();
}

function nombre_archivo($nombre) {
	$nombre = ereg_replace("[()]","",$nombre);
	$nombre = ereg_replace("[^A-Za-z0-9,.+-]","_",$nombre);
//	$nombre = ereg_replace("['`\"/\()<>]","",$nombre);
	return $nombre;
}


//funcion para corregir el path segun el sistema operativo
function enable_path($paso){
	if (($paso != "") && ((str_count_letra('/',$paso) > 0) || (str_count_letra('\\',$paso) > 0))) {
		if (SERVER_OS == "linux") {
			$ret = str_replace("\\","/",$paso);
		} elseif (SERVER_OS == "windows") {
			$ret = str_replace("/","\\",$paso);
		}
	} else $ret = $paso;

	return $ret;
}

function errorHandler($errno, $errstr, $errfile, $errline, $errcontext) {
	global $_ses_user,$_ultimo_error,$html_root;
	$mostrar = 0;
	switch ($errno) {
		case E_USER_WARNING:
			$tipo_error = "USER_WARNING";
			$mostrar = 0;
			break;
		case E_USER_NOTICE:
			$tipo_error = "USER_NOTICE";
			$mostrar = 1;
			break;
		case E_WARNING:
			$tipo_error = "WARNING";
			$mostrar = 2;
			break;
		case E_NOTICE:
			$tipo_error = "NOTICE";
			$mostrar = 0;
			break;
		case E_CORE_WARNING:
			$tipo_error = "CORE_WARNING";
			$mostrar = 2;
			break;
		case E_COMPILE_WARNING:
			$tipo_error = "COMPILE_WARNING";
			$mostrar = 2;
			break;
		case E_USER_ERROR:
			$tipo_error = "USER_ERROR";
			$mostrar = 0;
			break;
		case E_ERROR:
			$tipo_error = "ERROR";
			$mostrar = 2;
			break;
		case E_PARSE:
			$tipo_error = "PARSE";
			$mostrar = 2;
			break;
		case E_CORE_ERROR:
			$tipo_error = "CORE_ERROR";
			$mostrar = 2;
			break;
		case E_COMPILE_ERROR:
			$tipo_error = "COMPILE_ERROR";
			$mostrar = 2;
			break;
		case 2048:
			$mostrar = 0;
	}
	if ($mostrar == 2) {
		$_ultimo_error[] = $errstr;
	}
	$msg_error = "<table width='50%' height='100%' border=0 align=center cellpadding=0 cellspacing=0>";
	$msg_error .= "<tr><td height='50%'>&nbsp;</td></tr>";
	$msg_error .= "<tr><td align=center>";
	$msg_error .= "<table border=2 width='100%' bordercolor='#FF0000' bgcolor='#FFFFFF' cellpadding=0 cellspacing=0>";
	if ($mostrar == 1) {
		if  ($_SERVER["HTTP_HOST"]=="localhost") {
			$msg_error .= "<tr><td width=15% align=center valign=middle style='border-right:0'>";
			$msg_error .= "<img src=$html_root/imagenes/error.gif alt='ERROR' border=0>";
			$msg_error .= "</td><td width=85% align=center valign=middle style='border-left:0'>";
			$msg_error .= "<font size=2 color=#000000 face='Verdana, Arial, Helvetica, sans-serif'><b>";
			$msg_error .= "SE HA PRODUCIDO UN ERROR EN EL SISTEMA<br>";
			$msg_error .= "El error fue notificado a los programadores y sera solucionado a la brevedad<br>";
			$msg_error .= "</b></font>";
			$msg_error .= "</td></tr>";
		}
		else {
			$msg_error .= "TIPO:$tipo_error<br>";
			$a = explode("\t\n\t",$errstr);
			if (substr($a[0],0,2) == "a:") {
				$a[0] = unserialize($a[0]);
			}
				echo "DESCRIPCION:<pre>";
				if (is_array($a[0])) {
					print_r($a[0]);
				}
				else {
					echo $a[0];
				}
				echo "</pre><br>";
				echo "ARCHIVO:".$a[1]."<br>";
				echo "LINEA:".$a[2]."<br>";
			if (count($_ultimo_error) > 0) {
				echo "ERRORES:<pre>";
				print_r($_ultimo_error);
				echo "</pre>";
				$_ultimo_error = array();
			}
			echo "USUARIO:".$_ses_user["name"]."<br>";
		}
		$msg_error .= "</table></td></tr>";
		$msg_error .= "<tr><td height='50%' align='center'>";
		/*$link_volver = "";
		if ($_SERVER["REQUEST_URI"] != "") {
			$link_volver .= $_SERVER["REQUEST_URI"];
		}
		elseif ($_SERVER["HTTP_REFERER"] != "") {
			$link_volver .= $_SERVER["HTTP_REFERER"];
		}
		if ($link_volver == "") {
			$msg_error .= "&nbsp;";
		}
		else {*/
			//$msg_error .= "<input type=button value='Volver' onClick=\"document.location='$link_volver';\" style='width:100px;height:30px;'>";
			$msg_error .= "<input type=button value='Volver' onClick=\"history.back();\" style='width:100px;height:30px;'>";
		//}
		$msg_error .= "</td></tr>";
		$msg_error .= "</table>\n";
		echo $msg_error;
		//phpinfo();
	}
}

function reportar_error($descripcion,$archivo,$linea) {
	if (is_array($descripcion)) {
		$descripcion = serialize($descripcion);
	}
	trigger_error($descripcion."\t\n\t".$archivo."\t\n\t".$linea);
	//fin_pagina();
	exit();
}


///////////////////////////////PARA ELIMINAR ELEMENTOS REPETIDOS EN UN ARRAY////////////////////////////////////
///////////////////////////////BROGGI///////////////////////////////////////////////////////////////////////////
//si retorna_en = 1 la salida es es un arreglo
//si retorna_en = 0 la salida es es un string
function elimina_repetidos($entrada,$retorna_en=1)
{$copia=array();
 $tamaño=count($entrada);
 $indice=0;
 $indice_copia=0;
 while ($indice<$tamaño)
       {$auja=$entrada[$indice];
        $entrada[$indice]="";
        if (in_array($auja,$entrada))
           {
           }
        else {$copia[$indice_copia]=$auja;
              $indice_copia++;
             }
        $indice++;
       }
 if ($retorna_en==1) return $copia;
 else {$tamaño=count($copia);
       $indice=0;
       $string=$copia[$indice];
       $indice++;
       while ($indice<$tamaño)
             {$string.=",".$copia[$indice];
              $indice++;
             }
       return $string;
      }
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function isIPIn($ip,$net,$mask) {
   $lnet=ip2long($net);
   $lip=ip2long($ip);
   $binnet=str_pad( decbin($lnet),32,"0",STR_PAD_LEFT );
   $firstpart=substr($binnet,0,$mask);
   $binip=str_pad( decbin($lip),32,"0",STR_PAD_LEFT );
   $firstip=substr($binip,0,$mask);
   return(strcmp($firstpart,$firstip)==0);
}

function getIP() {
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
       $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    elseif (isset($_SERVER['HTTP_VIA'])) {
       $ip = $_SERVER['HTTP_VIA'];
    }
    elseif (isset($_SERVER['REMOTE_ADDR'])) {
       $ip = $_SERVER['REMOTE_ADDR'];
    }
    else {
       $ip = "unknown";
    }
	return $ip;
}


function Mostrar_Header($FileName,$FileType,$FileSize) {
  Header("Cache-Control: post-check=0,pre-check=0");
  Header("Content-Type: $FileType");
  Header("Content-Transfer-Encoding: binary"); 
  Header("Content-Connection: close"); 
  Header("Content-Disposition: attachment; filename=\"$FileName\"");
  Header("Content-Description: $FileName");
  Header("Content-Length: $FileSize");
}

function validar_clave($clave,&$error_clave){
   if(strlen($clave) < 6){
      $error_clave = "La clave debe tener al menos 6 caracteres";
      return false;
   }
   if(strlen($clave) > 16){
      $error_clave = "La clave no puede tener más de 16 caracteres";
      return false;
   }
   if (!preg_match('`[a-z]`',$clave)){
      $error_clave = "La clave debe tener al menos una letra minúscula";
      return false;
   }
   if (!preg_match('`[A-Z]`',$clave)){
      $error_clave = "La clave debe tener al menos una letra mayúscula";
      return false;
   }
   if (!preg_match('`[0-9]`',$clave)){
      $error_clave = "La clave debe tener al menos un caracter numérico";
      return false;
   }
   $error_clave = "";
   return true;
} 

function es_letra($clave,&$error_clave){
  if (!preg_match('`[A-Z]`',$clave)) {
    return false;
  }
  else {
    return true;
  }
}

function es_cuie($cuie){
  $primera_letra=substr($cuie,0,1);
  $num_cuie=substr($cuie,1,5);
  $boolean_numero=es_numero($num_cuie);
  $boolean_letra=es_letra($primera_letra,$error_txt);
  if ($boolean_numero&&$boolean_letra)
    return true;
  else 
    return false;
  
}

function enviar_mail_html($para,$asunto,$contenido,$adjunto,$path,$adj=1){ 
  return enviar_mail($para,null,null,$asunto,$contenido,$adjunto,$path,'0');
}

function edad($edad){
  list($anio,$mes,$dia) = explode("-",$edad);
  $anio_dif = date("Y") - $anio;
  $mes_dif = date("m") - $mes;
  $dia_dif = date("d") - $dia;
  if ($dia_dif < 0 || $mes_dif < 0) {
    $anio_dif--;
  }
  return $anio_dif;
}

function GetCountDaysBetweenTwoDates($DateFrom,$DateTo){
  $HoursInDay = 24;
  $MinutesInHour = 60;
  $SecondsInMinutes = 60;
  $SecondsInDay = (($SecondsInMinutes*$MinutesInHour)*$HoursInDay );
  return intval(abs(strtotime($DateFrom) - strtotime($DateTo))/$SecondsInDay);
}

function bisiesto($anio_actual){
      $bisiesto=false;
      //probamos si el mes de febrero del año actual tiene 29 días
        if (checkdate(2,29,$anio_actual))
        {
          $bisiesto=true;
      }
      return $bisiesto;
}

function dia_mes_anio($fecha_desde,$fecha_hasta){
  // separamos en partes las fechas
  $array_nacimiento = explode ( "-", $fecha_desde );
  $array_actual = explode ( "-", $fecha_hasta );
  
  $anos =  $array_actual[0] - $array_nacimiento[0]; // calculamos años
  $meses = $array_actual[1] - $array_nacimiento[1]; // calculamos meses
  $dias =  $array_actual[2] - $array_nacimiento[2]; // calculamos días
  
  //ajuste de posible negativo en $días
  if ($dias < 0)
  {
      --$meses;
  
      //ahora hay que sumar a $dias los dias que tiene el mes anterior de la fecha actual
      switch ($array_actual[1]) {
             case 1:     $dias_mes_anterior=31; break;
             case 2:     $dias_mes_anterior=31; break;
             case 3: 
                  if (bisiesto($array_actual[0]))
                  {
                      $dias_mes_anterior=29; break;
                  } else {
                      $dias_mes_anterior=28; break;
                  }
             case 4:     $dias_mes_anterior=31; break;
             case 5:     $dias_mes_anterior=30; break;
             case 6:     $dias_mes_anterior=31; break;
             case 7:     $dias_mes_anterior=30; break;
             case 8:     $dias_mes_anterior=31; break;
             case 9:     $dias_mes_anterior=31; break;
             case 10:     $dias_mes_anterior=30; break;
             case 11:     $dias_mes_anterior=31; break;
             case 12:     $dias_mes_anterior=30; break;
      }
  
      $dias=$dias + $dias_mes_anterior;
  }
  
  //ajuste de posible negativo en $meses
  if ($meses < 0)
  {
      --$anos;
      $meses=$meses + 12;
  }
  return  array ("anios"=>$anos, "meses"=> $meses, "dias"=> $dias);
}

function icono_sort($indice) {
  global $sort, $up;
  $ret = '';
  if (is_numeric($indice) && is_numeric($sort) && ($up == 0 || $up == 1)) {
    if ($sort == $indice) {
      if ($up == 0) {
        $ret = '<span class="glyphicon glyphicon-chevron-up"></span>';  
      }
      else {
        $ret = '<span class="glyphicon glyphicon-chevron-down"></span>';
      }
    } 
  }
  return $ret;
}

/*******************************************
 ** Autenticar el usuario
 *******************************************/

//set_error_handler('errorHandler');
// require_once(MOD_DIR."/permisos/permisos.class.php");
if (isset($_POST["username"])) {
  $user = $db->Quote($_POST["username"]);
  $pass = $db->Quote(md5($_POST["password"]));
  $query = "SELECT 
              u.id,
              u.login,
              t.id AS id_tipo,
              t.nombre AS tipo,
              u.apellido,
              u.nombre,
              u.direccion,
              u.telefono,
              u.email,
              u.observaciones,
              u.fecha_alta,
							u.cuil
            FROM
              sistema.usuarios u
              LEFT OUTER JOIN sistema.usuarios_tipos t ON (u.id_tipo = t.id)
            WHERE 
              u.activo = 't' AND
              u.login = $user AND
              u.passwd = $pass
            LIMIT 1
            ";
  $res = sql($query) or form_login();

  if ($res !== false && $res->recordCount() == 1) {
    $usuario->id            = $res->fields["id"];
    $usuario->login         = $res->fields["login"];
    $usuario->id_tipo       = $res->fields["id_tipo"];
    $usuario->tipo          = $res->fields["tipo"];
    $usuario->apellido      = $res->fields["apellido"];
    $usuario->nombre        = $res->fields["nombre"];
    $usuario->direccion     = $res->fields["direccion"];
    $usuario->telefono      = $res->fields["telefono"];
    $usuario->email         = $res->fields["email"];
    $usuario->observaciones = $res->fields["observaciones"];
	$usuario->fecha_alta    = $res->fields["fecha_alta"];
	$usuario->cuil    			= $res->fields["cuil"];
    $usuario->sess_start    = time();
  }
  else {
    form_login();
  }
	// Verificar que el ip sea valido
	// $myip = getIP();
	// $ip_permitida = false;
	// foreach ( $ip_permitidas as $k=>$v ) {
	// 	list($net,$mask)=split("/",$k);
	// 	if (isIPIn($myip,$net,$mask)) {
	// 		$ip_permitida = true;
	// 	}
	// }
	
	// if (!$ip_permitida) {
	// 	$acceso_remoto = false;
	// 	$sql = "select login from usuarios where acceso_remoto=1";
	// 	$result = sql($sql) or die("No se pudo verificar el usuario");
	// 	while (!$result->EOF) {
	// 		if ($result->fields['login'] == $_POST['username'])
	// 			$acceso_remoto = true;
	// 		$result->MoveNext();
	// 	}
	// }

	header("Location: $html_root/");
  die();
}

if ($usuario->has('id')) {
  if(((time() - $usuario->sess_start) > $session_timeout * 60)){
    $usuario->destroy();
    form_login();
  }
  else {
    $usuario->sess_start = time();
  }
}
else {
  form_login();
}

/*******************************************
 ** Variables Utiles
 *******************************************/

// Tamaño máximo de los archivos a subir
$max_file_size = get_cfg_var("upload_max_filesize");  // Por defecto deberia se 5 MB

// Para usar con los resultados boolean de la base de datos
$sino=array(
	"0" => "No",
	"f" => "No",
	"false" => "No",
	"NO" => "No",
	"n" => "No",
	"N" => "No",
	"1" => "Sí",
	"t" => "Sí",
	"true" => "Sí",
	"SI" => "Sí",
	"s" => "Sí",
	"S" => "Sí"
);
// Para el formato de fecha
$dia_semana = array("Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado");
$meses = array("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

// El tipo de resultado debe ser n para que funcione la
// libreria phpss
db_tipo_res("d");

$GLOBALS["parametros"] = decode_link($_GET["p"]);

define("lib_included","1");

$default_url = "";
if (isset($_SERVER["REDIRECT_URL"])) {
  $default_url_tmp = MOD_DIR.preg_replace('/^'.addcslashes($html_root, '/').'/', '', $_SERVER["REDIRECT_URL"]);
  if (substr($default_url_tmp, -4) != ".php") {
    $default_url_tmp .= ".php";
  }
  if (file_exists($default_url_tmp)) {
    $url_permiso = preg_replace('/^'.addcslashes($html_root, '/').'|_datos$|_funciones$/', '', $_SERVER["REDIRECT_URL"]);
    if (permiso_check($url_permiso, "ver")) {
      // Si es pedido por AJAX
      if (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && $_SERVER["HTTP_X_REQUESTED_WITH"] == "XMLHttpRequest") {
        include_once($default_url_tmp);
        die();
      }
      else {
        $default_url = $default_url_tmp;
      }
    }
    else {
      header('HTTP/1.0 404 not found ('.$url_permiso.')');
      die();
    }
  }
  else {
    header('HTTP/1.0 404 not found');
    die();
  }
}
?>