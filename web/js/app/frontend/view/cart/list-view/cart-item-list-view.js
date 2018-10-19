/**
 * Created by Денис on 09.06.2017.
 */
define(function(require){
  var ListView = require('view/base/list-view'),
      CartCartItemListItemView = require('view/cart/list-view/cart-item-list-item');

  var template = _.template('\
<div class="cards-container__header">\n' +
      '                  <div class="cards-container__item cards-container__item_header"></div>\n' +
      '                  <div class="quantity-wrap quantity-wrap_header"><span>Кол-во</span></div>\n' +
      '                  <div class="cards-container__location cards-container__location_header"><span>Местонахождение</span></div>\n' +
      '                  <div class="cards-container__price cards-container__price_header"><span>Цена</span></div>\n' +
      '                </div>\
      \
      <div class="cards-container__container"></div>');

  return ListView.extend({
    //tagName: 'table',
    itemView: CartCartItemListItemView,
    template: template,
    container: '.cards-container__container'
  });
});