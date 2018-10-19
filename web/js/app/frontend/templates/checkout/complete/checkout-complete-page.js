/* 
 *  @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
define(function(require){
    return '<div class="center_no-column">\n\
    <h1>Заказ оформлен</h1>\n\
    <div class="ordering-container">\n\
      <aside class="right"></aside>\n\
      <div class="wrap">\n\
        <div class="ordering complete_ordering">\n\
          <p>Ваш заказ <b>№<%= doc_no %></b> на <b><%= Number(cost).toCurrencyString() %></b> оформлен. <br />В ближайшее время с Вами свяжется менеджер для подтверждения заказа.</p>\n\
          <%= cz %>\n\
        </div>\n\
      </div>\n\
    </div>\n\
  </div>';
});