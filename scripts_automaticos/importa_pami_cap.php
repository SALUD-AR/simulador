<?

include("funciones_generales.php");

$filename = 'pamicap.txt'; 

        if (!$handle = fopen($filename, 'r')) {
             echo "No se Puede abrir ($filename)";
            exit;
        }
        
        $sql_tmp="truncate table puco.pami_cap_hec;";
        $db->Execute($sql_tmp) or die("Error borrando las sesiones\n");

        $sql_tmp="DROP INDEX puco.pami_cap_hec_dni;";
        $db->Execute($sql_tmp) or die("Error borrando las sesiones\n");

       while (!feof($handle)) {
        $buffer = fgets($handle);
        $datos = explode("\t",$buffer);

        $a=trim($datos[0]);
        $b=trim($datos[1]);
        $c=trim($datos[2]);
        $d=trim($datos[3]);       
        $e=trim($datos[4]);       
        $f=trim($datos[5]);       
        $g=trim($datos[6]);       
        
        $c=utf8_encode($c); 
        $c=ereg_replace('[^ A-Za-z0-9_-]','',$c);

        if ($a!='') {
            $sql_tmp="INSERT INTO puco.pami_cap_hec
                        (beneficio,gp,ape_nom,tipo_dni,dni,fecha_nac,modulos)
                        VALUES
                        ('$a','$b','$c','$d','$e','$f','$g')";
            $db->Execute($sql_tmp) or die("Error Insertando los registros\n".$sql_tmp); 
    		};
	     }
		    
      fclose($handle);   
?>