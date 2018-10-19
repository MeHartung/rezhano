define(function(require){
    var Backbone = require('backbone'),
        ModalDialogView = require('view/dialog/base/modal-dialog-view'),
        Form = require('view/user/password-reset/password-reset-request-form-view')
    ;

    return ModalDialogView.extend({
        events: _.extend({}, ModalDialogView.prototype.events, {}),
        initialize: function (options) {
            this.model = new Backbone.Model({
                title: 'Восстановление пароля',
                content: null
            });

            this.form = new Form({
                model: this.model
            });

//            options.template = defaultTemplate;

            ModalDialogView.prototype.initialize.apply(this, arguments);
        },
        render: function () {
            ModalDialogView.prototype.render.apply(this, arguments);

            this.form.setElement(this.$('.layer__container')).render();

            return this;
        },
        // show: function () {
        //     this.$overlay.fadeIn();
        //     this.$el.fadeIn();
        //
        //     this.isVisible = this.isOpened = true;
        // },
        hide: function () {
            this.$el.hide();
            this.$overlay.hide();

            this.isVisible = this.isOpened = false;
        }
    });

});