/**
 * Created by Dancy on 15.09.2017.
 */
define(function(require){
  var Backbone = require('backbone'),
      CitySelectLayerView = require('view/common/header/city-select-layer');

  var template = _.template('\
<div class="current-city" id="current-city">\
  <% if (!isConfirmed) { %>\
    <div class="hint_city">\
      <div class="hint_city_content">Выбран ваш город?</div>\
      <div class="hint_button">\
        <a href="#" class="hint_button_close button" type="button">Да</a>\
        <a href="#" class="button button-grey hint_change_city">Нет</a>\
      </div>\
      <i class="corner"></i>\
    </div>\
   <% } %>\
  <img src="/images/city.png"><a href="#" class="dropdown cityselect"><%= city %></a>\
</div>');

  return Backbone.View.extend({
    events: {
      'click .cityselect': 'onClick',
      'click .hint_button_close': 'onLocationConfirmClick',
      'click .hint_change_city': 'onLocationChangeClick'
    },
    initialize: function(){
      this.citySelectLayer = new CitySelectLayerView({
        location: this.model
      });
      this.citySelectLayer.render().$el.appendTo($('body'));
    },
    render: function(){
      this.$el.html(template({
        city: this.model.city.get('name'),
        isConfirmed: this.model.get('isConfirmed')
      }));

      return this;
    },
    onClick: function(e){
      e.preventDefault();

      this.citySelectLayer.open();
    },
    onLocationChangeClick: function(e){
      e.preventDefault();

      this.citySelectLayer.open();
    },
    onLocationConfirmClick: function(e){
      e.preventDefault();

      this.model.confirm({
        reload: false
      });

      this.$('.hint_city').remove();
    }
  });
});