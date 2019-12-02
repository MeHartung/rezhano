define(function (require) {
  var ModalDialog = require('view/dialog/base/modal-dialog-view');

  require('ymaps');

  var template = _.template('\
    <div class="layer__close"></div>\n\
    <div class="map-wrapper" id="map"></div>\n\
  ');

  return ModalDialog.extend({
    tagName: 'div',
    className: 'layer layer-map',
    template: template,
    events: {
      'click .layer__close': 'onCloseButtonClick'
    },
    initialize: function (options) {
      ModalDialog.prototype.initialize.apply(this, arguments);
      this.address = options.address;

      this.myMap = null;
      this.store = this.model.get('store');
    },
    render: function () {
      // ModalDialog.prototype.render.apply(this, arguments);
      var self = this;
      this.$el.html(template({
        address: this.model.get('address'),
        city: this.model.get('city') ? this.model.get('city') : ''
      }));

      this.store = this.model.get('store');
      return this;
    },
    show: function () {
      this.$overlay.stop().fadeIn();
      this.$el.stop().fadeIn();
    },
    open: function () {
      ModalDialog.prototype.open.apply(this, arguments);
      var self = this;
      if (!this.myMap) {
        ymaps.ready(function () {
          self.initMap();
        });
      }
    },
    destroyMap: function () {
      if (this.myMap) {
        this.myMap.destroy();
        this.myMap = null;
      }
    },
    close: function () {
      this.destroyMap();
      this.remove();
      ModalDialog.prototype.close.apply(this, arguments);
    },
    initMap: function () {
      this.preparePlacemarks();
    },
    preparePlacemarks: function () {
      var mapIcon = {
        iconLayout: 'default#image',
        iconImageHref: '/images/icons/map-tick.png',
        iconImageSize: [52, 67],
        iconImageOffset: [-25, -67]
      };
      var shopMode = this.store ? this.store.workTime !== null ? this.store.workTime : '' : '';
      var shopPhone = this.store ? this.store.phone !== null ? this.store.phone : '' : '';

      var baloonContent = !this.store ? ('<h4 class="ymaps-title">' + this.model.get('address') + '</h4>') :
        ('<h4 class="ymaps-title">' + this.model.get('address') + '</h4>' +
          '<span class="ymaps-text">' + shopPhone + '</span>' +
          '<span class="ymaps-text">' + shopMode + '</span>');

      var address = this.model.get('address');

      var myMap,
          bounds,
          geocodeCoord,
          firstGeoObject;
     /*
      * Для работы геодекодера 2.1 нужен отдельный сервис, с уникальным ключом. Ключ вставляется в настройках.
      */
      $.ajax('https://geocode-maps.yandex.ru/1.x/?format=json&apikey=' + yamap_token + '&geocode=' + address.toString(), {
        method: 'GET'
      })
        .then(function (res) {
          firstGeoObject = res.response.GeoObjectCollection.featureMember[0].GeoObject;
          bounds = firstGeoObject.boundedBy;
          geocodeCoord = res.response.GeoObjectCollection.featureMember[0].GeoObject.Point.pos.split(' ');

          myMap = new ymaps.Map('map', {
            center: geocodeCoord,
            zoom: 9
          });

          var placemarksCollection = new ymaps.GeoObjectCollection({}, {
            draggable: false
          });
          var placemark = new ymaps.Placemark( [geocodeCoord[1], geocodeCoord[0] ], {balloonContent: baloonContent}, mapIcon, {draggable: false});
          placemarksCollection.add(placemark);
          myMap.geoObjects.add(placemarksCollection);

          myMap.setBounds(placemarksCollection.getBounds(), {
            checkZoomRange: true
          }).then(function () {
            placemark.balloon.open()
          });
        })
        .catch(function (err) {
          console.log(err)
        });
    }
  });
});
