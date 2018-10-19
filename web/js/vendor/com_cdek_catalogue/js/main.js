function hideLoader() {
    jQuery('#points_loader').hide();
    jQuery('#points_container').fadeIn();
}

function showLoader() {
    jQuery('#points_container').hide();
    jQuery('#points_loader').show();
}

function initPoints() {
    jQuery('#reset_cities').on('click', function () {
        jQuery('.cities a.active').removeClass('active');
        jQuery(this).addClass('active');
        jQuery('.points').show();
        return false;
    });

    jQuery('.cities a.select_city').on('click', function () {
        jQuery('.cities a.active').removeClass('active');
        jQuery(this).addClass('active');
        jQuery('.points').hide();
        jQuery('.points_' + jQuery(this).data('city_id')).show();
        return false;
    });

    jQuery('#show_all_link').on('click', function () {
        jQuery('#block_map_all').toggle();
        if (jQuery(this).hasClass('expanded')) {
            jQuery(this).html('Показать все на карте &nbsp;&nbsp;<i class="fa fa-hand-o-down"></i>').removeClass('expanded');
        }
        else {
            jQuery(this).html('Свернуть карту &nbsp;&nbsp;<i class="fa fa-hand-o-up"></i>').addClass('expanded');
        }
        return false;
    });

    drawTotalMap();
}

function getCities(region_id) {
    var url = urlPrefix + '/contacts/cities';
    if (typeof region_id !== "undefined") {
        url += '?region_id=' + region_id;
    }
    jQuery.ajax({
        url: url,
        dataType: 'html',
        success: function (data) {
            jQuery('#points_container').html(data);
            initPoints();
            hideLoader();
        },
        error: function () {
            alert('Не удалось загрузить список пунктов выдачи');
            jQuery('#points_container').html('');
            hideLoader();
        }
    });
}

function drawTotalMap() {
    ymaps.ready(function () {
        var myGeoObjects = new ymaps.GeoObjectCollection();
        jQuery.each(points_array, function (i, point) {
            myGeoObjects.add(new ymaps.Placemark([point.coord_y, point.coord_x],
                    {
                        hintContent: point.object_name,
                        balloonContent: '<div style="width:100%;border-bottom:1px solid #c0c0c0;padding:0 0 10px;color:#9B3F13;">' + point.object_description + '</div>'

                    }, {
                // Опции.
                iconLayout: 'default#image',
                // Своё изображение иконки метки.
                iconImageHref: '/images/yamap.png',
                // Размеры метки.
                iconImageSize: [40, 40],
                // Смещение левого верхнего угла иконки относительно
                // её "ножки" (точки привязки).
                iconImageOffset: [-8, -40]
            }

            ));
            center_y = point.coord_y;
            center_x = point.coord_x;
        });
        myMap = initYMap('block_map_all', center_y, center_x, 10);
        myMap.geoObjects.add(myGeoObjects);
        if (points_array.length > 1) {
            myMap.setBounds(myGeoObjects.getBounds());
        }
        jQuery('#block_map_all').hide().css('top', 0);
        jQuery('#points_all').css('top', 0);
    });
}

function submitRegionsPage() {
    showLoader();
    getCities(jQuery('#region').val());
}

function drawYMap(container, point_object) {
    ymaps.ready(function () {
        var my_map = initYMap(container, point_object.coord_y, point_object.coord_x, 17);
        addPlaceToYMap(my_map, point_object.object_name, point_object.object_description);
    });
}

function initYMap(container, coord_y, coord_x, zoom) {
    var options = {};
    options.controls = ['zoomControl', 'searchControl', 'typeSelector', 'fullscreenControl'];
    options.center = [coord_y, coord_x];
    if (typeof zoom !== "undefined") {
        options.zoom = 17;
    }
    else {
        options.zoom = zoom;
    }
    var my_map = new ymaps.Map(container, options);
    return my_map;
}

function addPlaceToYMap(my_map, object_name, object_description) {
    var my_placemark = new ymaps.Placemark(
            my_map.getCenter(),
            {
                hintContent: object_name,
                balloonContent: '<div style="width:100%;border-bottom:1px solid #c0c0c0;padding:0 0 10px;color:#9B3F13;">' + object_description + '</div>'
            }, {
        // Опции.
        iconLayout: 'default#image',
        // Своё изображение иконки метки.
        iconImageHref: '/images/yamap.png',
        // Размеры метки.
        iconImageSize: [40, 40],
        // Смещение левого верхнего угла иконки относительно
        // её "ножки" (точки привязки).
        iconImageOffset: [-8, -40]
    });
    my_map.geoObjects.add(my_placemark);
}
;

function toggleMap(point_code) {
    if (jQuery('#map_link' + point_code).hasClass('expanded')) {
        jQuery('#map_link' + point_code)
                .html('Свернуть <span> ↑</span>')
                .removeClass('expanded');
    } else {
        jQuery('#map_link' + point_code)
                .html('Развернуть <span> ↓</span>')
                .addClass('expanded');
    }
    jQuery('#block_map' + point_code).toggle();
    return false;
}

jQuery(document).ready(function () {
    getCities();
});


