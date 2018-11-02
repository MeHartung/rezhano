/* 
 *  @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
define(function(require){
  return '\
    <label>\n\
      <input type="radio" id="checkout_shippingMethod_<%= id %>" name="checkout[shippingMethod]" required="required" class="radio" value="<%= id %>" <% if (checked) {%> checked="checked" <% } %>>\n\
      <span class="custom-radio__radio"></span>\n\
      <span><%= name %></span>\n\
    </label>\n\
';
});

