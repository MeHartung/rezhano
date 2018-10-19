define(function(require){
  var Backbone = require('backbone'),
    Product = require('model/catalog/product/product');

  return Backbone.View.extend({
    className: 'button button_add-to-cart',
    events: {
      'click': 'onClick'
    },
    initialize: function(options){
      this.cart = options.cart;
    },
    render: function(){
      return this;
    },
    onClick: function(e){
      var self = this,
        cart = this.cart,
        cartItem = this.cart.createItem({
          product_id: this.model.get('id') ,
          quantity: 1,
        });
      cartItem
        .save()
        .done(function(){
          cart.items.set(cartItem, {
            remove: false
          });
        });
    }
  });
});