/**
 * Created by Денис on 23.05.2017.
 */

define(function(require){
  var Backbone = require('backbone'),
      Order = require('model/order/order'),
      CartItem = require('model/cart/cart-item'),
      CartItemCollection = require('model/cart/cart-item-collection');

  return Order.extend({
    initialize: function () {
      Order.prototype.initialize.apply(this, arguments);

      this.items.on('add', this.onCartItemAdded, this)
      this.items.on('remove', this.onCartItemRemoved, this)
      this.items.on('change:quantity', this.onCartItemQuantityChange, this);

      this.on('change:shipping_cost', this.updateTotals);
      this.on('change:fee', this.updateTotals);
    },
    onCartItemAdded: function(model){
      this.trigger('add', model);

      this.updateTotals();
    },
    onCartItemQuantityChange: function(model){
      if (model.previousAttributes().quantity < model.attributes.quantity){
        this.trigger('add', model);
      }

      this.updateTotals();
    },
    createItem: function(values){
      return new CartItem(values);
    },
    updateTotals: function(){
      var totalQuantity = 0,
          totalCost = 0;

      this.items.each(function(item){
        totalQuantity += item.get('quantity');
        totalCost += item.get('cost');
      });

      this.set({
        quantity: totalQuantity,
        subtotal: totalCost,
        total: totalCost + Number(this.get('shipping_cost') || 0) + Number(this.get('fee') || 0)
      })
    },
    createOrderItemCollection: function(){
      return new CartItemCollection(this.get('order_items'));
    },
    onCartItemRemoved: function(model){
      this.updateTotals();
    }
  });
});