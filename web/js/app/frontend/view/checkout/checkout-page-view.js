/**
 * Страница оформления заказа
 */
define(function(require){
  var CommonView = require('view/common/common-view'),
    CheckoutForm = require('view/checkout/form'),
    TotalsPanelView = require('view/cart/cart-totals-panel'),
    MobileTotalsPanelView = require('view/cart/cart-totals-panel-mobile');

  return CommonView.extend({
    events: {
      'keydown .input-textarea' : 'autosizeR',
      'click .button-question': 'onQuestionClick',
    },
    initialize: function(options) {
      CommonView.prototype.initialize.apply(this, arguments);

      //Корзина
      this.order = options.cart;

      this.shippingCost = false;

      this.checkoutForm = new CheckoutForm({
        model: this.order
      });
      this.totalsPanelView = new TotalsPanelView({
        model: this.order,
      });

      this.mobileTotalsPanelView = new MobileTotalsPanelView({
        model: this.order,
      });

      this.listenTo( this.checkoutForm, 'enableShipping', this.enableShipping);
      this.listenTo( this.checkoutForm, 'disableShipping', this.disableShipping);

      $(window).on('scroll.'+this.cid, $.proxy(this.onWindowScroll, this));
    },
    render: function(){
      CommonView.prototype.render.apply(this, arguments);

      this.checkoutForm.setElement(this.$('#checkoutForm'));
      this.checkoutForm.render();

      this.totalsPanelView.setElement(this.$('.cards-container__payment-info-wrap'));
      this.totalsPanelView.render();

      this.mobileTotalsPanelView.setElement(this.$('.mobile-payment-info-wrap'));
      this.mobileTotalsPanelView.render();

      return this;
    },
    onWindowScroll: function () {
      var scroll = $(window).scrollTop();
      var max = $('.section-purchase').height();
      if(scroll > 205) {
        this.$('.cards-container__payment-info').css({
          position: "fixed",
          top: 95
        });
        if(scroll < max) {
        } else {
          this.$('.cards-container__payment-info').css({
            position: "fixed",
            top: 95
          });
        }
      } else {
        this.$('.cards-container__payment-info').css({
          position: "relative",
          top: 0
        });
      }
    },
    enableShipping: function () {
      this.shippingCost = true;
      this.order.set('shippingCost', this.shippingCost )
    },
    disableShipping: function () {
      this.shippingCost = false;
      this.order.set('shippingCost', this.shippingCost )
    },
    autosizeR: function (e){
      var el = e.currentTarget;
      setTimeout(function(){
        el.style.cssText = 'height:auto; padding:0';
        el.style.cssText = 'height:' + (el.scrollHeight+4) + 'px';
      },0);
    },
  })
});