var myMap, date1, date2, checkLoadMap = false;

function init(){
    if (!checkLoadMap) {
        checkLoadMap = true;
        myMap = new ymaps.Map('map', {
		    center: [59.95035350157218, 29.969579841796854],
		    zoom: 8,
		    controls: ['zoomControl']
	    }, {
		    // В нашем примере хотспотные данные есть только для 9 и 10 масштаба.
		    // Поэтому ограничим диапазон коэффициентов масштабирования карты.
		    //minZoom: 2,
		    //maxZoom: 2
	    });
	
	    myMap.events.add('click', function(e) {
		    var coords = e.get('coords');
		    //var projection = myMap.options.get('projection');
		    //alert(projection.toGlobalPixels([61.03188, 28.087042],myMap.getZoom()));
		    $("#info").html(coords.join(', '));
	    });
	} else {
	    myMap.layers.each(function(layer){
	        if (layer.options._options.name == "png") 
	            myMap.layers.remove(layer);
	        //console.log(layer.options._options.name);
	    });
	}
	
    var imgUrlTemplate = 'createImg/png.php?tileX=%x&tileY=%y&tileZ=%z&date1='+date1+'&date2='+date2,
		imgLayer = new ymaps.Layer(imgUrlTemplate, {
			tileTransparent: true,
			name: "png"
			
		});
    // Добавляем слои на карту.
    myMap.layers.add(imgLayer);
    //console.log(date1+" "+date2);
}
	
$(document).ready(function(){
	
	$(".calendar").submit(function(){
	    date1 = $(this).find("input[name='date1']").val();
	    date2 = $(this).find("input[name='date2']").val();
	    ymaps.ready(init);
	    return false;
	});
});