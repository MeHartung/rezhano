/*
 * @author Alexander Grinevich <agrinevich at accurateweb.ru>
 */

define(function(require){
  var Backbone = require('backbone');

  var template = _.template('\
  <div class="cart-empty">\n\
    <div class="cart-empty__wrap">\n\
      <div class="cart-empty__image"></div>\n\
      <div class="cart-empty__title">Корзина пуста</div>\n\
      <a href="<%= catalogUrl %>" class="button">В каталог товаров</a>\n\
    </div>\n\
  </div>\n\
 ');

  return Backbone.View.extend({
    render: function () {
      this.$el.html(template({catalogUrl: urlPrefix + '/catalog'}));

      return this;
    }
  });
});