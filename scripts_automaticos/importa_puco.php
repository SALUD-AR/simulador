<?

include("funciones_generales.php");

$filename = 'puco.txt'; 

        if (!$handle = fopen($filename, 'r')) {
             echo "No se Puede abrir ($filename)";
            exit;
        }
        
        $sql_tmp="truncate table puco.puco;";
        $db->Execute($sql_tmp) or die("Error borrando las sesiones\n");

        $sql_tmp="DROP INDEX puco.doc_i;";
        $db->Execute($sql_tmp) or die("Error borrando las sesiones\n");

               
       while (!feof($handle)) {
        $buffer = fgets($handle, 61);
        $a=substr($buffer,3,8);
        $b=substr($buffer,0,3);
        $c=substr($buffer,15,6);
        $d=substr($buffer,22,40);       
        
        $b=trim($b);
        $d=ereg_replace('[^ A-Za-z0-9_-]','',trim($d));
        $c=ereg_replace('[^ A-Za-z0-9_-]','',trim($c));
        $a=trim($a);
		$d=utf8_encode($d);	
		if ($a!='') {
        $sql_tmp="INSERT INTO puco.puco
                    (tipo_doc,nombre,cod_os,documento)
                    VALUES
                    ('$b', '$d','$c','$a')";
        $db->Execute($sql_tmp) or die("Error Insertando los registros\n".$sql_tmp); 
			};
	   }
		fclose($handle);   
?>