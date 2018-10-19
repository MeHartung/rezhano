/* 
 * @author Denis N. Ragozin <ragozin at artsofte.ru>
 * @version SVN: $Id$
 * @revision SVN: $Revision$
 */
define(function(require){
  var Backbone = require('backbone'),
      PaymentMethod = require('model/order/pickup/pickup-point');

  var PickupDetailsCollection = Backbone.Collection.extend({
    model: PaymentMethod,
    
    hasSelected: function()
    {
      return (this.findWhere({selected: true})) !== null;
    }
           
    
  });


  
  
  return PickupDetailsCollection;
});