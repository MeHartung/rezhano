/*
 * @author Alexander Grinevich <agrinevich at accurateweb.ru>
 */
define(function(require){
  var Backbone = require('backbone'),
      PasswordInputWidget = require('view/widget/password-input-widget');

  require('jquery-validate');

  var template = _.template('' +
    '      <div class="layer__title">Вход</div>\n' +
    '      <form method="POST" class="layer__form" action="<%= urlPrefix %>/login_check">\n' +
    '        <ul class="error-list global"></ul>' +
    '        <input class="layer__input layer__input-username" type="text" placeholder="Почта" name="_username">\n' +
    '        <div class="password-input-wrap">\n' +
    '          <input class="layer__input layer__input-password" type="password" placeholder="Пароль" name="_password">\n' +
    '          <div class="password-input-wrap__toggle" title="Показать пароль"></div>\n' +
    '        </div>\n' +
    '        <div class="user-layer__controls">\n' +
    '          <span class="custom-checkbox">\n' +
    '            <label>\n' +
    '              <input type="checkbox" class="checkbox" checked="" name="_remember_me">\n' +
    '              <span class="custom-checkbox__checkbox"></span>\n' +
    '              <span>Запомнить</span>\n' +
    '            </label>\n' +
    '          </span>\n' +
    '          <a class="restore-link js-restore" href="">Забыли пароль?</a>\n' +
    '        </div>\n' +
    '        <div class="buttons">\n' +
    '          <button class="button layer__button" type="submit" name="_submit" value="Войти">Войти</button>\n' +
    '        </div>\n' +
    '      </form>')

  return Backbone.View.extend({
    events: {
      'submit form': 'onSubmit'
    },
    initialize: function(){
      this.isInvalid = true;

      this.passwordInput = new PasswordInputWidget();
    },
    render: function(){
      var self = this;

      this.$el.html(template());

      $.validateExtend({
        email: {
          required: true,
          pattern: /^.+\@.+\..+$/
        },
        password: {
          required: true
        },
        confirmPassword: {
          required: true,
          conditional: function () {
            return self.$('#fos_user_registration_form_plainPassword_first').val() === self.$('#fos_user_registration_form_plainPassword_second').val()
          }
        },
        newPassword: {
          required: true,
          pattern: /^.{5,}$/
        }
      });

      this.initValidation();
      this.passwordInput.setElement(this.$('.password-input-wrap')).render();

      return this;
    },
    initValidation: function () {
      var self = this;

      this.$('form').validateDestroy();
      this.$('form').validate({
        sendForm: false,
        onChange: true,
        onBlur: true,
        onSubmit: true,
        eachValidField : function() {
          self.isInvalid = false;
          var $this = $(this),
            $parent = $this.parent();

          if ($parent.hasClass('radio')){
            $parent = $parent.parent();
          }

          $parent.removeClass('invalid');

          if ($this.val()) {
            $parent.addClass('valid');
          }
        },
        eachInvalidField : function() {
          self.isInvalid = true;
          var $this = $(this),
            $parent = $this.parent();

          if ($parent.hasClass('radio')){
            $parent = $parent.parent();
          }

          $parent.removeClass('valid').addClass('invalid');
        },
        description : {
          confirmPassword: {
            required: '<div>Введите пароль еще раз</div>',
            conditional: '<div>Введенные пароли не совпадают</div>'
          },
          newPassword: {
            required: '<div>Введите пароль</div>',
            pattern: '<div>Пароль должен содержать не менее 5 символов</div>'
          },
          password: {
            required: '<div>Введите пароль</div>'
          },
          email: {
            required: '<div>Укажите Ваш e-mail</div>',
            pattern: '<div>Введен неверный e-mail</div>'
          }
        },
        invalid: function(event, options){
          // var $firstInvalidField = self.$('.invalid:first');
          // if ($firstInvalidField.length) {
          //   $('html, body').animate({scrollTop: Math.max($firstInvalidField.offset().top - $(window).height() / 3, 0)}, 'fast');
          // }
          self.isInvalid = true;
        },
        valid: function(event, options){
          self.isInvalid = false;
          self.onSubmit(event);
        }
      });
    },
    onSubmit: function(e){
      e.preventDefault();
      var self = this;
      var $form = this.$('form');

        $form.addClass('loading');

        $.ajax({
          type: $form.attr('method'),
          url: $form.attr('action'),
          data: $form.serialize(),
          dataType: "json",
          success: function(data) {
            // if (data.success) {
              // Обновляем страницу
              window.location.href = window.location.href;
            // }
          },
          error: function(data){
            for (var field in data.responseJSON.errors) {
              var $field = $form.find('[name*='+field+']');
              if (data.responseJSON.errors.hasOwnProperty(field)) {
                if ($field.length) {
                  $field.parent('.form-group').addClass('invalid').find('.error-list').html('<div>' + data.responseJSON.errors[field]+ '</div>');
                } else {
                  $form.find('.error-list.global').html('<li>'+ data.responseJSON.errors[field] + '</li>');
                }
              }
            }
          },
          complete: function()
          {
            $form.removeClass('loading')
          }
        });
      }
  });
});