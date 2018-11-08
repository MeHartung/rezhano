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
      this.activeMethod = this.collection.findWhere({'is_active': true});
      this.onShippingMethodChange(this.activeMethod);

      return this;
    },
    onCityChange: function (e) {
      var self = this;
      var city = $(e.currentTarget).val();
      $.ajax({
        url: urlPrefix + '/shipping/methods',
        type: 'POST',
        data: {city: city},
        success: function (r) {
          self.collection.reset(r);

          if (city === 'Другой город') {
            self.$('.another-city-text').show()
          } else {
            self.$('.another-city-text').hide()
          }
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
      if (method && method.get('options')['recipient_address_required']) {
        this.enableAddressField();
        this.trigger('enableShipping');
      } else {
        this.disableAddressField();
        this.trigger('disableShipping');
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

