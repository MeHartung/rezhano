define(function(require){
  var Backbone = require('backbone');

  require('jquery-validate');
  require('vendor/inputmask/jquery.inputmask');

  return Backbone.View.extend({
    events: {
      'click #questionFormSubmit': 'onSubmit'
    },
    initialize: function (options) {
      console.log('form init')
      this.addressRequired = true;

      $.validateExtend({
        email: {
          required: true,
          pattern: /^.+\@.+\..+$/
        },
        name: {
          required: true
        },
        phone: {
          required: true,
          pattern: /\+7\s\(\d{3}\)\s\d{3}\-\d{2}\-\d{2}/
        }
      });
    },
    render: function () {
      // this.shippingPanel.setElement(this.$('.shipping-panel'));
      // this.shippingPanel.render();

      this.$('#question_customer_phone').inputmask('+7 (999) 999-99-99');
      this.initValidation();
      return this;
    },
    onSubmit: function(e) {
      e.preventDefault();
    },
    initValidation: function () {
      var self = this;
      this.$el.validateDestroy();
      this.$el.validate({
        sendForm: true,
        onChange: true,
        onBlur: true,
        eachValidField : function() {
          var $this = $(this),
            $parent = $this.parents('.step-item');

          $parent.removeClass('error');
        },
        eachInvalidField : function() {
          var $this = $(this),
            $parent = $this.parents('.step-item');

          $parent.addClass('error');
        },
        description : {
          name: {
            required : '<div>Представьтесь, пожалуйста</div>'
          },
          email: {
            required : '<div>Укажите Ваш электронный адрес</div>',
            pattern : '<div>Введен неверный адрес электронной почты</div>'
          },
          phone: {
            required: '<div>Укажите Ваш телефон</div>',
            pattern: '<div>Введен неверный номер телефона</div>'
          }
        },
        invalid: function(event, options){
          self.isInvalid = true;
        },
        valid: function(event, options){
          self.isInvalid = false;
          self.$('#checkout_submit').attr('disabled', 'disabled');

        }
      });
    }
  })
});