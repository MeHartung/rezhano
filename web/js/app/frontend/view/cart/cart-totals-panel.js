/**
 * Created by Денис on 23.06.2017.
 */
define(function(require){
  var Backbone = require('backbone');
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

  var template = _.template('<% _.each(order_items, function(order_item){ %>\n\
    <div class="payment-info__product">\n\
       <span class="payment-info__product-name"> <%= order_item.name %></span>\n\
       <span class="payment-info__product-value"> <%= order_item.quantity %> <%= order_item.product.units %> × <%= order_item.product.price %>₽</span>\n\
    </div>\n\
  <% }) %>\n\
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

  return Backbone.View.extend({
    className: 'cards-container__payment-info',
    initialize: function(){
      this.listenTo(this.model, 'change', this.render);
    },
    render: function(){
      this.$el.html(template({
        subtotal: this.model.get('subtotal'),
        order_items: this.model.get('order_items'),
        shippingCost: this.model.changed.shippingCost,
        shippingCount: this.model.get('shipping_methods').find(ship => ship.uid === 'eac20e0f-056a-4c10-9f43-7bee5c47167a').cost,
        total: Number(this.model.get('subtotal') )+ Number(this.model.get('shipping_methods').find(ship => ship.uid === 'eac20e0f-056a-4c10-9f43-7bee5c47167a').cost)
      }));

      return this;
    }
  })
});