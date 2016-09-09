<?php 
	$time = microtime(true);
	
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
	//проверка попадания точки в область
	function ODZ($x,$y,$odzPix)
	{
		$check = false;
		for($i = 0; $i < (count($odzPix)-1); $i++)
		{
			$k1 = $i;
			$k2 = $i+1;
			if ($odzPix[$k1]["y"] >= $odzPix[$k2]["y"]) 
			{
				$k1 = $i+1;
				$k2 = $i;
			}
			
			if (!($y >= $odzPix[$k1]["y"] && $y < $odzPix[$k2]["y"])) continue;
			
			$ox = -((($odzPix[$k2]["x"] - $odzPix[$k1]["x"])*$y + ($odzPix[$k1]["x"]*$odzPix[$k2]["y"] - $odzPix[$k2]["x"]*$odzPix[$k1]["y"]))/($odzPix[$k1]["y"]-$odzPix[$k2]["y"]));
			
			if ($x > $ox) $check = !$check;
			
		}
		return $check;
	}
	
	//echo getLongitudeToPix(28.087042,0)." ,  ".getLatitudeToPix(61.03188,0)."<br>";
	
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
	
	$wh = 256;
	$tileX = isset($_GET['tileX'])?$_GET['tileX']:149;
	$tileY = isset($_GET['tileY'])?$_GET['tileY']:74;
	$zoom = isset($_GET['tileZ'])?$_GET['tileZ']:8;
	$fileDir1 = "karta/z".$zoom."/";
	$fileDir = "kartaGradient/z".$zoom."/";
	$fileName = "x".$tileX."y".$tileY.".png";
	$trueFileName = (file_exists($fileDir.$fileName))?true:false;
	//примерные координаты моря (граница верхнего левого по нижний правый)
	$xOdz1 = getLongitudeToPix(9.94837012499998,$zoom);
	$yOdz1 = getLatitudeToPix(66.42445616455103,$zoom);
	$xOdz2 = getLongitudeToPix(32.31653418749997,$zoom);
	$yOdz2 = getLatitudeToPix(52.63962485326667,$zoom);
	//координаты начала фотки
	$nachZoomVoda = pow(2,9-$zoom); //начальный зум воды
	$widthVoda = 1522;
	$heightVoda = 1100;
	$kordVoda = array(
		"x" => getLongitudeToPix(26.437022,$zoom),
		"y" => getLatitudeToPix(60.83086,$zoom)
	);
	$whMapGlobal = pow(2,$zoom+8);
	$tileOdzX1 = (int)($kordVoda["x"]/$wh);
	$tileOdzY1 = (int)($kordVoda["y"]/$wh);
	$tileOdzX2 = (int)(($kordVoda["x"]+$widthVoda/$nachZoomVoda)/$wh);
	$tileOdzY2 = (int)(($kordVoda["y"]+$heightVoda/$nachZoomVoda)/$wh);
	
	//echo $tileOdzX1." ".$tileOdzY1."<br>";
	//echo $tileOdzX2." ".$tileOdzY2."<br>";
		
	//если тайлы подходят
	if ($tileX >= $tileOdzX1 && $tileY >= $tileOdzY1 && $tileX <= $tileOdzX2 && $tileY <= $tileOdzY2) {
		
		if ($trueFileName) {
			$im = imageCreateFromPng($fileDir.$fileName) or die ("Ошибка при создании изображения");
			imagesavealpha($im, true);
		} else {
			$im = imageCreateTrueColor ($wh, $wh) or die ("Ошибка при создании изображения");
			imagealphablending($im, false);
			imagesavealpha($im, true);
			$couleur_fond = ImageColorAllocateAlpha ($im, 0, 0, 0, 127);
			
			if (file_exists($fileDir1.$fileName)) {
				$im1 = imageCreateFromPng($fileDir1.$fileName) or die ("Ошибка при создании изображения");
			} else {
				session_start();
				//unset($_SESSION["imgKarta"]);
				$_SESSION["imgKarta"] = imageCreateFromPng("karta.png");
				//$_SESSION["imgKarta"] = imageCreateTrueColor($wh,$wh);
				$im2 = imageCreateTrueColor (imagesx($_SESSION["imgKarta"]), imagesy($_SESSION["imgKarta"])) or die ("Ошибка при создании изображения");
				imageCopy($im2,$_SESSION["imgKarta"],0,0,0,0,imagesx($im2),imagesy($im2));
				//$im1 = imageCreateTrueColor (imagesx($im2)/$nachZoomVoda, imagesy($im2)/$nachZoomVoda) or die ("Ошибка при создании изображения");
				//imagecopyresized($im1,$im2,0,0,0,0,imagesx($im2)/$nachZoomVoda,imagesy($im2)/$nachZoomVoda,imagesx($im2),imagesy($im2));
				
				//echo imagesx($im2)/$nachZoomVoda/($tileOdzX2-$tileOdzX1)/$nachZoomVoda."<br>";
				//echo $wh*$tileY-$kordVoda["y"]."<br>";
				//echo ($wh*($tileY-$tileOdzY1))/$nachZoomVoda."<br>";
				
				$dstX = 0;
				$dstY = 0;
				$srcX = $wh*$tileX-$kordVoda["x"];
				$srcY = $wh*$tileY-$kordVoda["y"];
				if ($srcX < 0) {
					$dstX = -$srcX;
					$srcX = 0;
				}
				if ($srcY < 0) {
					$dstY = -$srcY;
					$srcY = 0;
				}
				$srcX *= $nachZoomVoda;
				$srcY *= $nachZoomVoda;
				$srcWH = $wh*$nachZoomVoda;
				//if ($srcWH > imagesx($im2) && $srcWH > imagesy($im2)) $srcWH = (imagesx($im2) > imagesy($im2))?imagesx($im2):imagesy($im2);
				//echo $razm1*$nachZoomVoda."<br>";
				//echo $razm2*$nachZoomVoda."<br>";
				
				$im1 = imageCreateTrueColor ($wh, $wh) or die ("Ошибка при создании изображения");
				imagecopyresized($im1,$im2,$dstX,$dstY,$srcX,$srcY,$wh,$wh,$srcWH,$srcWH);
				imagedestroy($im2);
				
				if (!file_exists($fileDir1)) mkdir($fileDir1, 0777, true);
				ImagePNG($im1,$fileDir1.$fileName,1); 
			}
			
			$pixels = array();
			$pixels[] = array(	"x" => getLongitudeToPix(30.21833,$zoom),
								"y" => getLatitudeToPix(59.88833,$zoom), 
								"ves" => 2.41);
			$pixels[] = array(	"x" => getLongitudeToPix(30.21667,$zoom), 
								"y" => getLatitudeToPix(59.97833,$zoom), 
								"ves" => 6.63);
			$pixels[] = array(	"x" => getLongitudeToPix(30.21667,$zoom), 
								"y" => getLatitudeToPix(59.96667,$zoom), 
								"ves" => 5.57);
			$pixels[] = array(	"x" => getLongitudeToPix(30.26333,$zoom), 
								"y" => getLatitudeToPix(59.91667,$zoom), 
								"ves" => 3.31);
			$pixels[] = array(	"x" => getLongitudeToPix(30.16667,$zoom), 
								"y" => getLatitudeToPix(59.885,$zoom), 
								"ves" => 4.99);
			$pixels[] = array(	"x" => getLongitudeToPix(30.15,$zoom), 
								"y" => getLatitudeToPix(59.87667,$zoom), 
								"ves" => 4.52);
			$pixels[] = array(	"x" => getLongitudeToPix(30.15,$zoom), 
								"y" => getLatitudeToPix(59.97167,$zoom), 
								"ves" => 1.20);
			$pixels[] = array(	"x" => getLongitudeToPix(30.12833,$zoom), 
								"y" => getLatitudeToPix(59.93833,$zoom), 
								"ves" => 4.22);
			$pixels[] = array(	"x" => getLongitudeToPix(30.08667,$zoom), 
								"y" => getLatitudeToPix(59.90167,$zoom), 
								"ves" => 4.02);
			$pixels[] = array(	"x" => getLongitudeToPix(30.075,$zoom), 
								"y" => getLatitudeToPix(59.885,$zoom), 
								"ves" => 6.02);
			$pixels[] = array(	"x" => getLongitudeToPix(30.06167,$zoom), 
								"y" => getLatitudeToPix(59.86333,$zoom), 
								"ves" => 31.33);
			$pixels[] = array(	"x" => getLongitudeToPix(30.00333,$zoom), 
								"y" => getLatitudeToPix(59.98833,$zoom), 
								"ves" => 6.83);
			$pixels[] = array(	"x" => getLongitudeToPix(30.01167,$zoom), 
								"y" => getLatitudeToPix(59.99833,$zoom), 
								"ves" => 51.20);
			$pixels[] = array(	"x" => getLongitudeToPix(29.98167,$zoom), 
								"y" => getLatitudeToPix(59.96,$zoom), 
								"ves" => 6.02);
			$pixels[] = array(	"x" => getLongitudeToPix(29.96167,$zoom), 
								"y" => getLatitudeToPix(59.92833,$zoom), 
								"ves" => 5.12);
			$pixels[] = array(	"x" => getLongitudeToPix(29.93667,$zoom), 
								"y" => getLatitudeToPix(59.90833,$zoom), 
								"ves" => 0.3);
			$pixels[] = array(	"x" => getLongitudeToPix(29.92,$zoom), 
								"y" => getLatitudeToPix(59.88833,$zoom), 
								"ves" => 9.64);
			$pixels[] = array(	"x" => getLongitudeToPix(29.85833,$zoom), 
								"y" => getLatitudeToPix(59.99,$zoom), 
								"ves" => 4.67);
			$pixels[] = array(	"x" => getLongitudeToPix(29.93833,$zoom), 
								"y" => getLatitudeToPix(60,$zoom), 
								"ves" => 2.41);
			$pixels[] = array(	"x" => getLongitudeToPix(29.79667,$zoom), 
								"y" => getLatitudeToPix(59.95833,$zoom), 
								"ves" => 14.06);
			$pixels[] = array(	"x" => getLongitudeToPix(29.78833,$zoom), 
								"y" => getLatitudeToPix(59.93667,$zoom), 
								"ves" => 1.51);
			$pixels[] = array(	"x" => getLongitudeToPix(29.785,$zoom), 
								"y" => getLatitudeToPix(59.91833,$zoom), 
								"ves" => 8.13);

			//echo $pixels[2]["x"]." ".$pixels[2]["y"]."<br>";
			
			$options = array(
				"tileX" => $tileX,
				"tileY" => $tileY,
		/* 		"maxVes" => $pixels[0]["ves"],
				"minVes" => $pixels[0]["ves"], */
				"maxVes" => 30,
				"minVes" => 0.1,
				"sumVes" => 0
				//"maxVes" => 10,
				//"minVes" => 1
			);
			
			getMaxMinVes($pixels, $options["maxVes"], $options["minVes"], $options["sumVes"]);
			$options["maxVes"] = 30;
			$options["minVes"] = 0.1;
			
			
			$pixels2 = array();
		/* 	for($i = 0; $i < $wh; $i++) 
			{
				for($j = 0; $j < $wh; $j++) 
				{
					$x = $i+$wh*$options["tileX"];
					$y = $j+$wh*$options["tileY"];
					
					//проверка примерной ОДЗ
					//if ((!($x > $xOdz1 && $y > $yOdz1)) || (!($x < $xOdz2 && $y < $yOdz2))) continue;
					
					$pixels2[$i][$j]["ves"] = getVes($pixels, $i, $j, $options);
					//echo $i." ".$j." ".$pixels2[$i][$j]["ves"]."<br>";
				}
			} */
			//echo microtime(true) - $time."<br>";
			
			for($i = 0; $i < $wh; $i++) 
			{
				for($j = 0; $j < $wh; $j++)
				{
					$x = $i+$wh*$options["tileX"];
					$y = $j+$wh*$options["tileY"]; 

					//$srcColor = imagecolorsforindex($im1, ImageColorAt($im1, $x-$kordVoda["x"], $y-$kordVoda["y"]));
					$srcColor = imagecolorsforindex($im1, ImageColorAt($im1, $i, $j));
					//$rgb = ImageColorAt($im, $i, $j);
					//$r = ( $rgb >> 16) & 0xFF;
					//echo " ".($x-$kordVoda["x"])." | ".($y-$kordVoda["y"])." |".(($srcColor["alpha"] == 0)?$srcColor["red"]:"null")."<br>";
					//echo (($srcColor["alpha"] == 0)?$srcColor["red"]:"null")."<br>";
					
					if ($srcColor["red"] == 255 && $srcColor["alpha"] == 0)
					//if (ODZ($x,$y,$odzPix))
					{
						$pixels2[$i][$j]["ves"] = getVes($pixels, $i, $j, $options);
						//echo $pixels2[$i][$j]["ves"]."<br>";
						if (($options["maxVes"]-$options["minVes"]) != 0) $colorG = (int)(($pixels2[$i][$j]["ves"]-$options["minVes"])*(255/($options["maxVes"]-$options["minVes"])));
						else $colorG = (int)(($pixels2[$i][$j]["ves"]*255)/$options["maxVes"]);
						
						if ($colorG < 0) $colorG = 0;
						if ($colorG > 255) $colorG = 255;
						
						//echo $colorG."<br>";
						//$colorAlpha = ImageColorAllocateAlpha ($im, 255, 0, 255, 30);
						$colorAlpha = ImageColorAllocateAlpha ($im, 255, 255-$colorG, 255-$colorG, 30);
						ImageSetPixel ($im, $i, $j, $colorAlpha);
					} else ImageSetPixel ($im, $i, $j, $couleur_fond); 
				}
			}
			if (!file_exists($fileDir)) mkdir($fileDir, 0777, true);
			ImagePNG($im,$fileDir.$fileName,1); 
		}
		
		//echo microtime(true) - $time;
		header ("Content-type: image/png");
		ImagePNG($im);
	}
?>
