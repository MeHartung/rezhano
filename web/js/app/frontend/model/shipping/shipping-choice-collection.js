/* 
 *  @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
define(function(require){
  var Backbone = require('backbone'),
      ShippingChoice = require('model/shipping/shipping-choice');
  
  return Backbone.Collection.extend({
    model: ShippingChoice,
    comparator: function(a, b){
      var aCost = a.get('cost'),
          bCost = b.get('cost'),
          aPriority = a.get('priority'),
          bPriority = b.get('priority');
      
      if (aCost === bCost){
        return aPriority >= bPriority ? (aPriority === bPriority ? 0 : -1) : 1;
      } else {
        if (null === aCost){
          return 1;
        }
        if (null === bCost){
          return -1;
        }
        return aCost < bCost ? -1 : 1;
      }
      
    },
    selectItem: function(id){
      this.each(function(item){
        item.set('selected', id == item.get('id'));
      })
    }
  });
});

