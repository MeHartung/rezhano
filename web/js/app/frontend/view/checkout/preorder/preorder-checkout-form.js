define(function(require){
  var Backbone = require('backbone'),
      $ = require('jquery'),
      User = require('model/user/user'),
      OneClickOrder = require('model/checkout/preorder');

  require('vendor/inputmask/jquery.inputmask');
  require('jquery-validate');

  var template = _.template('\
  <div class="form form__preorder">\
    <div class="error-list"></div>\
    <div class="for-group">\
      <input type="text" name="firstname" required="required" placeholder="Ваше имя" class="form-control" value="<%=firstname%>" autofocus="" checked="">\
    </div>\
    <div class="for-group">\
    <input type="text" name="phone" id="form-phone" data-validate="phone" placeholder="Номер телефона" \n\
      data-describedby="preorder-form-phone-errors" data-description="phone" value="<%=phone%>"/>\
     <div class="error-list" id="preorder-form-phone-errors"></div>\
    </div>\
    <div class="for-group">\
        <input type="text" name="email" required="required" value="<%=email%>" placeholder="Электронная почта" class="form-control" autofocus="" checked="">\
    </div>\
  </div>\
  <input type="submit" value="Отправить заявку" id="submit">\
  ');

  return Backbone.View.extend({
    tagName: 'form',
    events: {
      'submit': 'onSubmit'
    },
    initialize: function(){
      this.product = this.model;

      this.model = new OneClickOrder({
        product_slug: this.product.get('slug')
      });

      self.isInvalid = true;
      this.user = User.getCurrentUser();
    },
    render: function(){
      var self = this;

      this.$el.html(template({
        phone: this.user.get('phone'),
        firstname: this.user.get('fullname'),
        email: this.user.get('email')
      }));

      this.$('#form-phone').inputmask('+7 (999) 999-99-99');

      $.validateExtend({
        firstname: {
          required: true,
          pattern: /^.{3,}$/
        },
        phone: {
          required: true,
          pattern: /\+7\s\(\d{3}\)\s\d{3}\-\d{2}\-\d{2}/
        },
        email: {
          required: false,
          pattern: /^.+\@.+\..+$/
        }
      });

      this.$el.validate({
        sendForm: true,
        onChange: true,
        onBlur: true,
        eachValidField : function() {
          var $this = $(this),
              $parent = $this.parent();

          if ($parent.hasClass('radio')){
            $parent = $parent.parent();
          };

          $parent.removeClass('invalid');
          if ($this.val()) {
            $parent.addClass('valid');
          }
        },
        eachInvalidField : function() {
          var $this = $(this),
            $parent = $this.parent();

          if ($parent.hasClass('radio')){
            $parent = $parent.parent();
          };

          $parent.removeClass('valid').addClass('invalid');
        },
        description : {
          phone: {
            required: '<div>Укажите Ваш телефон</div>',
            pattern: '<div>Введен неверный номер телефона</div>'
          },
          firstname: {
            required: '<div>Укажите Ваше имя</div>',
            pattern: '<div>Введеное имя неверно</div>'
          },
          email: {
            required: '<div>Укажите Ваш e-mail</div>',
            pattern: '<div>Введен неверный e-mail</div>'
          }
        },
        invalid: function(event, options){
          var $firstInvalidField = self.$('.invalid:first');
          if ($firstInvalidField.length) {
            $('html, body').animate({scrollTop: Math.max($firstInvalidField.offset().top - $(window).height() / 3, 0)}, 'fast');
          }
          self.isInvalid = true;
        },
        valid: function(event, options){
          self.isInvalid = false;
        }
      });


      return this;
    },
    onSubmit: function(e){
      e.preventDefault();
      var self = this;
      this.$('.error-list').html('');

      if (!this.isInvalid){
        var data = this.$el.serializeArray();

        $.map(data, function (n, i) {
          self.model.set(n['name'], n['value']);
        });

        this.model
          .save(this.model.attributes, {
            error: function (model, response, options) {
              for (field in response.responseJSON) {
                if (self.$('[name='+field+']').length) {
                  self.$('[name='+field+']').after('<div>'+response.responseJSON[field]+'</div>');
                } else {
                  self.$('.error-list').append('<div>' + response.responseJSON[field] + '</div>');
                }
              }
            }
          })
          .done(function () {
            self.trigger('submit:success');
          });
      }
    }
  });
});