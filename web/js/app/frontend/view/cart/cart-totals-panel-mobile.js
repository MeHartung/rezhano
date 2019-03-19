/**
 * Created by Денис on 23.06.2017.
 */
define(function(require){
  var Backbone = require('backbone');
  var ListView = require('view/base/list-view');

  var CartTotalsPanelListView = require('view/cart/cart-totals-panel-mobile-list-view');
 //
 //  var template = _.template('\
 // <span class="payment-info__title">В заказе 2 товара и 1 услуга:</span>\
 // <div class="payment-info__product">\
 //    <span class="payment-info__product-name">Буррата</span>\
 //    <span class="payment-info__product-value">1 шт. × 169₽</span>\
 //  </div>\
 // <div class="payment-info__product">\
 //    <span class="payment-info__product-name">Буррата</span>\
 //    <span class="payment-info__product-value">1 шт. × 169₽</span>\
 // </div>\
 // <span class="payment-info__discount">\
 //    Скидка 10%\
 // </span>\
 // <div class="payment-info__value">Итого\
 //  <span class="payment-info__cost"><%= Number(subtotal).toCurrencyString() %></span>\
 // </div>');

  var template = _.template('\
   <div class="payment-info__product-wrap"></div>\
  <% if (shippingCost) { %>\
    <div class="payment-info__product">\n\n  \
       <span class="payment-info__product-name">Доставка</span>\n\n \
       <span class="payment-info__product-value"> 1 шт. × <%= shippingCount %>₽</span>\n\n\
    </div>\n\
  <% } %>\n\
 <div class="payment-info__value">Итого\n\
   <% if (shippingCost) { %>\n\
     <span class="payment-info__cost"><%= Number(total).toCurrencyString() %></span>\n\
   <% } else { %>\n\
     <span class="payment-info__cost"><%= Number(subtotal).toCurrencyString() %></span>\n\
   <% } %>\
 </div>');

  return ListView.extend({
    className: 'cards-container__payment-info',
    initialize: function(){
      var self = this;
      this.listenTo(this.model, 'change', this.render);

      this.cartProductListView = new CartTotalsPanelListView({
        collection: new Backbone.Collection(self.model.get('order_items'))
      })
    },
    render: function(){
      this.$el.html(template({
        subtotal: this.model.get('subtotal'),
        shippingCost: this.model.get('shippingCost'),
        shippingCount: this.model.get('shipping_cost'),
        total: Number(this.model.get('subtotal') )+ Number(this.model.get('shipping_cost'))
      }));

      this.cartProductListView.setElement(this.$('.payment-info__product-wrap')).render();

      return this;
    }
  })
});