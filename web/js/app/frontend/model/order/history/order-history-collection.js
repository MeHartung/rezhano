define(function(require){
  var Backbone = require('backbone'),
      OrderHistory = require('model/order/history/order-history');

  return Backbone.Collection.extend({
    // url: urlPrefix + '/cabinet/orders',
    model: OrderHistory,
    initialize: function () {
      this.on('activate', this.activateModel, this);
    },
    activateModel: function (order) {
      _.each(this.models, function (model) {
        if (model.get('id') === order.get('id')){
          model.set({active: true})
        } else {
          if (model.get('active')) {
            model.set({active: false})
          }
        }
      })
    }
  });
});