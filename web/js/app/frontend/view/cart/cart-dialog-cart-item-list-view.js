/**
 * Created by Денис on 06.06.2017.
 */
define(function(require){
  var ListView = require('view/base/list-view'),
      CartDialogCartItemListItemView = require('view/cart/cart-dialog-cart-item-list-item-view');

  return ListView.extend({
    tagName: 'table',
    itemView: CartDialogCartItemListItemView
  })
})