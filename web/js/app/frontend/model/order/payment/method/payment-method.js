/* 
 * @author Denis N. Ragozin <ragozin at artsofte.ru>
 * @version SVN: $Id$
 * @revision SVN: $Revision$
 */
define(function(require){
  var Backbone = require('backbone');

  var PaymentMethod = Backbone.Model.extend({
    defaults:{
      name: '',
      driver_id: null,
      selected: false,
      details: null
    },
    initialize: function(){
      
    }
  });

  return PaymentMethod;
});