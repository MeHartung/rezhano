/**
 * Created by Dancy on 15.09.2017.
 */
define(function(require){
  var Backbone = require('backbone'),
      City = require('model/geography/city');

  return Backbone.Collection.extend({
    model: City,
    url: function(){
      return urlPrefix + '/api/geography/cities?region='+encodeURIComponent(this.region)
    },
    initialize: function(models, options){
      this.region = options.region;
    },
    fetch: function(options){
      var self = this;
      options || (options = {});

      var previousComplete = options.complete || null;
      this.trigger('fetch:start');

      options.complete = function(){
        self.isLoaded = true;
        self.trigger('fetch:end');
        if ($.isFunction(previousComplete)){
          previousComplete.apply(this, arguments);
        }
      };

      return Backbone.Collection.prototype.fetch.call(this, options);
    }
  });
});