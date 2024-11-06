/* 
 *  @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
define(function(require){
  var Backbone = require('backbone');
  
  var template = _.template(require('templates/checkout/shipping/courier-shipping-unavailable-alert'));
  
  return Backbone.View.extend({
    className: 'delivery-tab',
    attributes: {
      id: 'delivery-tab-courier',
      style: 'display: none;'
    },
    render: function(){
      this.$el.html(template({
        minimalOrderCostForCourierDelivery: ObjectCache.CheckoutFormOptions[0].minimal_cost_for_shipping
      }));
      
      return this;  
    } 
  });
});

