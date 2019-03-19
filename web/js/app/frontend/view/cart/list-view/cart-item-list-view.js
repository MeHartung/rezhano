/**
 * Created by Денис on 09.06.2017.
 */
define(function(require){
  var ListView = require('view/base/list-view'),
      CartCartItemListItemView = require('view/cart/list-view/cart-item-list-item'),
      CartCartItemListEmptyView = require('view/cart/list-view/cart-item-list-empty');

  var template = _.template('\
  <div class="cards-container__header">\
    <div class="cards-container__item cards-container__item_header">Описание</div>\
    <div class="cards-container__price cards-container__price_header"><span>Цена</span></div>\
    <div class="cards-container__quantity cards-container__quantity_header"><span>Количество</span></div>\
    <div class="cards-container__cost cards-container__cost_header"><span>Сумма</span></div>\
    <div class="cards-container__remove cards-container__remove_header"><span>Удалить</span></div>\
  </div>\
  <div class="cards-container__container"></div>\
  <div class="mobile-info__value">\
    Итого <span class="mobile-info__cost" data-cost="<%= totalCost %>"><%= total %> ₽</span>\
  </div>\
  <a href="<%= checkoutUrl %>" class="button button_black">\
    <span>Оформить заказ</span>\n\
  </a>\n\
  ');

  return ListView.extend({
    //tagName: 'table',
    initialize: function () {
      ListView.prototype.initialize.apply(this, arguments);

      this.cost = Number(ObjectCache.Cart.total).toCurrencyString('');
    },
    itemView: CartCartItemListItemView,
    template: template,
    container: '.cards-container__container',
    emptyView: CartCartItemListEmptyView,
    _templateData: function(){
      return {
        checkoutUrl : urlPrefix + '/checkout',
        total: Number(ObjectCache.Cart.total).toCurrencyString(''),
        totalCost: Number.parseInt(ObjectCache.Cart.total)
      };
    },
    render: function(){
      ListView.prototype.render.apply(this, arguments);

      return this;
    }
  });
});