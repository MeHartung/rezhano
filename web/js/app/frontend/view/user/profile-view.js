define(function(require){
  var Backbone = require('backbone'),
      UserClubPircePopupView = require('view/user/user-club-price-popup-view'),
      User = require('model/user/user'),
      CommonView = require('view/common/common-view');

  return CommonView.extend({
    initialize: function(options){
      CommonView.prototype.initialize.apply(this, [options]);
      this.popup = new UserClubPircePopupView();
      this.currentUser = User.getCurrentUser();
    },
    render: function(){
      CommonView.prototype.render.apply(this);
      if (this.currentUser.get('isShowClubMessage')) {
        this.$('#gkContent h1').after(this.popup.$el);
        this.popup.render();
      }
    }
  });
});