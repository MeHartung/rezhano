/* 
 *  @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
define(function(require){
  var Backbone = require('backbone');
  
  var SHIPPING_TYPE = {
    CLSID_COURIER: 'f40d5e67-7957-4506-ad6e-a2e88e871cde',    
    CLSID_PICKUP: '4d42fb65-8d6d-443b-88c5-8f6419028300'
  };
  
  return Backbone.Model.extend({
    idAttribute: 'id',
    defaults: {
      clsid: SHIPPING_TYPE.CLSID_COURIER,
      cost: null,
      duration: null,
      selected: false
    }
  }, SHIPPING_TYPE);
});

