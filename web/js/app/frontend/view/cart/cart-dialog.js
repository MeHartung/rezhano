/**
 * Created by Денис on 06.06.2017.
 */
define(function(require){
  var Backbone = require('backbone'),
      ModalDialog = require('view/dialog/base/modal-dialog-view'),
      CartItemListView = require('view/cart/cart-dialog-cart-item-list-view');

  require('lib/string');

  var dialogTemplate = _.template('\
<div class="layer__close"></div>\
<div class="layer__in">\
  <div class="vmCartModule" id="vmCartModule">\
    <h3>Корзина</h3>\
    <% if (!isEmpty) { %> \
    <div class="cart-item-list"></div>\
    <dl></dl>\
    <% } else { %>\
    <dl></dl>\
    <dl><dt>Корзина пуста</dt><dd></dd></dl>\
    <% } %>\
    <noscript>Пожалуйста, подождите</noscript>\
  </div>\
</div>\
');

  var template = _.template('\
  <dl class="cart-total">\
    <dd class="cart-total-price">Итого : <strong><%= total %> ₽</strong></dd>\
    <div class="show-cart">\
      <a class="button" href="<%= urlPrefix %>/cart">Показать корзину</a>\
    </div>\
  </dl>\
');

  var CartTotalsView = Backbone.View.extend({
    tagName: 'dl',
    initialize: function(){
      this.listenTo(this.model, 'change', this.render);
    },
    render: function(){
      this.$el.html(template({
        total: this.model.get('subtotal'),
        quantity: this.model.get('quantity')
      }));

      return this;
    }
  })

  return ModalDialog.extend({
    id: 'gkPopupCart',
    template: dialogTemplate,
    events: {
      'click .layer__close': 'onCloseButtonClick'
    },
    initialize: function(){
      ModalDialog.prototype.initialize.apply(this, arguments);

      this.cartItemListView = new CartItemListView({
        collection: this.model.items
      });
      this.cartTotalsView = new CartTotalsView({
        model: this.model
      })

    },
    render: function(){
      this.$el.html(dialogTemplate({
        isEmpty: this.model.items.length == 0,
      }));

      if (this.model.items.length){
        this.cartItemListView.setElement(this.$('.cart-item-list'));
        this.cartItemListView.render();

        this.cartTotalsView.setElement(this.$('dl'));
        this.cartTotalsView.render();
      }
    }
  });
});