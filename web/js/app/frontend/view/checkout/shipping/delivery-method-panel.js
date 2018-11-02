/* 
 * @author Denis N. Ragozin <ragozin at artsofte.ru>
 * @version SVN: $Id$
 * @revision SVN: $Revision$
 */
define(function(require){
  var Backbone = require('backbone'),
      ShippingChoiceCollection = require('model/shipping/shipping-choice-collection'),
      ShippingChoiceListView = require('view/checkout/shipping/shipping-choice-list-view'),
      ListView = require('view/base/list-view');

  return Backbone.View.extend({
    events: {
      'change #checkout_shipping_city_name': 'onCityChange',
      'selectmenuchange #checkout_shipping_city_name': 'onCityChange'
    },
    initialize: function () {
      this.shippingChoiceListView = new ShippingChoiceListView({
        collection: this.collection
      });

      this.listenTo(this.collection, 'add', this.onShippingMethodCollectionChange);
      this.listenTo(this.collection, 'remove', this.onShippingMethodCollectionChange);
      this.listenTo(this.collection, 'reset', this.onShippingMethodCollectionChange);
      this.listenTo(this.collection, 'change:is_active', this.onShippingMethodChange);
    },
    render: function(){
      this.shippingChoiceListView.setElement(this.$('.shipping-method-list'));
      this.shippingChoiceListView.render();

      this.$('#checkout_shipping_city_name').selectmenu();

      this.onShippingMethodChange(this.collection.findWhere({'is_active': true}));

      return this;
    },
    onCityChange: function (e) {
      var self = this;
        $.ajax({
          url: urlPrefix + '/shipping/methods',
          type: 'POST',
          data: {city: $(e.currentTarget).val()},
          success: function (r) {
            self.collection.set(r)
          }
        });
    },
    onShippingMethodCollectionChange: function () {
    if (this.collection.length > 1) {
      this.shippingChoiceListView.$el.show();
    } else {
      this.shippingChoiceListView.$el.hide();
    }

      this.onShippingMethodChange(this.collection.findWhere({'is_active': true}));
    },
    onShippingMethodChange: function (method) {
      if (method.get('options')['recipient_address_required']) {
        this.enableAddressField();
      } else {
        this.disableAddressField();
      }
    },
    disableAddressField: function () {
      this.trigger('disableAddressValidation');
      this.$('#shipping-address-input').hide();
    },
    enableAddressField: function () {
      this.trigger('enableAddressValidation');
      this.$('#shipping-address-input').show();
    }
  });
});

