define(function(require){
  var ModalDialog = require('view/dialog/base/modal-dialog-view');

  require('ymaps');

  var template = _.template('\
    <h2>Адрес магазина</h2>\n\
    <div class="layer-map__address">г.<%= city %>, <%= address %></div>\
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
        city: this.model.get('city')
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
      var self = this;

      this.myMap = new ymaps.Map("map", {
        center: [55.76, 37.64],
        zoom: 7
      });

      this.preparePlacemarks();
      this.myMap.geoObjects.add(self.placemarkCollection);
      this.myMap.setBounds(self.placemarkCollection.getBounds(), {zoomMargin: [50], checkZoomRange: true});
    },
    preparePlacemarks: function () {
      var self = this;
      this.placemarkCollection = new ymaps.GeoObjectCollection();
      var placemark = new ymaps.Placemark(this.model.get('coordinates'));

      self.placemarkCollection.add(placemark);
    }
  });
});