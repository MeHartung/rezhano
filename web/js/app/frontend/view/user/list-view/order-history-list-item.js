define(function(require){
  var Backbone = require('backbone'),
      ListItemView = require('view/base/list-item-view'),
      CartItemListView = require('view/user/list-view/order-item-list-view');

  require('lib/date.format');

  var template = _.template('\
  <div class="order-wrap__header">\n\
    <span class="order-date"><% if (document_number) { %><span class="order-number">№ <%= document_number %></span><% } %>от <%= date %></span>\n\
    <span class="order-status">\
    <% if (is_preorder) { %>\
      Предзаказ\
    <% } else if (payment_status_name) { %>\
      <i class="<%= paid_class %>"><%= payment_status_name %></i><% if (status) { %>, <%= status.toLowerCase() %><% } %>\
    <% } else { %>\
      <%= status %>\
    <% } %>\
    </span>\n\
    <span class="order-quantity"><%= itemsQuantity %></span>\n\
    <span class="other"><span class="other-amount-pay"><%= total %></span><i class="triangle-icon__down"></i><i class="triangle-icon__down_red"></i><i class="triangle-icon__up"></i></span>\n\
  </div>\
  <div class="order-details">\n\
    <div class="order-items"></div>\n\
    <div class="order-wrap__total">\n\
    <% if (is_preorder) { %>\
      <div class="total-item">\
        <b>Ожидаемая дата поступления:</b> <%= preorder_date %>\
      </div>\
    <% } else { %>\
      <div class="total-item">\n\
        <img src="/images/curier_icon.png" alt="" class="order-courier">\n\
        <span class="order-adress"><%= shipping %> <%= shipping_address %></span>\n\
      </div>\n\
      <div class="total-item">\n\
        <img src="/images/cash_icon.png" alt="" class="order-cash">\n\
        <span class="order-payment"><%= payment %></span>\n\
      </div>\n\
    <% } %>  \
      <div class="total-more">\n\
        <span class="total-delivery">Стоимость доставки <i><%= shipping_cost %></i></span>\n\
        <% if (fee) { %>\
          <span class="total-commission">Комиссия наложенного платежа <i><%= fee.toCurrencyString() %></i></span>\n\
        <% } %>\
        <% if (discount_sum) { %>\
          <span class="total-discount">Скидка постоянного клиента - <%= discount_percentage %>% <i><%= discount_sum.toCurrencyString() %></i></span>\n\
        <% } %>\
      </div>\n\
    </div>\n\
    <div class="total-amount">\n\
       <span class="amount-pay">ИТОГО: <i><%= total %></i></span>\n\
    </div>\n\
  </div>\
  ');

  return ListItemView.extend({
    className: 'order-wrap',
    events: {
      'click .order-wrap__header': 'onHeaderClick'
    },
    initialize: function(){
      ListItemView.prototype.initialize.apply(this, arguments);

      this.cartItemListView = new CartItemListView({
        collection: new Backbone.Collection(this.model.get('order_items') || [])
      });

      this.listenTo(this.model, 'change:active', this.render)
    },
    render: function(){

      this.$el.html(template({
        document_number: this.model.get('document_number'),
        date: new Date(this.model.get('created_at').date).format('d mmmm yyyy'),
        total: Number(this.model.get('total')).toCurrencyString(),
        status: this.model.get('status_name'),
        itemsQuantity: this.model.get('order_items').length + ' ' + String.formatEnding(this.model.get('order_items').length, ["товаров", "товар", "товара"]),
        payment: this.model.get('payment_method_name'),
        shipping: this.model.get('shipping_method_name'),
        shipping_cost: Number(this.model.get('shipping_cost')).toCurrencyString(),
        shipping_address: this.model.get('shipping_address') ? 'по адресу ' + this.model.get('shipping_address') : '',
        fee: Number(this.model.get('fee')),
        discount_percentage: Number(this.model.get('discount_percentage')),
        discount_sum: Number(this.model.get('discount_sum')),
        paid_class: this.model.get('is_paid') ? 'paid' : 'paid_not',
        payment_status_name: this.model.get('payment_status_name'),
        is_preorder: this.model.get('is_preorder'),
        preorder_date: this.model.get('preorder_date')
      }));

      this.cartItemListView.setElement(this.$el.find('.order-items')).render();

      if (this.model.get('active')) {
        this.$el.addClass('deployed')
      } else {
        this.$el.removeClass('deployed')
      }

      return this;
    },
    onHeaderClick:function () {
      if (!this.model.get("active")) {
        this.model.trigger('activate', this.model)
      } else {
        this.model.set({'active': false})
      }
    }
  })
});