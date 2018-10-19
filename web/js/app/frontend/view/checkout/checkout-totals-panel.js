/**
 * Created by Денис on 23.06.2017.
 */
define(function(require){
  var Backbone = require('backbone');

  var template = _.template('\
<div id="tt_order_subtotal_div">\
  <span id="tt_order_subtotal" class="bottom_totals"><%= Number(subtotal).toCurrencyString() %></span>\
  <span id="tt_order_subtotal_txt" class="bottom_totals_txt">Стоимость заказа</span>\
  <br class="op_clear">\
</div>\
<div id="tt_shipping_rate_div">\
  <span id="tt_shipping_rate" class="bottom_totals"><%= Number(shipping_cost).toCurrencyString() %></span>\
  <span id="tt_shipping_rate_txt" class="bottom_totals_txt">Стоимость доставки</span>\
  <br class="op_clear">\
</div>\
<% if (fee) { %>\
<div id="tt_order_payment_discount_after_div">\
  <span id="tt_order_payment_discount_after" class="bottom_totals"><%= Number(fee).toCurrencyString() %></span>\
  <span id="tt_order_payment_discount_after_txt" class="bottom_totals_txt">Комиссия</span>\
  <br class="op_clear">\
</div>\
<% } %>\
<div id="tt_total_div">\
  <span id="tt_total" class="bottom_totals"><%= Number(total).toCurrencyString() %></span>\
  <span id="tt_total_txt" class="bottom_totals_txt">Итого</span>\
  <br class="op_clear">\
</div>')

  return Backbone.View.extend({
    initialize: function(){
      this.listenTo(this.model, 'change', this.render);
    },
    render: function(){
      this.$el.html(template({
        subtotal: this.model.get('subtotal'),
        shipping_cost: this.model.get('shipping_cost'),
        fee: this.model.get('fee'),
        total: this.model.get('total')
      }));

      return this;
    }
  })
});