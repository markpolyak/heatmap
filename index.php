<html>
	<head>
	    <meta charset="utf-8">
		<title>Карта</title>
	</head>
	<body>
	    <p id=info></p>
	    <form class=calendar>
	        <label>От: </label> <input type=date name=date1 value="1950-01-01" min="1950-01-01" />
	        <label>До: </label> <input type=date name=date2 value="2016-06-02" min="1950-01-01" />
	        <button type=submit>Посмотреть</button>
	    </form>
		<div id=map style="width: 100%; height: 100%"></div>
	
<!--   		<img src="createImg/png.php?tileX=0&tileY=0&tileZ=2" />
		<img src="createImg/png.php?tileX=1&tileY=0&tileZ=2" />
		<img src="createImg/png.php?tileX=2&tileY=0&tileZ=2" />
		<img src="createImg/png.php?tileX=3&tileY=0&tileZ=2" />
		<br>
 		<img src="createImg/png.php?tileX=0&tileY=1&tileZ=2" />
		<img src="createImg/png.php?tileX=1&tileY=1&tileZ=2" />
		<img src="createImg/png.php?tileX=2&tileY=1&tileZ=2" />
		<img src="createImg/png.php?tileX=3&tileY=1&tileZ=2" />
		<br>  -->
<!--	<img src="createImg/png.php?tileX=0&tileY=2&tileZ=2" />
		<img src="createImg/png.php?tileX=1&tileY=2&tileZ=2" />
		<img src="createImg/png.php?tileX=2&tileY=2&tileZ=2" />
		<img src="createImg/png.php?tileX=3&tileY=2&tileZ=2" /> -->
		
		
		<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
		<!-- <script type="text/javascript" src="js/api-maps.yandex.js"></script> -->
		<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
		<!-- <script type="text/javascript" src="js/heatmap.min.js"></script> -->
		<script type="text/javascript" src="js/script.js"></script>
	</body>
</html>
