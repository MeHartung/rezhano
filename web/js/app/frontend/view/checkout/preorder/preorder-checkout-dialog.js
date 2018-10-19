define(function(require){
  var ModalDialog = require('view/dialog/base/modal-dialog-view'),
      PreorderCheckoutForm = require('view/checkout/preorder/preorder-checkout-form');

  var template = _.template('\
  <div class="layer__close"></div>\
  <div class="layer__in">\
    <h3>Оформить предзаказ</h3>\
    <p>Оставьте номер телефона, и мы Вам позвоним, как только товар появится на складе.</p>\
    <!--  <span id="user-choise">Вы выбрали: </span><span id="prod-name"><a href="<%= url %>"><%= name %></a></span><br />\ --> \
    <!-- <span id="user-price">Цена: </span><span id="prod-price"><%= price %> ₽</span>\--> \
    <div class="order-wrap__item">\
    <% if (image) { %>\
      <img class="order-pic" src="<%= image %>" alt="" >\
    <% } %>  \
        <a href="<%= url %>" class="order-name"><%= name %></a>\
        <span class="order-quantity">1 шт.</span>\
        <span class="order-date">~ <%= date %></span>\
        <span class="order-price"><%= price %> ₽</span>\
    </div>\
  </div>\
');

  return ModalDialog.extend({
    className: 'layer',
    template: template,
    events: {
      'click .layer__close': 'onCloseButtonClick'
    },
    initialize: function(options){
      ModalDialog.prototype.initialize.apply(this, arguments);

      this.checkoutForm = new PreorderCheckoutForm({
        model: this.model
      });
    },
    render: function(){
      this.$el.html(template({
        name: this.model.get('name'),
        price: this.model.get('price'),
        url: this.model.get('url'),
        image: this.model.get('image'),
        date: this.model.get('expected_delivery_date')
      }));

      this.checkoutForm.$el.appendTo(this.$el);
      this.checkoutForm.render();

      this.listenTo(this.checkoutForm, 'submit:success', this.onSubmitSuccess);

      return this;
    },
    onSubmitSuccess: function(){
      this.$el.html('\
<h3>Ваш заявка принята!</h3>\
<p>Наши менеджеры свяжутся с вами в самое ближайшее время!</p>\
');
    }
  });
});