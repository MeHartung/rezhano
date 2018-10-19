/**
 * Created by Денис on 23.05.2017.
 */

define(function(require){
  var Backbone = require('backbone'),
      CartItem = require('model/cart/cart-item');

  return Backbone.Collection.extend({
    url: urlPrefix + '/cart/items',
    model: CartItem
  });
});