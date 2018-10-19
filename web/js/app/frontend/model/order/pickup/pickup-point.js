/* 
 * @author Denis N. Ragozin <ragozin at artsofte.ru>
 * @version SVN: $Id$
 * @revision SVN: $Revision$
 */
define(function(require){
  var Backbone = require('backbone');

  var PickupPoint = Backbone.Model.extend({
    defaults:{
      name: '',
      selected: false,
      address: ''
    },
    initialize: function(){

    }
  });

  return PickupPoint;
});