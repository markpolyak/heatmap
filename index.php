<!-- <?php
	echo phpinfo();
?> -->

<html>
	<head>
	    <meta charset="utf-8">
		<title>Карта</title>
		<style>
		#gradient {
			position: absolute;
			top: 50%;
			right: 10px;
			background: #eee;
			-moz-transform: translateY(-50%);
			-webkit-transform: translateY(-50%);
			-o-transform: translateY(-50%);
			-ms-transform: translateY(-50%);
			transform: translateY(-50%);
			z-index: 9999;
		}
		#gradient p {
			margin: 15px 25px 10px 10px;
			text-align: right;
		}
		#gradient .gradient {
			position: absolute;
			top: 0;
			right: 0;
			width: 20px;
			height: 100%;
		    background: #00ff00;
		    background: -moz-linear-gradient(top, #ffffff 0%, #00ff00 100%);
		    background: -webkit-linear-gradient(top, #ffffff 0%, #00ff00 100%);
		    background: -o-linear-gradient(top, #ffffff 0%, #00ff00 100%);
		    background: -ms-linear-gradient(top, #ffffff 0%, #00ff00 100%);
		    background: linear-gradient(top, #ffffff 0%, #00ff00 100%);

		}
		</style>
	</head>
	<body>
	    <p id=info></p>
	    <form class=calendar>
	        <label>От: </label> <input type=date name=date1 value="1950-01-01" min="1950-01-01" />
	        <label>До: </label> <input type=date name=date2 value="2016-06-02" min="1950-01-01" />
	        <button type=submit>Посмотреть</button>
	    </form>
	    <div id="gradient">
	    	<p>0.1</p>
	    	<p>5</p>
	    	<p>10</p>
	    	<p>15</p>
	    	<p>20</p>
	    	<p>25</p>
	    	<p>30</p>
	    	<div class="gradient"></div>
	    </div>
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
