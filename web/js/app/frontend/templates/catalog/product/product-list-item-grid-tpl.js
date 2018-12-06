define(function(){
  //var _ = require('underscore');

  return _.template('\
  <div class="product-link-wrap">\
    <a class="product-item__image product-page-link" title="<%= name %> купить" href="<%= url %>"> \n\
      <% if (image) { %>\n\
        <img src="<%= image %>" alt="Фотография товара <%= name %>"/>\n\
      <% } else { %>\n\
        <img src="/images/no_photo.png" alt="Нет фотографии"/>\n\
      <% } %>\n\
      <% if (isNovice) { %>\n\
        <span class="product-item__label label-news"></span>\n\
      <% } %>\n\
      <% if (isSale) { %>\n\
          <span class="product-item__label label-action"></span>\n\
      <% } %>\n\
    </a>\n\
    <a href="<%= url %>" class="product-item__name  product-page-link"><%= name %></a>\n\
  </div>\
  <span class="product-item__type"><%= short_description %></span>\n\
  <div class="product-item__characteristics">\n\
  <% if (package) { %>\
    <span class="product-item__quantity"><%= package %> <%= units %> / </span>\n\
  <% } %>\
  <span class="product-item__price"><%= price %></span>\
  </div>\
  <% if (isPurchasable) { %>\
    <a class="addtocart-button button button_black button_add-to-cart button-buy_in-product button_yellow-yellow" data-product-id="<%= id %>"><span>В корзину</span></a>\
    <div class="controls-wrap">\n\
       <span class="controls-title">количество</span>\n\
       <div class="product-item__controls quantity-widget quantity-wrap">\n\
          <a class="controls-item controls-item__increase"></a>\n\
          <input type="text" class="custom-input" value="1">\n\
          <span class="quantity-widget_units"><%= units %></span>\n\
          <a class="controls-item controls-item__reduce"></a>\n\
       </div>\
    </div>\
  <% } %>\n\
  ')
});