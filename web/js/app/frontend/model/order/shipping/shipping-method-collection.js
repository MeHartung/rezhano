/* 
 *  @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
define(function(require){
  var Backbone = require('backbone'),
      ShippingMethod = require('model/order/shipping/shipping-method');
  
  return Backbone.Collection.extend({
    url: urlPrefix + '/shipping/methods',
    model: ShippingMethod,
    initialize: function(){
      this.isLoaded = false;
      this.on('change:is_active', this.onChangeActive, this);
    },
    onChangeActive: function(model){
      if (model.get('is_active') && !model.previousAttributes().is_active){
        this.each(function(_model){
          if (_model.cid !== model.cid){
            _model.set({ 'is_active': false })
          }
        });
      }
    },
    comparator: function(method){
      return -method.get('priority');
    },
    hasSelected: function()
    {
      return (this.findWhere({selected: true})) !== null;
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
    },
    getPickupMethods: function(){
      return this.findWhere({ clsid: ShippingMethod.CLSID_PICKUP });
    },
    getCourierMethods: function(){
      return this.findWhere({ clsid: ShippingMethod.CLSID_COURIER });
    },
    setLoaded: function(v){
      this.isLoaded = !!v;
    }
  });
});

