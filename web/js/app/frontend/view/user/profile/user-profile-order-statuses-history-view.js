define(function(require){
  var BackBone = require('backbone'),
    OrderStatusesListView = require('view/user/profile/user-profile-order-statuses-history-list-view'),
    ModalDialogView = require('view/dialog/base/dialog-view');

  var template = _.template('' +
    '<div class="popup-layer__title">\n' +
    '   Трек-номер <%= trackNumber %>\n' +
    '   <a class="popup-layer__close"></a>\n' +
    '  </div>\n' +
    '<div class="popup-layer__content">'+
'');

  return ModalDialogView.extend({
    events: _.extend({}, ModalDialogView.events, {
      'click .popup-layer__close': 'close'
    }),
    className: 'popup-layer popup-layer_cabinet',
    initialize: function (options) {
      ModalDialogView.prototype.initialize.apply(this, arguments);
      this.collection = this.model.getOrderStatusesHistory();
      this.orderStatusesView = new OrderStatusesListView({
        collection: this.collection
      });
    },
    render: function () {
      this.$el.html(template({
        trackNumber: this.model.get('document_number')
      }));

      this.orderStatusesView.setElement(this.$('.popup-layer__content'));
      this.orderStatusesView.render();


      return this;
    },
    close: function(){
      ModalDialogView.prototype.close.apply(this, arguments);
      this.remove();
    },
    remove: function () {
      this.orderStatusesView.remove();
      ModalDialogView.prototype.remove.apply(this, arguments);
    }
  });
});