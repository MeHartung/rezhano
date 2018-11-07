/**
 * Страница оформления заказа
 */
define(function(require){
  var CommonView = require('view/common/common-view'),
    CheckoutForm = require('view/checkout/form');

  return CommonView.extend({
    initialize: function(options) {
      CommonView.prototype.initialize.apply(this, arguments);

      //Корзина
      this.order = options.cart;

      this.checkoutForm = new CheckoutForm({
        model: this.order
      });
      $(window).on('scroll.'+this.cid, $.proxy(this.onWindowScroll, this));
    },
    render: function(){
      CommonView.prototype.render.apply(this, arguments);

      this.checkoutForm.setElement(this.$('#checkoutForm'));
      this.checkoutForm.render();

      return this;
    },
    onWindowScroll: function () {
      var scroll = $(window).scrollTop();
      var max = $('.section-purchase').height();
      var final = 0;
      if(scroll > 200) {
        if(scroll < max) {
          final = scroll - 250;
        } else {
          final = max - 200;
        }
      } else {
        final = 0;
      }
      this.$('.cards-container__payment-info').css('top', final + "px");
    }
  })
});