define(function(require){
  return '\
<span class="controls-title">количество</span>\
<% if (showButtons) { %><a class="controls-item controls-item__increase quantity-control quantity-control__down"></a><% } %>\n\
<input class="custom-input quantity-control__input" type="text" value="<%= quantity %>" placeholder="1">\n\
<% if (showButtons) { %><a class="controls-item controls-item__reduce quantity-control quantity-control__up"></a><% } %>\
<% if (product_stock) { %><span class="quantity-balance">всего <%= product_stock %> шт.</span><% } %>';
});

