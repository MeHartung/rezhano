define(function(){
  //var _ = require('underscore');

  return _.template('\
  <a class="product-item__image" title="<%= name %> купить" href="<%= url %>" \
    <% if (image) { %>\n\
      style="background: url(\'<%= image %>\') center no-repeat; background-size: cover">\n\
    <% } else { %>\n\
     style="background: url(/images/no_photo.png) center no-repeat">\n\
    <% } %>\n\
    <% if (isSale) { %>\
      <div class="product-item__discount-ticket">\
        <span>-<%= discountValue %>%</span>\
      </div>\
    <% } %>\
  </a>\
  <div class="product-item__info">\
    <a href="<%= url %>" class="product-item__title" ><%= name %></a>\
    <% if (isPurchasable) { %>\
        <button class="addtocart-button button button_add-to-cart" data-product-id="<%= id %>"><span><%= price %></span></button>\
    <% } %>\
  </div>\
')
});