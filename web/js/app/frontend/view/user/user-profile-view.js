define(function(require){
  var Backbone = require('backbone'),
    CommonView = require('view/common/common-view'),
    Order = require('model/order/order'),
    UserProfileOrdersListView = require('view/user/profile/user-profile-orders-list'),
    OrderFilterForm = require('view/user/profile/user-profile-orders-filter-form');

  require('jquery-ui/widgets/tabs');
  require('jquery-ui.custom');

  return CommonView.extend({
    events: {

    },
    initialize: function () {
      var self = this;
      CommonView.prototype.initialize.apply(this, arguments);
      this.orderCollection = new Backbone.Collection(ObjectCache.Orders || [], {
        model: Order
      });
      this.orderListView = new UserProfileOrdersListView({
        collection: this.orderCollection,
        el: this.$('.personal-cabinet__order-list')
      });
      this.orderFilterForm = new OrderFilterForm({
        el: this.$('form[name=order_filter]'),
        orders: this.orderCollection
      });
    },
    render: function () {
      CommonView.prototype.render.apply(this, arguments);
      this.orderFilterForm.render();

      return this;
    }
  })
});