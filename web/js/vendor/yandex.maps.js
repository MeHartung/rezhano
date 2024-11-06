yandexMapPlacemarks = [];
ymaps.ready(initMap);

function initMap ()
{
  var myMap = new ymaps.Map('map', {
    center: [60.731135,56.792636],
    type: 'yandex#publicMap',
    zoom: 16
  }), lastplacemark = null;

  myMap.controls.add('zoomControl', { left: 5, top: 5 });

  placemarksCollection = new ymaps.GeoObjectCollection({}, {
     draggable: false
  });
  
  var mapIcon = {iconImageHref:"/images/map-ico.png", iconImageSize:[46, 56], iconImageOffset:[-13, -52]};
  
  
  $.each(yandexMapPlacemarks, function(key, value)
  {
    var placemark = new ymaps.Placemark(value['coords'], value['placemark'], mapIcon); lastplacemark = placemark;
    var place = $('#place_' + value['id'] + ' .title').parent();

    placemarksCollection.add(placemark);

    placemark.events.add('click', function(){
      $('.contact-wrap').removeClass('active');
      place.addClass('active');
    });

    $('#place_' + value['id'] + ' .title').click(function(){
      $('.contact-wrap').removeClass('active');
      place.addClass('active');
      setTimeout(function(){placemark.balloon.open()}, 1300);
      myMap.setCenter(value['coords'], 16, {duration:900});
    });
  });

  myMap.geoObjects.add(placemarksCollection);
  myMap.setBounds(placemarksCollection.getBounds(), {zoomMargin: [50]});
  if (yandexMapPlacemarks.length === 1)
  {
    myMap.setCenter(yandexMapPlacemarks[0]['coords'], 16, {duration:900});
    setTimeout(function(){lastplacemark.balloon.open()}, 1300);
  }
}