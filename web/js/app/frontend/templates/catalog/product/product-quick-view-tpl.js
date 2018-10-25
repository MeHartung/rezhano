define(function(require){
    return '' +
'<div class="layer__images"></div>' +
'<div class="layer__text">' +
'<h2 class="layer__title"><%= name %></h2>' +
'PRODUCT_QUICK_VIEW' +
'<div class="product-item__characteristics"></div>' +
'<% if (isPurchasable) { %> ' +
'  <a class="addtocart-button button button_add-to-cart"><span>В корзину</span></a> ' +
'  <div class="product-item__controls"></div>' +
'<% } %>' +
'</div>';
});