<?

include("funciones_generales.php");

        $sql_tmp="CREATE INDEX pami_cap_hec_dni
                  ON puco.pami_cap_hec
                  USING btree
                  (dni);";
        $db->Execute($sql_tmp) or die("Error borrando las sesiones\n");
?>