define(function(require){
  var Backbone = require('backbone'),
    CommonView = require('view/common/common-view'),
    Notice = require('model/user/notice/notice'),
    UserNoticeListView = require('view/user/user-notice-list-view');

  return CommonView.extend({
    events: {

    },
    initialize: function (options) {
      var self = this;
      this.order = options.cart;

      this.noticeCollection = new Backbone.Collection(ObjectCache.Notifications || [], {
        model: Notice
      });
      this.userNoticeListView = new UserNoticeListView({
        collection: this.noticeCollection,
        el: this.$('.section-wrap')
      });
      CommonView.prototype.initialize.apply(this, arguments);
    },
    render: function () {
      CommonView.prototype.render.apply(this, arguments);
      this.userNoticeListView.render();

      return this;
    }
  })
});
