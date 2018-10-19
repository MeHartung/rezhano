/* 
 * @author Denis N. Ragozin <ragozin at artsofte.ru>
 * @version SVN: $Id$
 * @revision SVN: $Revision$
 */
define(function(require){
  var Backbone = require('backbone');
  
  require('acommerce/utils/string');
  require('core-lib/utils/date.format');

  
  var app = require('core/app');

  var template = _.template('\
          <% if (count > 0){ %>\n\
            <div class="bucket-inner">\n\
                <ul class="current-items js-current-items">\n\
                    <li class="amount-of-items"><a href="<%= cart_url %>"><span><%= count %></span> <%= goods_label %></a></li>\n\
                    <li><a href="<%= cart_url %>">на <span><%= (total_cost).toCurrencyString() %></span></a></li>\n\
                </ul>\n\
                <% if (checkoutButton){ %>\n\
                  <input type="hidden" name="fromPlace" value="pageHeaderButton" />\n\
                  <input type="hidden" name="fromUrl" value="<%= encodeURIComponent(window.location.pathname) %>" />\n\
                  <input type="hidden" name="dateTime" value="<%= (new Date()).format(\'yyyy-mm-dd HH:MM:ss\') %>" />\n\
                  <input type="submit" value="Оформить заказ" class="checkout-button make-an-order">\n\
                <% } %>\n\
            </div>\n\
          <% } else { %> \n\
          <div class="bucket-inner cart-empty">\n\
              <ul class="current-items">\n\
                  <li class="amount-of-items">Корзина</li>\n\
                  <li>пуста</li>\n\
              </ul>\n\
          </div>\n\
          <% } %>\n\
    ');

  var CartPanelView = Backbone.View.extend({
    events: {
      'click .js-current-items': 'onClickCurrentItems',
    },
    initialize: function(){
      this.model.on('change', this.render, this);
      this.render();
    },
    render: function(){
      this.$el.html(template({
        count: this.model.get('quantity'),
        goods_label: String.formatEnding(this.model.get('quantity'),  ["товаров", "товар", "товара"]),
        total_cost: this.model.get('cost'),
        checkoutButton: this.model.get('checkoutButton') && this.model.costIsValidForCheckout(),
        cart_url: app.url('/cart')
      }));
    },
    onClickCurrentItems: function (e) {
      e.preventDefault();

      if (location.pathname == app.url('/checkout')) {
        return;
      }

      this.model.get('items').fetch({
        reset: true
      });
      this.model.trigger('submit', {title: 'Корзина'});
    }
  });

  return CartPanelView;
});

