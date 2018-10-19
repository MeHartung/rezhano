define(function (require) {
  var Backbone = require('backbone'),
    PasswordWidget = require('view/widget/password-input-widget'),
    CommonView = require('view/common/common-view');

  return CommonView.extend({
    events: {
    },
    initialize: function () {
      CommonView.prototype.initialize.apply(this, arguments);
      this.passwordWidget = new PasswordWidget({
        el: this.$('.password-input-wrap')
      });
    },
    render: function () {
      CommonView.prototype.render.apply(this, arguments);
      this.passwordWidget.render();
      return this;
    },
  });
});