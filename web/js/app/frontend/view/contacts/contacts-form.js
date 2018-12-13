define(function(require){
  var CommonView = require('view/common/common-view'),
    Question = require('model/text/question'),
    Backbone = require('backbone');

  require('jquery-validate');
  require('vendor/inputmask/jquery.inputmask');

  var template = _.template('' +
    '         <div class="question-layer-wrap">\n' +
    '           <h1>Написать нам</h1>\n' +
    '           <form id="questionForm">\n' +
    '             <div class="question-wrapper">\n' +
    '               <div class="question-wrapper__row">\n' +
    '                 <div class="step-item input-text__name">\n' +
    '                   <input class="input-text " name="name" required="required" data-validate="name" data-description="name" data-describedby="name-errors" id="question_customer_name" type="text" placeholder="ФИО">\n' +
    '                   <i class="error-icon">\n' +
    '                     <span class="error-icon__message" id="name-errors"><div>Представьтесь, пожалуйста</div></span>\n' +
    '                   </i>\n' +
    '                 </div>\n' +
    '                 <div class="step-item input-text__email">\n' +
    '                   <input class="input-text " required="required" name="phone" data-validate="phone" data-description="phone" data-describedby="phone-errors" id="question_customer_phone" type="text" placeholder="Телефон">\n' +
    '                   <i class="error-icon">\n' +
    '                     <span class="error-icon__message" id="name-errors"><div>Представьтесь, пожалуйста</div></span>\n' +
    '                   </i>\n' +
    '                 </div>\n' +
    '               </div>\n' +
    '               <div class="step-item input-text__question">\n' +
    '                 <input class="input-text " required="required" name="text" type="text" data-validate="text" data-description="text" data-describedby="text-errors" id="question_customer_text" placeholder="Расскажите, что вас интересует">\n' +
    '                 <i class="error-icon">\n' +
    '                  <span class="error-icon__message" id="name-errors">\n' +
    '                    <div>Представьтесь, пожалуйста</div>\n' +
    '                  </span>\n' +
    '                 </i>\n' +
    '               </div>\n' +
    '             </div>\n' +
    '             <div class="step-item step-item-checkbox">\n' +
    '               <div class="custom-checkbox font-alegreya" name="tos">\n' +
    '                 <label>\n' +
    '                   <input type="checkbox" id="question_customer_tos" required="required" class="checkbox" data-validate="tos" data-description="tos" data-describedby="tos-errors">\n' +
    '                   <span class="custom-checkbox__checkbox"></span>\n' +
    '                   <span>Я согласен с условиями <a target="_blank" href="{% if setting(\'tos_article\') %}{{ path(\'article_show\',{\'slug\':setting(\'tos_article\').slug}) }}{% else %}#{% endif %}">передачи информации</a></span>\n' +
    '                 </label>\n' +
    '               </div>\n' +
    '               <i class="error-icon">\n' +
    '                 <span class="error-icon__message"></span>\n' +
    '               </i>\n' +
    '             </div>\n' +
    '             <div class="step-item" style="display: block">\n' +
    '               <button class="button button-transparent-white" type="submit"><span>отправить</span></button>\n' +
    '               <div class="submit-loader-wrap" style="display: none"><span class="submit-loader"></span></div>\n             ' +
    '             </div>\n' +
    '           </form>\n' +
    '         </div>\n' +
    '        <div class="question-layer-success" style="display: none">\n' +
    '          <span class="question-layer-message__text">Спасибо!<br>\n' +
    '            Сыровары обязательно<br>\n' +
    '            вам ответят.\n' +
    '          </span>\n' +
    '        </div>\n' +
    '        <div class="question-layer-error" style="display: none">\n' +
    '          <span class="question-layer-message__text">Что-то пошло не так...<br>\n' +
    '            Не отчаивайтесь и попробуйте<br>\n' +
    '            ещё раз!\n' +
    '          </span>\n' +
    '        </div>');

  return CommonView.extend({
    tagName: 'div',
    className:'layer-questions',
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
      CommonView.prototype.initialize.apply(this, arguments);
      this.isInvalid = true;
      this.address = options.address;

      this.model = new Question();
      this.model.set('source', 'contacts');
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
      this.$('#questionForm').validateDestroy();
      this.$('#questionForm').validate({
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
      $('.submit-loader-wrap').show();
      e.preventDefault();
      var self = this;
      this.model.save(null, {
        success: function () {
          self.$el.parents().find('.question-layer-wrap').hide();
          self.$el.parents().find('.question-layer-success').show();
          $('.submit-loader-wrap').hide();
        },
        error: function () {
          self.$el.parents().find('.question-layer-wrap').hide();
          self.$el.parents().find('.question-layer-error').show();
          $('.submit-loader-wrap').hide();
        }
      })
    }
  });
});