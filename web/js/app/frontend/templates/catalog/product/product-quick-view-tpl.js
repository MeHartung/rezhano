define(function(require){
    return '\
<div class="layer__images">\n\
<% _.each(images, function(image) { %>  \n\
  <a href="" class="show-full-image" itemprop="image" rel="vm-additional-images[]">\n\
    <img src="/uploads/<%= image %>" alt="">\n\
  </a>\n\
<%});%></div>\
<div class="layer__text">\n\
<a href="<%= product_url %>" class="layer__title layer-title"><%= name %></a>\n\
<div class="layer__text-wrap scroll-pane">\n\
  <div class="layer__text-item">\n\
  <% if (description) { %>\n\
    <div class="layer__text-title">С чем едят:</div> \n\
    <div class="layer__text-text"><%= description %></div> \n\
  <% } %>\n\
  </div>\n\
  <% if (attributes) { %>\n\
      <% _.each(attributes, function(attr, key) { %>\n\
          <div class="layer__text-item">\n\
              <div class="layer__text-title"><%= key %>:</div> \n\
              <div class="layer__text-text"><%= attr %></div> \n\
          </div>\n\
      <%});%>\n\
  <% } %>\
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