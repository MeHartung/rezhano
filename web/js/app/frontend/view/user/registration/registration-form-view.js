define(function (require) {
  var Form = require('backbone-forms'),
    PasswordInputWidget = require('view/widget/password-input-widget');

  require('vendor/inputmask/jquery.inputmask');

  //    '                <input type="checkbox" id="registerIndividual_tos" name="registerIndividual[tos]" required="required" class="checkbox" value="1" checked="checked">\n' +

  var RegistrationFormField = Form.Field.extend({
    template: _.template('\
    <div class="step-item">\
      <span class="field-description field-<%= key %>"><label class="required" for="<%= editorId %>"><%= title %></label>*</span>\n\
      <span data-editor></span>\n\
      <div class="help-inline" data-error></div>\n\
      <div class="help-block"><%= help %></div>\n\
    </div>  \
    '),
    className: 'step-item',
    initialize: function (options) {
      Form.Field.prototype.initialize.apply(this, arguments);

      this.inline = options.schema.inline || false;
    },
    render: function () {
      Form.Field.prototype.render.apply(this, arguments);

      if (this.inline) {
        this.$el.addClass('step-item_inline');
      }

      return this;
    }
  });

  var RegistrationForm = Form.extend({
    schema: {
      lastname: {
        type: "Text",
        title: "Фамилия",
        editorClass: "input-text_200",
        editorAttrs: {
          "required": "required"
        },
        inline: true,
        validators: [{type: 'required', message: 'Введите Вашу фамилию'}]
      },
      firstname: {
        type: "Text",
        title: "Имя",
        editorClass: "input-text_200",
        editorAttrs: {
          "required": "required"
        },
        inline: true,
        validators: [{type: 'required', message: 'Введите Ваше имя'}]
      },
      middlename: {
        type: "Text",
        title: "Отчество",
        editorClass: "input-text_200",
        // editorAttrs: {
        //   "required": "required"
        // },
        inline: true//,
        //validators: [{type: 'required', message: 'Введите Ваше отчество'}]
      },
      phone: {
        type: "InputPhone",
        title: "Номер телефона",
        editorClass: "input-text_200",
        editorAttrs: {
          "required": "required",
          "placeholder": "+7 (___) ___ - __ - __"
        },
        inline: true,
        validators: [{type: 'required', message: 'Введите Ваш номер телефона'}]
      },
      email: {
        type: "Text",
        title: "Электронная почта",
        editorClass: "input-text_200",
        editorAttrs: {
          "required": "required"
        },
        inline: true,
        validators: [
          { type: 'required', message: 'Введите Ваш адрес электронной почты' },
          { type: 'email', message: 'Неверный адрес e-mail' }
        ]
      },
      plainPasswordFirst: {
        type: "Password",
        title: "Пароль",
        editorClass: "input-text_200",
        inline: true,
        validators: [{type: 'required', message: 'Введите пароль'}]
      },
      plainPasswordSecond: {
        type: "Password", title: "Повтор пароля", editorClass: "input-text_200", inline: true,
        validators: [{type: 'required', message: 'Введите пароль еще раз'}]
      },
      contragent: {
        type: "CustomCheckbox",
        title: "Я &mdash; контрагент ПАО «Газпромнефть»",
        editorClass: 'custom-checkbox_biggest'
      },
      tos: {
        type: "CustomCheckbox",
        title: "Я ознакомился и согласен с <a href=\"\">Политикой Компании</a> и <a href=\"\">условиями сотрудничества</a>",
        template: _.template('' +
          '<div class="step-item step-item_inline">' +
          '    <div class="control" data-editor ></div>' +
          '    <div class="help-inline" data-error></div>' +
          '    <div class="help-block"><%= help %></div>' +
          '</div>'),
        validators: [{type: 'required', message: 'Вы должны принять условия сотрудничества'}]
      }
    },
    events: {
      'submit': 'onFormSubmit'
    },
    initialize: function () {
      Form.prototype.initialize.apply(this, arguments);

      this.listenTo(this, 'change', this.onChange);
    },
    onFormSubmit: function (e) {
      var fields = this.fields,
          self = this;
      e.preventDefault();

      self.$('.global-errors').hide();

      if ('undefined' === typeof this.commit()) {
        this.model.save(null, {
          success: function (r) {
            //alert('Success!')
            //if (r.location){
            window.location.href = urlPrefix + '/registration/success';
            //}
            // $('.sf_admin_form > .alert').remove();
            // $('.sf_admin_form').prepend($('<div class="alert alert-success">Изменения успешно сохранены</div>'));
          },
          error: function (model, xhr) {
            switch (xhr.status) {
              case 401: {
                window.location.reload();
                break;
              }
              case 400: {
                var data = JSON.parse(xhr.responseText);
                _.forEach(fields, function (field, key) {
                  if (data.errors[key]) {
                    field.setError(data.errors[key]);
                  } else {
                    field.clearError();
                  }
                });
                if (data.errors['#']){
                  self.$('.global-errors').show().html('<div class="help-inline">'+data.errors['#']+'</div>')
                }
                break;
              }
              case 500: {
                self.$('.global-errors').show().html('<div class="help-inline">Не удалось сохранить изменения. Пожалуйста, попробуйте позднее.</div>')
              }
            }
          }
        });
      }
    }
  }, {
    Field: RegistrationFormField
  });

  var CustomInput = RegistrationForm.editors.CustomInput = Form.Editor.extend({
    template: '',
    inputOptions: null,
    initialize: function (options) {
      Form.editors.Base.prototype.initialize.call(this, options);

      this.input = this.createInputEditor(_.extend({},
        options, this.inputOptions));

      this.listenTo(this.input, 'all', this.handleEditorEvent);
    },
    createInputEditor: function (options) {
      return new Form.editors.Text(options);
    },
    render: function () {
      this.$el.html(_.template(this.template));
      this.$el.prepend(this.input.render().$el);

      return this;
    },
    getValue: function () {
      return this.input.getValue();
    },
    setValue: function (value) {
      this.input.setValue(value);
    },
    focus: function () {
      this.input.focus();
    },
    blur: function () {
      this.input.blur();
    },
    handleEditorEvent: function (event, editor) {
      this.trigger.call(this, event, this, editor, Array.prototype.slice.call(arguments, 2));
    }
  });

  RegistrationForm.editors.Password = CustomInput.extend({
    template: '<div class="password-input-wrap__toggle" title="Показать пароль"></div>',
    inputOptions: {
      type: 'password'
    },
    className: 'password-input-wrap',
    events: {
      'click .password-input-wrap__toggle': 'onShowPasswordButtonClick'
    },
    initialize: function (options) {
      CustomInput.prototype.initialize.call(this, options);

      this.state = new Backbone.Model({
        'show-password': false
      });
      this.listenTo(this.state, 'change:show-password', this.onShowPasswordChange);
    },
    render: function () {
      CustomInput.prototype.render.apply(this, arguments);

      this.onShowPasswordChange();

      return this;
    },
    onShowPasswordButtonClick: function (e) {
      e.preventDefault();

      this.state.set('show-password', !this.state.get('show-password'));
    },
    onShowPasswordChange: function () {
      if (this.state.get('show-password')) {
        this.input.$el.attr('type', 'text');
        this.$('.password-input-wrap__toggle').addClass('visible');
      } else {
        this.input.$el.attr('type', 'password');
        this.$('.password-input-wrap__toggle').removeClass('visible');
      }
    }
  });

  RegistrationForm.editors.InputMask = Form.editors.Text.extend({
    initialize: function (options) {
      Form.editors.Text.prototype.initialize.apply(this, arguments);

      this.mask = options.mask || {};
    },
    render: function () {
      Form.editors.Text.prototype.render.apply(this, arguments);

      this.$el.inputmask(this.mask);

      return this;
    }
  });

  RegistrationForm.editors.InputPhone = Form.editors.InputMask.extend({
    initialize: function (options) {
      Form.editors.InputMask.prototype.initialize.call(this, _.extend({}, options, {
        mask: '+7 (999) 999 - 99 - 99'
      }));
    }
  });


  RegistrationForm.editors.CustomCheckbox = Form.editors.CustomInput.extend({
    className: 'custom-checkbox custom-checkbox_bg-blue',
    template: '' +
      '<label>' +
      '  <span class="custom-checkbox__checkbox"></span>\n' +
      '  <span><%= title %></span>' +
      '</label>',
    events: {
      'change input': function (event) {
        this.trigger('change', this);
      }
    },
    createInputEditor: function (options) {
      return new Form.editors.Checkbox(_.extend({}, options, {
        schema: {'editorClass': 'checkbox'}
      }));
    },
    render: function () {
      this.$el.html(_.template(this.template)({
        title: this.schema.title
      }));
      this.$('label').prepend(this.input.render().$el);

      return this;
    }
  });

  RegistrationForm.validators.errMessages.required = 'Обязательное поле';

  return RegistrationForm;
});