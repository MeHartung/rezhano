/* 
 *  @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
define(function(require){
  return '\
  <input type="radio" name="checkout[shipping_method_id]" id="checkout_shipping_method_id_<%= uid %>" value="<%= uid %>"\
   data-required  data-describedby="shipping-method-errors" data-description="shipping-method" <% if (selected) { %>checked<% } %>/>\n\
  <p class="shipping-choice-label-container">\n\
    <label for="checkout_shipping_method_id_<%= uid %>">\
    <span><%= name %><% if (cost) { %> (<%= cost %>)<% } %></span>\
    <% if(help){ %><span class="vmshipment_description"><%= help %></span><% } %>\
    </label>\n\
  </p>\n\
';
});

