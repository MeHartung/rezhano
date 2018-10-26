/**
 * Created by Денис on 23.06.2017.
 */
define(function(require){
  var Backbone = require('backbone');

  var template = _.template('\
 <span class="payment-info__title">В заказе 2 товара и 1 услуга:</span>\
 <div class="payment-info__product">\
    <span class="payment-info__product-name">Буррата</span>\
    <span class="payment-info__product-value">1 шт. × 169₽</span>\
  </div>\
 <div class="payment-info__product">\
    <span class="payment-info__product-name">Буррата</span>\
    <span class="payment-info__product-value">1 шт. × 169₽</span>\
 </div>\
 <span class="payment-info__discount">\
    Скидка 10%\
 </span>\
 <div class="payment-info__value">Итого\
  <span class="payment-info__cost"><%= Number(subtotal).toCurrencyString() %></span>\
 </div>');

  return Backbone.View.extend({
    className: 'cards-container__payment-info',
    initialize: function(){
      this.listenTo(this.model, 'change', this.render);
    },
    render: function(){
      this.$el.html(template({
        subtotal: this.model.get('subtotal')
      }));

      return this;
    }
  })
});