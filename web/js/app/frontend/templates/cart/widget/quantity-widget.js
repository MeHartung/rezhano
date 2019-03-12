define(function(require){
  return '\
<span class="controls-title"></span>\
<% if (showButtons) { %><a class="controls-item controls-item__increase quantity-control quantity-control__down" href="#"></a><% } %>\n\
<input class="custom-input quantity-control__input" type="text" value="<%= quantity %>" placeholder="1" readonly>\n\
<% if (units) { %>\
  <span class="quantity-widget__units"><%= units %></span>\n\
<% } %>\
<% if (showButtons) { %><a class="controls-item controls-item__reduce quantity-control quantity-control__up" href="#"></a><% } %>'
});

// <% if (product_stock) { %><span class="quantity-balance"><%= product_stock %></span><% } %>';