/* 
 *  @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
define(function(require){
  var ListView = require('view/base/list-view'),
      CourierShippingChoiceListItem = require('view/checkout/shipping/shipping-choice-list-item');
  
  return ListView.extend({
    itemView: CourierShippingChoiceListItem
  });
});

