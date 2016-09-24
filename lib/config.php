<?php
	// Соединение, выбор базы данных
	$dbconn = pg_connect("host=pg.sweb.ru port=5432 dbname=mpolyakru_hbio user=mpolyakru_hbio password=test1234")
		or die('Could not connect: ' . pg_last_error());
/*
	// Выполнение SQL запроса
	$query = "SELECT VWS.id_sample, VWS.date, PPS.chlorophyll_a_concentration
		FROM Samples AS VWS INNER JOIN photosynthetic_pigments_samples AS PPS ON VWS.id_sample = PPS.id_sample 
		WHERE VWS.date >= '1980-04-04' AND VWS.date <= '1983-11-01'
		ORDER BY VWS.date DESC,VWS.id_sample DESC";
	//$query = "SELECT id_sample, sample_date
	//	FROM vw_samples ORDER BY sample_date DESC,id_sample DESC";

	$result = pg_query($query) or die('Ошибка запроса: ' . pg_last_error());

	echo "<table border=1>";
    echo "<tr>";
    echo "<td>id_sample</td>";
    echo "<td>date</td>";
    echo "<td>chlorophyll_a_concentration</td>";
    echo "</tr>";
	while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
	    
	    echo "<tr>";
	    foreach ($line as $row_name => $col_value) {
		    echo "<td>$col_value</td>";
	    }
	    echo "</tr>";
	}
	echo "</table>";

	// Очистка результата
	pg_free_result($result);

	// Закрытие соединения
	pg_close($dbconn);	*/
?>