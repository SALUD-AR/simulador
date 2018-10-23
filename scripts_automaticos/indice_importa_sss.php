<?

include("funciones_generales.php");

        $sql_tmp="CREATE INDEX beneficiarios_sss__indice_dni
                    ON puco.beneficiarios_sss
                    USING btree
                    (numero_documento);";
        $db->Execute($sql_tmp) or die("Error borrando las sesiones\n");
?>