define(function(require){
  var ModalDialog = require('view/dialog/base/modal-dialog-view'),
      CartItemListView = require('view/user/list-view/order-item-list-view');

  var template = _.template('\
  <div class="layer__close"></div>\
  <div class="layer__in">\
    <div class="left">\
      <h3>Заказ №<%= document_number %></h3>\
      <p><b>Способ оплаты:</b> <%= payment %></p>\
      <p><b>Способ доставки:</b> <%= shipping %></p>\
      <p><b>Стоимость заказа:</b> <%= total.toCurrencyString() %></p>\
      <p><b>Стоимость доставки:</b> <%= shipping_cost.toCurrencyString() %></p>\
      <p><b>Комиссия наложенного платежа\n:</b> <%= fee.toCurrencyString() %></p>\
    </div>\
    <div class="right">\
    <table class="order-items"></table>\
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
      this.cartItemListView = new CartItemListView({
        collection: this.model.get('items')
      });
    },
    render: function(){
      this.$el.html(template({
        document_number: this.model.get('document_number'),
        payment: this.model.get('payment_method_name'),
        shipping: this.model.get('shipping_method_name'),
        total: Number(this.model.get('total')),
        shipping_cost: Number(this.model.get('shipping_cost')),
        fee: Number(this.model.get('fee')),
        discount_percentage: +this.model.get('discount_percentage'),
        discount_sum: +this.model.get('discount_sum')
      }));

      this.cartItemListView.setElement(this.$el.find('.order-items')).render();
      return this;
    }
  });
});