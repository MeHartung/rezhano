define(function(){
  //var _ = require('underscore');

  return _.template('\
  <a class="product-item__image" title="<%= name %> купить" href="<%= url %>"> \
    <% if (image) { %>\n\
      <img src="<%= image %>" alt="Фотография товара <%= name %>"/>\n\
    <% } else { %>\n\
     <img src="/images/no_photo.png" alt="Нет фотографии"/>\n\
    <% } %>\n\
    <% if (isSale) { %>\
      <div class="product-item__discount-ticket">\
        <span>-<%= discountValue %>%</span>\
      </div>\
    <% } %>\
  </a>\
  <a href="<%= url %>" class="product-item__name" ><%= name %></a>\n\
  <span class="product-item__type">мягкий сыр</span>\n\
  <div class="product-item__characteristics">\
    <span class="product-item__quantity">300 г  / </span>\n' +
'   <span class="product-item__price"><%= price %></span>\
  </div>\
  <% if (isPurchasable) { %>\
    <a class="addtocart-button button button_black button_add-to-cart" data-product-id="<%= id %>"><span>В корзину</span></a>\
    <div class="product-item__controls">\n' +
'        <span class="controls-title">количество</span>\n' +
'        <a class="controls-item controls-item__increase"></a>\n' +
'        <input type="text" class="custom-input" value="1">\n' +
'        <a class="controls-item controls-item__reduce"></a>\n' +
'    </div>\
  <% } %>\n')
});