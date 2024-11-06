define(function(require){
  var BackBone = require('backbone'),
      OrderItemsListView = require('view/user/profile/user-profile-order-list-orderitems-list-view'),
      ModalDialogView = require('view/dialog/base/dialog-view');

  require('lib/string');

  var template = _.template('\
    <div class="popup-layer__title">\
      Заказ №<%= orderNumber %>\
      <span class="popup-layer__sub-title"><%= nbItems %></span>\
      <a class="popup-layer__close"></a>\
    </div>\
    <div class="popup-layer__content scroll-pane"></div>\
');

  return ModalDialogView.extend({
    events: _.extend({}, ModalDialogView.events, {
      'click .popup-layer__close': 'close'
    }),
    className: 'popup-layer popup-layer_cabinet popup-layer_order-info',
    initialize: function (options) {
      ModalDialogView.prototype.initialize.apply(this, arguments);
      this.orderItemsCollection = new BackBone.Collection(this.model.get('order_items'));
      this.orderItemsListView = new OrderItemsListView({
        collection: this.orderItemsCollection
      });
    },
    render: function () {
      var nbItems = this.orderItemsCollection.length;
      this.$el.html(template({
        orderNumber: this.model.get('document_number'),
        nbItems: nbItems + ' ' + String.formatEnding(nbItems, ["товаров", "товар", "товара"])
      }));

      this.orderItemsListView.setElement(this.$('.popup-layer__content'));
      this.orderItemsListView.render();

      // this._restorePosition();

      return this;
    },
    close: function(){
      ModalDialogView.prototype.close.apply(this, arguments);
      this.remove();
    },
    remove: function () {
      this.orderItemsListView.remove();
      ModalDialogView.prototype.remove.apply(this, arguments);
    }
  });
});