define(function(require){
  var ListView = require('view/base/list-view'),
      CartCartItemListItemView = require('view/user/list-view/order-item-list-item');

  return ListView.extend({
    itemView: CartCartItemListItemView
  });
});