<?

include("funciones_generales.php");

        $sql_tmp="truncate table puco.beneficiarios_sss;";
        $db->Execute($sql_tmp) or die("Error truncate\n");

        $sql_tmp="DROP INDEX puco.beneficiarios_sss__indice_dni;";
        $db->Execute($sql_tmp) or die("Error drop index\n");

        
        $indice=1;
        while ($indice<6) {
          $filename = '0'.$indice.'.txt';
          if (!$handle = fopen($filename, 'r')) {
               echo "No se Puede abrir ($filename)";
              exit;
          }    

           while (!feof($handle)) {
            $buffer = fgets($handle);
            $datos = explode("|",$buffer);

            $a=trim($datos[0]);
            $b=trim($datos[1]);
            $c=trim($datos[2]);
            $d=trim($datos[3]);       
            $e=trim($datos[4]);       
            $f=trim($datos[5]);       
            $g=trim($datos[6]);       
            $h=trim($datos[7]);       
            $i=trim($datos[8]);       
            $j=trim($datos[9]);       
            $k=trim($datos[10]);       
            $l=trim($datos[11]);       
            $m=trim($datos[12]);       
            $n=trim($datos[13]);       
            $o=trim($datos[14]);       
            $p=trim($datos[15]);  

            $d=utf8_encode($d); 
            $d=ereg_replace('[^ A-Za-z0-9_-]','',$d);     

                $sql_tmp="INSERT INTO puco.beneficiarios_sss
                            (cuil_beneficiario,
                            tipo_documento,
                            numero_documento,
                            nombre_apellido,
                            sexo,
                            fecha_nacimiento,
                            tipo_beneficiario,
                            codigo_parentesco,
                            codigo_postal,
                            id_provincia,
                            cuil_titular,
                            codigo_os,
                            ultimo_aporte,
                            cuil_valido,
                            cuit_empleador,
                            lote)
                            VALUES
                            ('$a','$b','$c','$d','$e','$f','$g','$h','$i','$j','$k','$l','$m','$n','$o',0)";
                $db->Execute($sql_tmp); //or die("Error Insertando los registros\n".$sql_tmp); 
    	     }		    
          fclose($handle); 
          $indice++;
      } 

?>