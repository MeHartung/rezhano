/* 
 * @author Denis N. Ragozin <ragozin at artsofte.ru>
 * @version SVN: $Id$
 * @revision SVN: $Revision$
 */
define(function(require){
  var Backbone = require('backbone'),
      PaymentMethod = require('model/order/payment/method/payment-method');

  var PaymentMethodCollection = Backbone.Collection.extend({
    url: urlPrefix + '/payment/methods',
    model: PaymentMethod,
    initialize: function(){
      this.isLoaded = false;
    },
    hasSelected: function(){
      return (this.findWhere({selected: true})) !== null;
    },
    fetch: function(options){
      var self = this;
      options || (options = {});
      
      var previousComplete = options.complete || null;
      this.trigger('fetch:start');

      options.complete = function(){
        self.trigger('fetch:end');
        self.isLoaded = true;
        if ($.isFunction(previousComplete)){
          previousComplete.apply(this, arguments);
        }
      }
      
      return Backbone.Collection.prototype.fetch.call(this, options);
    },
    setLoaded: function(v){
      this.isLoaded = !!v;
    }
  });


  
  
  return PaymentMethodCollection;
});