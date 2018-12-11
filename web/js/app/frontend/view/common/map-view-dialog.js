define(function(require){
  var ModalDialog = require('view/dialog/base/modal-dialog-view');

  require('ymaps');

  var template = _.template('\
    <div class="layer-map__address"><%= address %></div>\
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
    initialize: function(options){
      ModalDialog.prototype.initialize.apply(this, arguments);
      this.address = options.address;

      this.myMap = null;
    },
    render: function(){
      // ModalDialog.prototype.render.apply(this, arguments);

      this.$el.html(template({
        address: this.model.get('address'),
        city: this.model.get('city') ? this.model.get('city') : ''
      }));

      return this;
    },
    show: function(){
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
        iconImageOffset:[-25, -67]
      };

      var baloonContent = ('<h4>'+ this.model.get('address') +'</h4>');

      var address = this.model.get('address');
      ymaps.geocode( address.toString() ).then(
        function (res) {
          var geoObj = res.geoObjects.get(0),
            bounds = geoObj.properties.get('boundedBy');
          var placemark = new ymaps.Placemark(geoObj.geometry.getCoordinates(), {balloonContent: baloonContent}, mapIcon, {draggable: false});
          this.myMap = new ymaps.Map("map", {
            center: geoObj.geometry.getCoordinates(),
            zoom: 7
          });
          this.myMap.geoObjects.add(placemark);
          this.myMap.setBounds(bounds, {
            zoomMargin: [50],
            checkZoomRange: true
          });
        }
      );
    }
  });
});