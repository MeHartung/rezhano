define(function(require){
    return '\
<div class="layer__images">\n\
<% _.each(images, function(image) { %>  \n\
  <a href="<%= product_url %>" class="show-full-image">\n\
    <img src="<%= image %>" alt="">\n\
  </a>\n\
<%});%></div>\
<div class="layer__text ">\n\
    <a href="<%= product_url %>" class="layer__title layer-title"><%= name %></a>\n\
    <div class="layer__text-wrap scroll-pane">\n\
      <% if (attributes) { %>\n\
         <% _.each(attributes, function(attr, key) { %>\n\
           <div class="layer__text-item">\n\
             <div class="layer__text-title"><%= key %>:</div> \n\
             <div class="layer__text-text"><%= attr %></div> \n\
           </div>\n\
         <%});%>\n\
      <% } %>\n\
      <% if (description) { %>\n\
        <div class="layer__text-item">\n\
          <div class="layer__text-text" style="margin-top: 15px;display: inline-block;"><%= description %></div> \n\
        </div>\n\
      <% } %>\n\
    </div>\n\
    <div class="layer__text-wrap layer__text-wrap_mobile">\n\
      <% if (attributes) { %>\n\
         <% _.each(attributes, function(attr, key) { %>\n\
           <div class="layer__text-item">\n\
             <div class="layer__text-title"><%= key %>:</div> \n\
             <div class="layer__text-text"><%= attr %></div> \n\
           </div>\n\
         <%});%>\n\
      <% } %>\n\
      <% if (description) { %>\n\
        <div class="layer__text-item">\n\
          <div class="layer__text-text" style="margin-top: 15px;display: inline-block;"><%= description %></div> \n\
        </div>\n\
      <% } %>\n\
    </div>\n\
    <div class="product-item__characteristics">\n\
      <% if (package) { %>\n\
        <span class="product-item__quantity"><%= package %> <%= units %> / </span>\n\
      <% } %>\n\
      <span class="product-item__price"><%= price %></span>\n\
    </div>\n\
    <div class="product-item__info">\n\
        <% if (isPurchasable) { %> \n\
          <a class="addtocart-button button button_black button_add-to-cart button-buy_in-product " data-product-id="<%= productId %>">\n\
            <span>В корзину</span>\n\
          </a> \
          <span class="product-item__controls-title">количество</span>\n\
          <div class="product-item__controls"></div>\n\
        <% } %>\n\
    </div> \n\
</div>\n\
';});