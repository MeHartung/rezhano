define(function(){
  //var _ = require('underscore');

  return _.template('\
  <a class="product-item__image product-page-link <% if (mountBg) { %>product-item__image_yellow<% } %> " title="<%= name %> купить" href="<%= url %>"> \
    <% if (image) { %>\n\
      <img src="<%= image %>" alt="Фотография товара <%= name %>"/>\n\
    <% } else { %>\n\
     <img src="/images/no_photo.png" alt="Нет фотографии"/>\n\
    <% } %>\n\
    <% /* < % if (isSale) { % >\
       <div class="product-item__discount-ticket">\
         <span>-< %= discountValue % >%</span>\
       </div>\
     < % } % > */ %>\
  </a>\
  <a href="<%= url %>" class="product-item__name  product-page-link"><%= name %></a>\n\
  <span class="product-item__type"><%= type %></span>\n\
  <div class="product-item__characteristics">\n\
  <% if (package) { %>\
    <span class="product-item__quantity"><%= package %> <%= units %> / </span>\n\
  <% } %>\
  <span class="product-item__price"><%= price %></span>\
  </div>\
  <% if (isPurchasable) { %>\
    <a class="addtocart-button button button_black button_add-to-cart button-buy_in-product" data-product-id="<%= id %>"><span>В корзину</span></a>\
    <div class="controls-wrap">\n\
       <span class="controls-title">количество</span>\n\
       <div class="product-item__controls quantity-widget quantity-wrap">\n\
          <a class="controls-item controls-item__increase"></a>\n\
          <input type="text" class="custom-input" value="1">\n\
          <a class="controls-item controls-item__reduce"></a>\n\
       </div>\
    </div>\
  <% } %>\n\
  ')
});