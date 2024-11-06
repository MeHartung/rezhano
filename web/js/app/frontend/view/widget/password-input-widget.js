define(function (require) {
    var Backbone = require('backbone');

    return Backbone.View.extend({
        className: 'password-input-wrap',
        events: {
            'click .password-input-wrap__toggle': 'onShowPasswordButtonClick'
        },
        initialize: function () {
            this.state = new Backbone.Model({
                'show-password': false
            });
            this.listenTo(this.state, 'change:show-password', this.onShowPasswordChange);
        },
        render: function(){
            this.onShowPasswordChange();

            return this;
        },
        onShowPasswordButtonClick: function (e) {
            e.preventDefault();

            this.state.set('show-password', !this.state.get('show-password'));
        },
        onShowPasswordChange: function () {
            if (this.state.get('show-password')) {
                this.$('.layer__input-password').attr('type', 'text');
                this.$('.password-input-wrap__toggle').addClass('visible');
            } else {
                this.$('.layer__input-password').attr('type', 'password');
                this.$('.password-input-wrap__toggle').removeClass('visible');
            }
        }
    })
});