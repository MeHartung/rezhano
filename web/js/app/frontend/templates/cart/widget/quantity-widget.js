define(function(require){
  return '<% if (showButtons) { %><a class="quantity-control quantity-control__down"></a><% } %>\n\
          <input class="quantity-control__input" type="text" value="<%= quantity %>" placeholder="1">\n\
          <% if (showButtons) { %><a class="quantity-control quantity-control__up"></a><% } %>\
          <% if (product_stock) { %><span class="quantity-balance">всего <%= product_stock %> шт.</span><% } %>';
});

