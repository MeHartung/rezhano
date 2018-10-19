define(function(require){
  var Backbone = require('backbone'),
      OrderItemCollection = require('model/order/item/order-item-collection');

  return Backbone.Model.extend({
    urlRoot: urlPrefix + '/profile/history/order',
    defaults: {
      active:false
    },
    initialize: function () {
      this.set({ items:  new Backbone.Collection(this.get('order_items') || [])});
    }
  });
});