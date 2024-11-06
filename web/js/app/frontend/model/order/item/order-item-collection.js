/* 
 *  @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
define(function(require){
  var Backbone = require('backbone'),
      OrderItem = require('model/order/item/order-item');
  
  return Backbone.Collection.extend({
    model: OrderItem
  });
});

