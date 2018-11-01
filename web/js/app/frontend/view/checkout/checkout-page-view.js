/**
 * Страница оформления заказа
 */
define(function(require){
  var CommonView = require('view/common/common-view'),
    ShippingMethodCollection = require('model/order/shipping/shipping-method-collection'),
    CheckoutForm = require('view/checkout/form');

  return CommonView.extend({
    events: {

    },
    initialize: function(options) {
      CommonView.prototype.initialize.apply(this, arguments);

      //Корзина
      this.order = options.cart;

      this.shippingMethodCollection = new ShippingMethodCollection(this.order.get('paymentMethods'));

      this.checkoutForm = new CheckoutForm({
        model: this.order
      });
    },
    render: function(){
      CommonView.prototype.render.apply(this, arguments);

      this.checkoutForm.setElement(this.$('#checkoutForm'));
      this.checkoutForm.render();

      return this;
    }
  })
});