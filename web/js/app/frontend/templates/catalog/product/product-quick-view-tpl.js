define(function(require){
    return '' +
'<div class="layer__images">' +
'<% _.each(images, function(image) { %>  ' +
'  <a href="" class="show-full-image">\n' +
'    <img src="/uploads/<%= image %>" alt="">\n' +
'  </a>\n' +
'<%});%></div>' +
'<div class="layer__text">' +
'<a href="<%= product_url %>" class="layer__title layer-title"><%= name %></a>' +
'PRODUCT_QUICK_VIEW<br>' +
'<div class="layer__text-wrap scroll-pane">' +
'  <div class="layer__text-item">' +
'    <div class="layer__text-title">Тип:</div> \n' +
'    <div class="layer__text-text">это готовое самостоятельное блюдо. Можно посыпать солью, чёрным перцем, сбрызнуть оливковым маслом, и с помощью ломтика хлеба или крекера собирать жидкую начинку. А можно так же, как и Моцареллу —со свежими помидорами и базиликом, или овощами, пожаренными на гриле.</div> \n' +
'  </div>\n'+
'</div>\n'+
'<div class="product-item__characteristics">' +
'  <span class="product-item__quantity">300 г  / </span>\n' +
'  <span class="product-item__price">1&nbsp;200&nbsp;₽</span>\n' +
'</div>' +
'<div class="product-item__info">' +
    '<% if (isPurchasable) { %> ' +
    '  <a class="addtocart-button button button_black button_add-to-cart">' +
    '    <span>В корзину</span>' +
    '  </a> ' +
    '  <span class="product-item__controls-title">количество</span>' +
      '<div class="product-item__controls"></div>' +
    '<% } %>' +
'</div> \n' +
'</div>';
});