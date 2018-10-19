/*
 * @author Alexander Grinevich <agrinevich at accurateweb.ru>
 */

define(function(require){
  var Backbone = require('backbone'),
      LoginDialog = require('view/user/login/user-login-dialog-view');

  var template = _.template('<span class="user-icon"></span><%= username %>');

  /**
   * Панель пользователя в шапке
   */
  return Backbone.View.extend({
    tagName: 'a',
    className: 'header-panel__sign',
    events: {
      'click': 'onClick'
    },
    initialize: function(options){
      this.options = options;
      this.loginDialog = null;
    },
    render: function(){
      var url = urlPrefix + '/' + (this.model.get('authenticated') ? 'cabinet' : 'login');

      this.$el.html(template({
        username: this.model.get('authenticated') ? this.model.getUsername() : 'Войти'
      }));

      if (!this.model.get('authenticated')) {
        this.$el.attr('href', url);
      }

      return this;
    },
    onClick: function (e) {
      if (!this.model.get('authenticated')){
        e.preventDefault();

        this._getLoginDialog().open('login');
      }
    },
    _getLoginDialog: function(){
      if (null === this.loginDialog){
        this.loginDialog = new LoginDialog({
          model: this.model
        });
        this.loginDialog.render().$el.appendTo('body');
      }

      return this.loginDialog;
    }
  });
});