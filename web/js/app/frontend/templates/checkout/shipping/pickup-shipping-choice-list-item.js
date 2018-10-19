/* 
 *  @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
define(function(require){
  return '\
<td>\n\
  <input type="radio" name="order[delivery_method_id]" id="order_delivery_method_id_<%= id %>" value="<%= uid %>" data-address="<%= address %>" data-name="<%= name %>" <% if (specregionDepartmentId) { %>data-department-id="<%= specregionDepartmentId %>"<% } %>\
    data-required  data-describedby="delivery-method-errors" data-description="delivery-method"/>\n\
  <label for="order_delivery_method_id_<%= id %>"><%= name %></label>\n\
  <br/>\n\
  <p class="pickup-point-address-container">\n\
    <% if (geocoordinates) { %>\n\
        <a href="#" title="Посмотреть на карте" class="dashed map-link pickup-address" data-geocoordinates="<%= geocoordinates %>"><%= address %></a>\n\
    <% } else { %>\n\
        <span class="pickup-address"><%= address %></span>\n\
    <% } %>\n\
  </p>\n\
  <div class="work_hours_table_cover" style="display:none">\n\
    <table class="work_hours_table">\n\
      <% if (timetable) { %>\n\
        <tr>\n\
          <td>Режим работы:</td>\n\
          <td><p><%= timetable.replace(\/(?:\\r\\n\|\\r\|\\n)\/g, \'</p><p>\') %></p></td>\n\
        </tr>\n\
      <% } %>\n\
      <% if (phone) { %>\n\
        <tr>\n\
            <td>Телефон:</td>\n\
            <td>\n\
                <p><%= phone.replace(\/(?:\\r\\n\|\\r\|\\n)\/g, \'</p><p>\') %></p>\n\
            </td>\n\
        </tr>\n\
       <% } %>\n\
       <% if (acceptedCards.length) { %>\n\
          <tr>\n\
              <td>Карты оплаты:</td>\n\
              <td>\n\
                <% if ($.inArray(\'visa\', acceptedCards) >= 0) { %><img src="/images/visa.png"/><% } %>\n\
                <% if ($.inArray(\'mastercard\', acceptedCards) >= 0) { %><img src="/images/mastercard.png"/><% } %>\n\
                <% if ($.inArray(\'maestro\', acceptedCards) >= 0) { %><img src="/images/maestro.png"/><% } %>\n\
              </td>\n\
          </tr>\n\
        <% } %>\n\
    </table>\n\
  </div>\n\
</td>\n\
<td class="delivery-time">\n\
    <%= durationString %>\n\
</td>\n\
<td class="delivery-cost">\n\
  <span  <% if (freeShipping) { %>class="free_delivery"<% } %>>\n\
    <% if (null !== cost || null === embeddedCalculatorCode) { %>\n\
      <%= cost %>\n\
    <% } else { %>\n\
      <a href="#" class="embedded-calculator-link" data-calculator="<%= embeddedCalculatorCode %>" data-title="Калькулятор стоимости доставки &laquo;<%= shippingMethodName %>&raquo;">калькулятор</a>\n\
    <% } %>\n\
  </span>\n\
</td>';
});

