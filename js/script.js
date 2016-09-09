var myMap;

function init(){     
    var myMap = new ymaps.Map('map', {
		center: [59.95035350157218, 29.969579841796854],
		zoom: 11,
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
		$("#coord").html(coords.join(', '));
	});
	
    var imgUrlTemplate = 'createImg/png.php?tileX=%x&tileY=%y&tileZ=%z',
	//var imgUrlTemplate = 'images/%z/tile-%x-%y.jpg',
		imgLayer = new ymaps.Layer(imgUrlTemplate, {
			tileTransparent: true
		});
    // Добавляем слои на карту.
    myMap.layers.add(imgLayer);
}
	
$(document).ready(function(){
	ymaps.ready(init);
});