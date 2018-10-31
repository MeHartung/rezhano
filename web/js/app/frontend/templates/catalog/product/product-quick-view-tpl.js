define(function(require){
    return '\
<div class="layer__images">\n\
<% _.each(images, function(image) { %>  \n\
  <a href="" class="show-full-image">\n\
    <img src="/uploads/<%= image %>" alt="">\n\
  </a>\n\
<%});%></div>\
<div class="layer__text">\n\
<a href="<%= product_url %>" class="layer__title layer-title"><%= name %></a>\n\
<div class="layer__text-wrap scroll-pane">\n\
  <div class="layer__text-item">\n\
    <div class="layer__text-title">Тип:</div> \n\
    <div class="layer__text-text">это готовое самостоятельное блюдо. Можно посыпать солью, чёрным перцем, сбрызнуть оливковым маслом, и с помощью ломтика хлеба или крекера собирать жидкую начинку. А можно так же, как и Моцареллу —со свежими помидорами и базиликом, или овощами, пожаренными на гриле.</div> \n\
  </div>\n\
</div>\n\
<div class="product-item__characteristics">\n\
  <% if (package) { %>\n\
    <span class="product-item__quantity"><%= package %> <%= units %> / </span>\n\
  <% } %>\n\
  <span class="product-item__price"><%= price %></span>\n\
</div>\n\
<div class="product-item__info">\n\
    <% if (isPurchasable) { %> \n\
      <a class="addtocart-button button button_black button_add-to-cart">\n\
        <span>В корзину</span>\n\
      </a> \
      <span class="product-item__controls-title">количество</span>\n\
      <div class="product-item__controls"></div>\n\
    <% } %>\n\
</div> \n\
</div>\n\
';});