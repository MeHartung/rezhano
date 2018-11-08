/* 
 *  @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
define(function(require){
  return '\
    <label>\n\
      <input type="radio" id="checkout_shippingMethod_<%= id %>" name="checkout[shippingMethod]" required="required" class="radio" value="<%= id %>" <% if (checked) {%> checked="checked" <% } %>>\n\
      <span class="custom-radio__radio"></span>\n\
      <span><%= name %> \
        <% if (!recipient_address) { %>\
          c <a class="delivery-address" data-address="<%= address %>"><%= show_address %></a>\
        <% } %>\
      </span>\n\
    </label>\n\
    <% if (cost) { %>\
      <span class="delivery-info">\n\
        /  от <%= cost %> ₽ <span class="delivery-info__message"><%= help %></span>\n\
      </span>\n\
    <% } %>\
    ';
});

