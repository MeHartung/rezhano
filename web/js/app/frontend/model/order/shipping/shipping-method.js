/* 
 *  @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
define(function(require){
  var Backbone = require('backbone'),
      PickupDetailsCollection = require('model/order/pickup/pickup-details-collection'),
      ShippingChoiceCollection = require('model/shipping/shipping-choice-collection');
  
  return Backbone.Model.extend({
    defaults:{
      name: '',
      selected: false,
      details: null
    },
    initialize: function() {
           
      if (!this.has('details') || !(this.get('details') instanceof PickupDetailsCollection)) {
        var departments = this.get('details') ? this.get('details').departments : [];
        this.set('details', { departments: new PickupDetailsCollection(departments || [])} );
      }
      
      this.shippingChoices = new ShippingChoiceCollection();
      this.shippingChoices.on('all', this._onShippingChoiceCollectionEvent, this);
      this.shippingChoices.url = urlPrefix + '/shipping/methods/'+this.get('id')+'/choices';
    },
    fetchShippingChoices: function(){
      if (!this.get('deferredEstimate')) {
        this.shippingChoices.reset(this.get('choices'));
        this._onShippingChoiceCollectionLoaded();
      }
      else
      {
        var self = this;
        this.shippingChoices.fetch(
          {
              reset:true
          },
          {
          complete: function(){
            self._onShippingChoiceCollectionLoaded();
          }
        });
      }
    },
    _onShippingChoiceCollectionEvent: function(){
      var args = Array.prototype.slice.call(arguments, 0);
      args[0] = 'choices:'+args[0];
      
      this.trigger.apply(this, args);
    },
    _onShippingChoiceCollectionLoaded: function(){
      this.trigger('choices:loaded', this );
    }
  }, {
    CLSID_SHIPPING: 'f40d5e67-7957-4506-ad6e-a2e88e871cde',    
    CLSID_PICKUP: '4d42fb65-8d6d-443b-88c5-8f6419028300'
  });
});

