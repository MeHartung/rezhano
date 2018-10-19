define(function(require){
  var Backbone = require('backbone'),
    ProfileView = require('view/user/profile-view'),
    OrderHistoryCollection = require('model/order/history/order-history-collection'),
    OrderHistoryListView = require('view/user/list-view/order-history-list-view'),
    OrderHistoryFinishedListView = require('view/user/list-view/order-history-finished-list-view');

  return ProfileView.extend({
    events: {
      'click .js-show-finished.collapsed': 'onClickShowFinished',
      'click .js-show-active.collapsed': 'onClickShowActive'
    },
    initialize: function(options) {
      this.finishedOrdersLoaded = false;
      ProfileView.prototype.initialize.apply(this, [options]);
      this.order_history_collection = new OrderHistoryCollection(ObjectCache.ActiveOrders);
      this.order_history_collection_finished = new OrderHistoryCollection(ObjectCache.FinishedOrders);
      this.order_history_list_view = new OrderHistoryListView({
        collection: this.order_history_collection
      });
      this.order_history_list_view_finished = new OrderHistoryFinishedListView({
        collection: this.order_history_collection_finished
      });
    },
    render: function() {
      ProfileView.prototype.render.apply(this);
      this.order_history_list_view.setElement($('.active-orders')).render();
      this.order_history_list_view_finished.setElement($('.finished-orders')).render();
      // this.$('.finished-orders').hide();
    },
    onClickShowFinished: function () {
      var self = this;
      self.showFinished();
      // if (!this.finishedOrdersLoaded) {
      //   this.$('.order-preloader').css('display', 'inline-block');
      //   this.order_history_collection_finished.fetch({
      //     data: {
      //       finished: true
      //     },
      //     success: function () {
      //       self.finishedOrdersLoaded = true;
      //       self.$('.order-preloader').hide();
      //
      //       self.showFinished()
      //     }
      //   });
      // } else if (self.order_history_collection_finished.length) {
      //   self.showFinished()
      // }

    },
    onClickShowActive: function () {
      this.$('.js-show-active').removeClass('collapsed').addClass('deployed');
      this.$('.active-orders').show();
      this.$('.js-show-finished').removeClass('deployed').addClass('collapsed');
      this.$('.finished-orders').hide();
    },
    showFinished: function () {
      this.$('.js-show-active').removeClass('deployed').addClass('collapsed');
      this.$('.active-orders').hide();
      this.$('.js-show-finished').removeClass('collapsed').addClass('deployed');
      this.$('.finished-orders').show();
    }
  });
});