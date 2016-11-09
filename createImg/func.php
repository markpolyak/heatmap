<?php
    //долгота в px
	function getLongitudeToPix($lon,$z)
	{
		return (int) (($lon + 180.0) / 360.0 * (256 * pow(2, $z)));
	}
	//широта в px
	function getLatitudeToPix($lat,$z)
	{
		$rLat = $lat * M_PI / 180; 
		$a = 6378137.0;
		$k = 0.0818191908426;

		$zz = tan(M_PI_4 + $rLat / 2)  / pow((tan(M_PI_4 + asin($k * sin($rLat)) / 2)), $k);
		$y = (20037508.342789 - $a * log($zz)) * 53.5865938 / pow( 2 ,23 - $z);
		return (int) $y;
	}
	//вычисляем вес для каждого пикселя
	function getVes($pixels, $x, $y, $options) {
		$sum = 0;
		$sumDlina = 0;
		$funcDlina = array();
		$countPixels = count($pixels);
		$srVes = $options["sumVes"]/$countPixels;
		//echo $srVes."<br>";
		foreach($pixels as $pix)
		{
			if (($x+256*$options["tileX"]) == $pix["x"] && ($y+256*$options["tileY"]) == $pix["y"]) 
			{
				return $pix["ves"];
			}
			
			$dlina = sqrt(pow($pix["x"]-($x+256*$options["tileX"]),2)+pow($pix["y"]-($y+256*$options["tileY"]),2)); //расстояние до точки
			//echo $dlina."<br>";
			$dlina = 1/pow($dlina,3+0.08*(int)($pix["ves"]/$srVes));
			$sumDlina += $dlina;
			$funcDlina[] = $pix["ves"]*$dlina;
		}
		
		//$cof = ($options["sumVes"]/$countPixels)/($options["maxVes"]-$options["minVes"]);
		for($i = 0; $i < count($funcDlina); $i++)
		{
			$sum += ($funcDlina[$i])/($sumDlina);
		}
		//echo $cof."<br>";
		
		//print_r($funcDlina);
		//echo "<br>";
		//print_r($dlinaArr);
		//echo "<br>";
		
		//$sumVes = $sumDlina2/($sumDlina*$countPixels);
		//$sumVes = $sumVes/$sumDlina;
		//echo ($sumVes/$countPixels)."<br>";*($countPixels-($maxVes/($sumVes/$countPixels)))
		return ($sum);
	}
	
	function getMaxMinVes($pixels, &$maxVes, &$minVes, &$sumVes) {
		foreach($pixels as $pix)
		{
			if ($pix["ves"] > $maxVes) $maxVes = $pix["ves"];
			if ($pix["ves"] < $minVes) $minVes = $pix["ves"];
			$sumVes += $pix["ves"];
		}
	}
?>