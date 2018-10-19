/**
 * Created by Денис on 06.06.2017.
 */
define(function(require){
  var ModalDialog = require('view/dialog/base/modal-dialog-view'),
      Buy1ClickCheckoutForm = require('view/checkout/1click/1click-checkout-form');

  var template = _.template('\
  <div class="layer__close"></div>\
  <div class="layer__in">\
    <h3>Заказ без оформления</h3>\
    <p>Не нужно заполнять никаких данных, <br />просто оставьте номер телефона и мы перезвоним!</p>\
    <!-- <span id="user-choise">Вы выбрали: </span><span id="prod-name"><a href="<%= url %>"><%= name %></a></span><br /> -->\
    <!-- <span id="user-price">Цена: </span><span id="prod-price"><%= price %> ₽</span> -->\
    <span class="user-choise">Вы выбрали: </span>\
    <div class="order-wrap__item">\
      <img class="order-pic" src="/images/cube.jpg" alt="" >\
        <a href="<%= url %>" class="order-name"><%= name %></a>\
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

      this.checkoutForm = new Buy1ClickCheckoutForm({
        model: this.model
      });
    },
    render: function(){
      this.$el.html(template({
        name: this.model.get('name'),
        price: this.model.get('price'),
        url: this.model.get('url')
      }));

      this.checkoutForm.$el.appendTo(this.$el);
      this.checkoutForm.render();

      this.listenTo(this.checkoutForm, 'submit:success', this.onSubmitSuccess);

      return this;
    },
    onSubmitSuccess: function(){
      this.$el.html('\
<h3>Ваш заказ принят!</h3>\
<p>Наши менеджеры свяжутся с вами в самое ближайшее время!</p>\
');
      var today = new Date();
      var dd = today.getDate();
      var mm = today.getMonth()+1;
      var yyyy = today.getFullYear();

      if(dd<10) {
        dd = '0'+dd
      }

      if(mm<10) {
        mm = '0'+mm
      }

      today = mm + '/' + dd + '/' + yyyy;

      window.dataLayer = window.dataLayer || [];
      window.dataLayer.push({
        event: "oneClickPurchase",
        ecommerce: {
          purchase: {
            actionField: {
              id: today + ' - ' + this.model.get('product_id') + ' - ' + this.checkoutForm.model.get('phone'),
              affiliation: 'www.excam.ru',
              'revenue': this.model.get('price'),
//            'tax' => '4.90',
//            'shipping' => '5.99',
//            'coupon' => 'SUMMER_SALE'
            },
            products: [
              {
                'name': this.model.get('name'),
                'id': this.model.get('product_id'),
                'price': this.model.get('price'),
                // 'brand': product.get('brand'),
                // 'category': product.get('section'),
                'quantity': 1
              }
            ]
          }
        }
      });
    }
  });
});