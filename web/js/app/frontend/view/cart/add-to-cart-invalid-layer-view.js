/**
 * Created by Денис on 06.06.2017.
 */
define(function(require){
  var ModalDialogView = require('view/dialog/base/modal-dialog-view');

  var template = _.template('\
  <% if (quantity >= minQuantity) { %>\
    <div class="layer__close"></div>\
    <div class="layer__container">\
      <div class="layer__title">Уточнение количества</div>\
      <div class="add-to-cart-layer__wrap">\
        <p>Товар «<%= name %>» нельзя добавить в корзину в количестве <%= quantity %>&nbsp;<%= units %>.</p>\
        <p>Вы можете добавить <%= validQuantity %>&nbsp;<%= units %> или <%= nextValidQuantity %>&nbsp;<%= units %></p>\
      </div>\
    </div>\
    <div class="buttons">\
      <a class="button button_black button_yellow-yellow add-valid-quantity" href="<%= urlPrefix %>/cart">Добавить <%= validQuantity %> <%= units %></a>\
      <a class="button button_black button_yellow-yellow add-next-valid-quantity" href="#">Добавить <%= nextValidQuantity %> <%= units %></a>\
    </div>\
  <% } else { %>\
    <div class="layer__close"></div>\n\
    <div class="layer__container">\n\
      <div class="layer__title">Уточнение количества</div>\n\
      <div class="add-to-cart-layer__wrap">\n\
        <p>Товар «<%= name %>» нельзя добавить в корзину в количестве <%= quantity %>&nbsp;<%= units %>.</p>\
        <p>Минимальное доступное количество для заказа <%= minQuantity %>&nbsp;<%= units %></p>\n\
      </div>\n\
    </div>\n\
    <div class="buttons">\n\
      <a class="button button_black button_yellow-yellow add-min-quantity" href="<%= urlPrefix %>/cart">Добавить <%= minQuantity %> <%= units %></a>\n\
    </div>\n\
  <% } %>\
');

  return ModalDialogView.extend({
    id: 'facebox',
    className: 'layer add-to-cart-layer',
    template: template,
    events: {
      'click .layer__close': 'onCloseClick',
      'click .continue-shopping': 'onCloseClick',
      'click .add-valid-quantity': 'onAddValidQuantity',
      'click .add-next-valid-quantity': 'onAddNextValidQuantity',
      'click .add-min-quantity': 'onAddMinQuantity'
    },
    initialize: function(options){
      this.quantity = +options.quantity;
      this.product = options.product;
      this.cart = options.cart;
      this.cartItem = options.cartItem;
      this.step = +options.product.get('count_step');
      this.minQuantity = +options.product.get('min_count');

      var _q = options.quantity - this.minQuantity,
          modul = _q % this.step,
          min = modul > 0.0001 ? options.quantity - modul : options.quantity,
          max = modul > 0.0001 ? min + this.step : options.quantity;


      this.validQuantity = this.formatFloat(min);
      this.nextValidQuantity = this.formatFloat(max);

      ModalDialogView.prototype.initialize.apply(this, arguments);
    },
    onCloseClick: function(e){
      e.preventDefault();

      this.close();
    },
    render: function(){
      this.$el.html(template({
        'validQuantity': this.validQuantity,
        'nextValidQuantity': this.nextValidQuantity,
        'step': this.step,
        'units': this.product.get('units') ? this.product.get('units') : '',
        'minQuantity': this.minQuantity,
        'quantity': this.quantity,
        'name': this.product.get('name')
      }));

      return this;
    },
    close: function(){
      ModalDialogView.prototype.close.apply(this, arguments);

      this.dispose();
    },
    show: function(){
      var self = this;
      this.$overlay.fadeIn();
      this.$el.fadeIn();
    },
    onAddValidQuantity: function (e) {
      e.preventDefault();
      this.addToCart(this.validQuantity);
    },
    onAddNextValidQuantity: function (e) {
      e.preventDefault();
      this.addToCart(this.nextValidQuantity);
    },
    onAddMinQuantity: function (e) {
      e.preventDefault();
      this.addToCart(this.minQuantity);
    },
    addToCart: function (quantity) {
      var self = this;
      this.cartItem
        .set('quantity', quantity)
        .save()
        .done(function(){
          self.cart.items.set(self.cartItem, {
            remove: false
          });
          self.cart.trigger('item:add', self.cartItem, self.cartItem.quantity);
          self.close();
        });
    },
    formatFloat: function ($number) {
      return +$number.toFixed(2);
    }
  });
});