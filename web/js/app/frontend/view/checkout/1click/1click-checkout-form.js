/**
 * Created by Денис on 06.06.2017.
 */
define(function(require){
  var Backbone = require('backbone'),
      $ = require('jquery'),
      User = require('model/user/user'),
      OneClickOrder = require('model/checkout/oneclick-order');

  require('vendor/inputmask/jquery.inputmask');
  require('jquery-validate');

  var template = _.template('\
  <div class="form">\
    <div class="form-col__left">\
      <label>Телефон</label>\
    </div>\
    <div class="form-col__right">\
      <input type="text" name="phone" id="form-phone" data-validate="phone" \
            data-describedby="1click-form-phone-errors" data-description="phone" value="<%=phone%>"/>\
      <div id="1click-form-phone-errors"></div>\
    </div>\
  </div>\
  <input type="submit" value="Отправить заявку" id="submit">\
  ')

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
        phone: this.user.get('phone')
      }));


      this.$('#form-phone').inputmask('+7 (999) 999-99-99');

      $.validateExtend({
        name: {
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
            required: '<ul class="error_list"><li>Укажите Ваш телефон</li></ul>',
            pattern: '<ul class="error_list"><li>Введен неверный номер телефона</li></ul>'
          }
        },
        invalid: function(event, options){
          var $firstInvalidField = self.$('.invalid:first');
          if ($firstInvalidField.length) {
            $('body').animate({scrollTop: Math.max($firstInvalidField.offset().top - $(window).height() / 3, 0)}, 'fast');
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

      if (!this.isInvalid){
        this.model.save({
          phone: this.$('#form-phone').val()
        })
        .done(function() {
          self.trigger('submit:success');
        });
      }
    }
  });
});