/**
 * Created by Dancy on 15.09.2017.
 */
define(function(require){
  var ModalDialogView = require('view/dialog/base/modal-dialog-view'),
      CityListView = require('view/common/header/city-list-view'),
      RegionSelectView = require('view/common/header/region-select-view'),
      RegionCollection = require('model/geography/region-collection'),
      CityCollection = require('model/geography/city-collection');

  var dialogTemplate = _.template('\
<div class="layer__close"></div>\
<div class="layer__in">\
 <div class="gkPopupWrap">\
    <div id="gkAjaxCart">\
      <h3>Выберите ваш город</h3>\
      <div class="region-select"></div>\
      <ul class="list-stores"></ul>\
      <div class="buttons-wrap">\
        <a class="button button-grey button-close" href="#">Закрыть</a>\
      </div>\
    </div>\
  </div>\
</div>\
');

  return ModalDialogView.extend({
    template: dialogTemplate,
    events: {
    'click .layer__close, .button-close': 'onCloseButtonClick'
    },
    initialize: function(options){
      this.location = options.location;

      ModalDialogView.prototype.initialize.apply(this, arguments);

      this.cityList = new CityCollection(ObjectCache.City, {
        region: "Свердловская обл."
      });
      this.regionList = new RegionCollection(ObjectCache.Region)

      this.regionSelectView = new RegionSelectView({
        collection: this.regionList,
        location: this.location
      });
      this.cityListView = new CityListView({
        collection: this.cityList,
        location: this.location
      });

      this.listenTo(this.regionSelectView, 'change', this.onRegionSelectChange);
      this.listenTo(this.cityList, 'fetch:start', this.onCityListFetchStart);
      this.listenTo(this.cityList, 'fetch:end', this.onCityListFetchEnd);
    },
    render: function(){
      this.$el.html(dialogTemplate({}));

      this.cityListView.setElement(this.$('.list-stores')).render();
      this.regionSelectView.setElement(this.$('.region-select')).render();

      return this;
    },
    onRegionSelectChange: function(name){
      this.cityList.region = name;
      this.cityList.fetch();
    },
    onCityListFetchStart: function(){
      this.$('.ajax-loader').show();
    },
    onCityListFetchEnd: function(){
      this.$('.ajax-loader').hide();
    }
  });
});