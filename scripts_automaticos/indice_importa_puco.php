<?

include("funciones_generales.php");

        $sql_tmp="CREATE INDEX doc_i
                  ON puco.puco
                  USING btree
                  (documento)";
        $db->Execute($sql_tmp) or die("Error borrando las sesiones\n");
?>