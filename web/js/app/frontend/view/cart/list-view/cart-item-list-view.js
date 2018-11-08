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
  <a href="{{ url(\'checkout\') }}" class="button button_black"><span>Оформить заказ</span>\n\</a>\
     ');

  return ListView.extend({
    //tagName: 'table',
    itemView: CartCartItemListItemView,
    template: template,
    container: '.cards-container__container',
    emptyView: CartCartItemListEmptyView,
    _templateData: function(){
      return {
        checkoutUrl : urlPrefix + '/checkout'
      };
    }
  });
});