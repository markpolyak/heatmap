<?php
	require_once("../lib/config.php");
	require_once("func.php");
	$time = microtime(true);
	
	$wh = 256;                                              //высота и ширина каждого тайла
	$tileX = isset($_GET['tileX'])?$_GET['tileX']:149;      //выбрать тайл по X
	$tileY = isset($_GET['tileY'])?$_GET['tileY']:74;       //выбрать тайл по Y
	$zoom = isset($_GET['tileZ'])?$_GET['tileZ']:8;         //увеличение
	$date1 = isset($_GET['date1'])?$_GET['date1']:"1950-01-01";        //дата от
	$date2 = isset($_GET['date2'])?$_GET['date2']:"2017-01-01";        //дата до
	$checkLog = isset($_GET['log'])?$_GET['log']:false;                //включить логи
	$tileDirWater = "karta/z".$zoom."/";                    //папка сохранения тайлов шаблона воды
	$tileDirGradient = "kartaGradient/z".$zoom."/";         //папка сохранения тайлов градиента
	$fileName = "x".$tileX."y".$tileY.".png";               //имя тайла для сохранения
	$existsFileName = (file_exists($tileDirGradient.$fileName))?true:false; //проверка существования тайла
	
	//координаты начала фотки
	$nachZoomWater = pow(2,9-$zoom);                         //начальный зум воды
    $widthWater = 1522;                                      //ширина шаблона воды
	$heightWater = 1100;                                     //высота шаблона воды  
	//примерное ОДЗ воды    
	$kordWater = array(
		"x" => getLongitudeToPix(26.437022,$zoom),
		"y" => getLatitudeToPix(60.83086,$zoom)
	);
	$whMapGlobal = pow(2,$zoom+8);
	$tileOdzX1 = (int)($kordWater["x"]/$wh);
	$tileOdzY1 = (int)($kordWater["y"]/$wh);
	$tileOdzX2 = (int)(($kordWater["x"]+$widthWater/$nachZoomWater)/$wh);
	$tileOdzY2 = (int)(($kordWater["y"]+$heightWater/$nachZoomWater)/$wh);
	
	//echo $tileOdzX1." ".$tileOdzY1."<br>";
	//echo $tileOdzX2." ".$tileOdzY2."<br>";
		
	//если тайлы входят в ОДЗ 
	if ($tileX >= $tileOdzX1 && $tileY >= $tileOdzY1 && $tileX <= $tileOdzX2 && $tileY <= $tileOdzY2) {
		
		if ($existsFileName) {
			$im = imageCreateFromPng($tileDirGradient.$fileName) or die ("Ошибка при создании изображения");
			imagesavealpha($im, true);
		} else {
			$im = imageCreateTrueColor ($wh, $wh) or die ("Ошибка при создании изображения");
			imagealphablending($im, false);
			imagesavealpha($im, true);
			$couleur_fond = ImageColorAllocateAlpha ($im, 0, 0, 0, 127);
			
			if (file_exists($tileDirWater.$fileName)) {
				$im1 = imageCreateFromPng($tileDirWater.$fileName) or die ("Ошибка при создании изображения");
			} else {
				session_start();
				//unset($_SESSION["imgKarta"]);
				$_SESSION["imgKarta"] = imageCreateFromPng("karta.png");
				//$_SESSION["imgKarta"] = imageCreateTrueColor($wh,$wh);
				$im2 = imageCreateTrueColor (imagesx($_SESSION["imgKarta"]), imagesy($_SESSION["imgKarta"])) or die ("Ошибка при создании изображения");
				imageCopy($im2,$_SESSION["imgKarta"],0,0,0,0,imagesx($im2),imagesy($im2));
				//$im1 = imageCreateTrueColor (imagesx($im2)/$nachZoomWater, imagesy($im2)/$nachZoomWater) or die ("Ошибка при создании изображения");
				//imagecopyresized($im1,$im2,0,0,0,0,imagesx($im2)/$nachZoomWater,imagesy($im2)/$nachZoomWater,imagesx($im2),imagesy($im2));
				
				$dstX = 0;
				$dstY = 0;
				$srcX = $wh*$tileX-$kordWater["x"];
				$srcY = $wh*$tileY-$kordWater["y"];
				if ($srcX < 0) {
					$dstX = -$srcX;
					$srcX = 0;
				}
				if ($srcY < 0) {
					$dstY = -$srcY;
					$srcY = 0;
				}
				$srcX *= $nachZoomWater;
				$srcY *= $nachZoomWater;
				$srcWH = $wh*$nachZoomWater;
				//if ($srcWH > imagesx($im2) && $srcWH > imagesy($im2)) $srcWH = (imagesx($im2) > imagesy($im2))?imagesx($im2):imagesy($im2);
				//echo $razm1*$nachZoomWater."<br>";
				//echo $razm2*$nachZoomWater."<br>";
				
				$im1 = imageCreateTrueColor ($wh, $wh) or die ("Ошибка при создании изображения");
				imagecopyresized($im1,$im2,$dstX,$dstY,$srcX,$srcY,$wh,$wh,$srcWH,$srcWH);
				imagedestroy($im2);
				
				if (!file_exists($tileDirWater)) mkdir($tileDirWater, 0777, true);
				ImagePNG($im1,$tileDirWater.$fileName,1); 
			}
			
			$pixels = array();
			
			// Выполнение SQL запроса	
	        $query = "SELECT DISTINCT VWS.longitude, VWS.latitude, PPS.chlorophyll_a_concentration, VWS.sample_date
		        FROM vw_samples AS VWS INNER JOIN photosynthetic_pigments_samples AS PPS ON VWS.id_sample = PPS.id_sample 
		        WHERE VWS.sample_date >= '$date1' AND VWS.sample_date <= '$date2'
		        ORDER BY VWS.sample_date ASC";
		    
		    $result = pg_query($query) or die('Ошибка запроса: ' . pg_last_error());
		    
		    while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
		        $pixels["l{$line['longitude']}l{$line['latitude']}"] = 
		                array(	"x" => getLongitudeToPix($line['longitude'],$zoom),
								"y" => getLatitudeToPix($line['latitude'],$zoom),
								"ves" => $line['chlorophyll_a_concentration'],
								"date" => $line['sample_date']);
		    }
		    
		    if ($checkLog) {
		        echo sizeof($pixels);
		        echo "<pre>";
		        print_r($pixels);
		        echo "</pre>";
		    }

            /*
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
			*/

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
			//echo microtime(true) - $time."<br>";
			
			for($i = 0; $i < $wh; $i++) 
			{
				for($j = 0; $j < $wh; $j++)
				{
					$x = $i+$wh*$options["tileX"];
					$y = $j+$wh*$options["tileY"]; 

					//$srcColor = imagecolorsforindex($im1, ImageColorAt($im1, $x-$kordWater["x"], $y-$kordWater["y"]));
					$srcColor = imagecolorsforindex($im1, ImageColorAt($im1, $i, $j));
					//$rgb = ImageColorAt($im, $i, $j);
					//$r = ( $rgb >> 16) & 0xFF;
					//echo " ".($x-$kordWater["x"])." | ".($y-$kordWater["y"])." |".(($srcColor["alpha"] == 0)?$srcColor["red"]:"null")."<br>";
					//echo (($srcColor["alpha"] == 0)?$srcColor["red"]:"null")."<br>";
					
					//ищем по шаблону karta.png
					//если красный цвет и не прозрачный то вода
					if ($srcColor["red"] == 255 && $srcColor["alpha"] == 0)
					{
						$pixels2[$i][$j]["ves"] = getVes($pixels, $i, $j, $options);
						//echo $pixels2[$i][$j]["ves"]."<br>";
						if (($options["maxVes"]-$options["minVes"]) != 0) $colorG = (int)(($pixels2[$i][$j]["ves"]-$options["minVes"])*(255/($options["maxVes"]-$options["minVes"])));
						else $colorG = (int)(($pixels2[$i][$j]["ves"]*255)/$options["maxVes"]);
						
						if ($colorG < 0) $colorG = 0;
						if ($colorG > 255) $colorG = 255;
						
						//echo $colorG."<br>";
						//$colorAlpha = ImageColorAllocateAlpha ($im, 255, 0, 255, 30);
						$colorAlpha = ImageColorAllocateAlpha ($im, 255-$colorG, 255, 255-$colorG, 30);
						ImageSetPixel ($im, $i, $j, $colorAlpha);
					} else ImageSetPixel ($im, $i, $j, $couleur_fond); 
				}
			}
			//if (!file_exists($tileDirGradient)) mkdir($tileDirGradient, 0777, true);
			//ImagePNG($im,$tileDirGradient.$fileName,1); 
		}
		
		//echo microtime(true) - $time;
		if (!$checkLog) {
		    header ("Content-type: image/png");
		    ImagePNG($im);
		}
	}
?>