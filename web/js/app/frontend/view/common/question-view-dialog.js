define(function(require){
  var ModalDialog = require('view/dialog/base/modal-dialog-view'),
      Question = require('model/text/question'),
      QuestionForm = require('view/common/question-view-dialog-form'),
      Backbone = require('backbone');

  require('jquery-validate');
  require('vendor/inputmask/jquery.inputmask');

  var template = _.template('\
    <div class="layer__close"></div>\n\
    <div class="question-layer-wrap">\
      <h2>Какой у вас вопрос?</h2>\n\
      <form id="questionForm">\
        <div class="question-wrapper">\
          <div class="question-wrapper__row">\
            <div class="step-item input-text__name">\
              <input class="input-text " name="name" required="required" data-validate="name" data-description="name" data-describedby="name-errors" id="question_customer_name" type="text" placeholder="ФИО">\n\
                <i class="error-icon">\n\
                   <span class="error-icon__message" id="name-errors"><div>Представьтесь, пожалуйста</div></span>\n\
                </i>\n\
            </div>\
            <div class="step-item input-text__email">\
              <input class="input-text " required="required" name="phone" data-validate="phone" data-description="phone" data-describedby="phone-errors" id="question_customer_phone" type="text" placeholder="Телефон">\n\
              <i class="error-icon">\n\
                <span class="error-icon__message" id="phone-errors"><div>Укажите Ваш номер телефона</div></span>\n\
              </i>\n\
             </div>\
          </div>\
          <div class="step-item input-text__question">\
              <input class="input-text " required="required" name="text" type="text" data-validate="text" data-description="text" data-describedby="text-errors" id="question_customer_text" placeholder="Расскажите, что вас интересует">\n\
              <i class="error-icon">\n\
                <span class="error-icon__message" id="text-errors"><div>Введите текст вопроса</div></span>\n\
              </i>\n\
            </div>\
        </div>\n\
        <div class="step-item step-item-checkbox" name="tos">\n\
          <div class="custom-checkbox font-alegreya" >\n\
            <label>\n\
              <input type="checkbox" name="tos" id="question_customer_tos" required="required" class="checkbox" data-validate="tos" data-description="tos" data-describedby="tos-errors" >\n\
              <span class="custom-checkbox__checkbox"></span>\n\
              <span>Я согласен с условиями <a href="<%= tosUrl %>">передачи информации</a></span>\n\
            </label>\n\
          </div>\n\
          <i class="error-icon">\n\
            <span class="error-icon__message" id="tos-errors">Вы должны принять условия</span>\n\
          </i>\n\
        </div>\n\
        <div class="step-item" style="display: block">\n\
          <button id="questionFormSubmit" class="button button_black" type="submit"><span>отправить</span></button>\n\
        </div>\
      </form>\n\
    </div>\
    <div class="question-layer-message question-layer-success" style="display: none">\
      <span class="question-layer-message__text">Спасибо!<br>\n' +
            'Сыровары обязательно<br>\n' +
            'вам ответят.</span>\n\
        </div>\
        <div class="question-layer-message question-layer-error" style="display: none">\
          <span class="question-layer-message__text">Что-то пошло не так...<br>\n' +
            'Не отчаивайтесь и попробуйте<br>\n' +
            'ещё раз!</span>\n\
        </div>\
          ');

  return ModalDialog.extend({
    tagName: 'div',
    className: 'layer layer-questions',
    template: template,
    events: {
      'click .layer__close': 'close',
      // 'click #questionFormSubmit': 'onSubmit'
      'change #question_customer_name' : 'onNameChange',
      'change #question_customer_phone' : 'onMailOrPhoneChange',
      'change #question_customer_text' : 'onTextChange',
      'change #question_customer_tos' : 'onTosChange'
    },
    initialize: function(options){
      ModalDialog.prototype.initialize.apply(this, arguments);
      this.isInvalid = true;
      this.address = options.address;

      this.model = new Question();

      this.tosArticle = new Backbone.Model(ObjectCache.TosArticle || {});
    },
    render: function(){
      $.validateExtend({
        phone: {
          required: true,
          pattern: /\+7\s\(\d{3}\)\s\d{3}\-\d{2}\-\d{2}/
        },
        name: {
          required: true
        },
        text: {
          required: true
        },
        tos: {
          required: true
        }
      });

      var tosUrl = '#';
      if (this.tosArticle.get('slug')){
        tosUrl = urlPrefix + '/' + this.tosArticle.get('slug');
      }

      this.$el.html(template({
        tosUrl: tosUrl,
      }));

      this.$('#question_customer_phone').inputmask('+7 (999) 999-99-99');
      this.initValidation();

      return this;
    },
    initValidation: function () {
      var self = this;
      this.$el.find('#questionForm').validateDestroy();
      this.$el.find('#questionForm').validate({
        sendForm: false,
        onChange: true,
        onBlur: true,
        onSubmit: true,
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
          phone: {
            required: '<div>Укажите Ваш телефон</div>',
            pattern: '<div>Введен неверный номер телефона</div>'
          },
          text: {
            required : '<div>Введите текст вопроса</div>'
          },
          tos: {
            required : '<div>Вы должны принять условия</div>'
          }
        },
        invalid: function(event, options){
          self.isInvalid = true;
        },
        valid: function(event, options){
          self.isInvalid = false;
          self.$('#questionFormSubmit').attr('disabled', 'disabled');
          self.onSubmit(event);
        }
      });
    },
    show: function(){
      this.$overlay.stop().fadeIn();
      this.$el.stop().fadeIn();
    },
    open: function () {
      ModalDialog.prototype.open.apply(this, arguments);
      var self = this;
    },
    close: function () {
      this.remove();
      ModalDialog.prototype.close.apply(this, arguments);
    },
    onNameChange: function (e) {
      this.model.set('fio', $(e.currentTarget).val());
    },
    onMailOrPhoneChange: function (e) {
      // if (this.isValidEmail($(e.currentTarget).val())) {
      //   this.model.set('email', $(e.currentTarget).val());
      // } else {
        this.model.set('phone', $(e.currentTarget).val());
      // }
    },
    onTextChange: function (e) {
      this.model.set('text', $(e.currentTarget).val());
    },
    isValidEmail: function(val){
      return /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(val)
    },
    onTosChange: function (e) {
      this.model.set('tos', $(e.currentTarget).val());
    },
    onSubmit: function (e) {
      e.preventDefault();
      var self = this;
      this.model.save(null, {
        success: function () {
          self.$('.question-layer-wrap').hide();
          self.$('.question-layer-success').show();
        },
        error: function () {
          self.$('.question-layer-wrap').hide();
          self.$('.question-layer-error').show();
        }
      })
    }
  });
});