/* 
 *  @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
define(function (require) {
    var Backbone = require('backbone'),
        ModalDialogView = require('view/dialog/base/modal-dialog-view'),
        LoginForm = require('view/user/login/user-login-form-view'),
        PasswordResetRequestDialog = require('view/user/password-reset/password-reset-request-dialog-view')
    ;

    var defaultTemplate = _.template('' +
        '    <div class="layer__close"></div>\n' +
        '    <div class="layer__container">\n' +
        '      <div class="layer__title"><%= title %></div>\n' +
        '    </div>\n' +
        '    <a class="button button-register" href="<%= urlPrefix %>/registration">Регистрация</a>');

    return ModalDialogView.extend({
        events: _.extend({}, ModalDialogView.prototype.events, {
            'click .restore-link': 'onResetPasswordLinkClick'
        }),
        initialize: function (options) {
            this.model = new Backbone.Model({
                title: 'Вход',
                content: null
            });

            this.loginForm = new LoginForm({
                model: this.model
            });
            this.passwordResetRequestDialog = null;

            options.template = defaultTemplate;

            ModalDialogView.prototype.initialize.apply(this, arguments);
        },
        render: function () {
            ModalDialogView.prototype.render.apply(this, arguments);

            this.loginForm.setElement(this.$('.layer__container')).render();

            return this;
        },
        show: function () {
            this.$overlay.fadeIn();
            this.$el.fadeIn();

            this.isVisible = this.isOpened = true;
        },
        hide: function () {
            this.$el.hide();
            this.$overlay.hide();

            this.isVisible = this.isOpened = false;
        },
        onResetPasswordLinkClick: function (e) {
            e.preventDefault();

            if (null === this.passwordResetRequestDialog){
                this.passwordResetRequestDialog = new PasswordResetRequestDialog({

                });
                this.passwordResetRequestDialog.$el.appendTo($('body'));
            }

            this.close();
            this.passwordResetRequestDialog.render().open();
        }
    });
});

