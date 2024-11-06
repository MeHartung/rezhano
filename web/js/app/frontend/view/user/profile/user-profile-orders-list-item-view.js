define(function(require){
  var ListItemView = require('view/base/list-item-view'),
      UserProfileOrderPreview = require('view/user/profile/user-profile-order-list-orderitems-view'),
      UserProfileOrderStatusesHistoryView = require('view/user/profile/user-profile-order-statuses-history-view');

  var template = _.template('\
    <a href="" class="order-list-row__item order-list-item__number"><%= document_number %></a>\
    <a href="" class="order-list-row__item order-list-item__status"><%= status_name %></a>\
    <span class="order-list-row__item order-list-item__date"><%= checkout_at %></span>\
  <span class="order-list-row__item order-list-item__value"><%= total %> ₽</span>\
');

  return ListItemView.extend({
    events: {
      'click .order-list-item__number': 'showOrderItemModal',
      'click .order-list-item__status': 'showOrderStatusModal'
    },
    tagName: 'div',
    className: 'order-list-row',
    initialize: function(){
      ListItemView.prototype.initialize.apply(this, arguments);
    },
    render: function(){
      this.$el.html(template({
        document_number: this.model.get('document_number'),
        status_name: this.model.get('status_name'),
        checkout_at: this.model.get('checkout_at'),
        total: this.model.get('total')
      }));

      return this;
    },
    showOrderItemModal: function (e) {
      e.preventDefault();
      if (this.orderPreview) {
        this.orderPreview.remove();
      }

      this.orderPreview = new UserProfileOrderPreview({
        model: this.model
      });
      this.orderPreview.render().$el.appendTo(this.$el);
      this.orderPreview.open();
    },
    showOrderStatusModal: function (e) {
      e.preventDefault();
      if (this.orderStatusesHistoryView) {
        this.orderStatusesHistoryView.remove();
      }

      this.orderStatusesHistoryView = new UserProfileOrderStatusesHistoryView({
        model: this.model
      });
      this.orderStatusesHistoryView.render().$el.appendTo(this.$el);
      this.orderStatusesHistoryView.open();
    }
  })
});