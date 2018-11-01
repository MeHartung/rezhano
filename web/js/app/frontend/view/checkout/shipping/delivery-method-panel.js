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
      })
    },
    render: function(){
      this.shippingChoiceListView.setElement(this.$('.shipping-method-list'));
      this.shippingChoiceListView.render();

      this.$('#checkout_shipping_city_name').selectmenu();

      return this;
    },
    onCityChange: function (e) {
        $.ajax({
          url: urlPrefix + '/shipping/methods',
          type: 'POST',
          data: data,
          success: function (html) {
          console.log(html)
          }
        });
    }
  });
});

