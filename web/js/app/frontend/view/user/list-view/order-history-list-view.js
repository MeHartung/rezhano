define(function(require){
  var ListView = require('view/base/list-view'),
      OrderHistoryListItemView = require('view/user/list-view/order-history-list-item');

  return ListView.extend({
    itemView: OrderHistoryListItemView,
    template: _.template('<div class="order-table">\
                      <span class="order-table__fix"></span>\
                      <span class="order-table__status">Статус</span>\
                      <span class="order-table__quantity">Кол-во</span>\
                      <span class="order-table__amount">Итоговая сумма</span>\
                  </div>'),
    renderContainer: function() {
      if (this.isEmpty()) {
        this.$el.html('У Вас нет активных заказов');
        return this;
      }
      ListView.prototype.renderContainer.apply(this);
    }
  });
});