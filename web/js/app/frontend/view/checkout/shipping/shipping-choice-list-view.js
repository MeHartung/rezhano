/* 
 *  @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
define(function(require){
  var ListView = require('view/base/list-view'),
      CourierShippingChoiceListItem = require('view/checkout/shipping/shipping-choice-list-item');
  
  return ListView.extend({
    itemView: CourierShippingChoiceListItem,
    _createItemView: function(item, index){
      return new this.itemView({ model: item, index: index, cart: this.options.cart });
    }
  });
});

