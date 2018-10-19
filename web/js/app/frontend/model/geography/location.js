/**
 * Created by Dancy on 15.09.2017.
 */
define(function(require){
  var Backbone = require('backbone'),
      City = require('model/geography/city');

  return Backbone.Model.extend({
    defaults: {
      isConfirmed: false
    },
    initialize: function(attributes, options){
      this.city = new City('undefined' !== typeof attributes.city ? attributes.city : {});
    },
    parse: function(){
      var parsed = Backbone.Model.prototype.parse.call(this, arguments)[0];

      if ('undefined' !== typeof parsed.city){
        this.city.set(parsed.city);

        delete parsed.city;
      }

      return parsed;
    },
    setCity: function(city, options){
      this.city.set(city.attributes);

      this.confirm(options);
    },
    confirm: function(options){
      var options = $.extend({
        reload: true
      }, options);

      document.cookie = "city=" + this.city.get('code') + ';path=/';

      if (options.reload){
        window.location.reload();
      }
    }
  });
});