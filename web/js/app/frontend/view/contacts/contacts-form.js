define(function(require){
  var CommonView = require('view/common/common-view'),
    Question = require('model/text/question'),
    Backbone = require('backbone');

  require('jquery-validate');
  require('vendor/inputmask/jquery.inputmask');

  return CommonView.extend({
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

      // this.$el.html(template({
      //   tosUrl: tosUrl,
      // }));

      this.$('#question_customer_phone').inputmask('+7 (999) 999-99-99');
      this.initValidation();

      return this;
    },
    initValidation: function () {
      var self = this;
      this.$el.validateDestroy();
      this.$el.validate({
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
    // show: function(){
    //   this.$overlay.stop().fadeIn();
    //   this.$el.stop().fadeIn();
    // },
    // open: function () {
    //   ModalDialog.prototype.open.apply(this, arguments);
    //   var self = this;
    // },
    // close: function () {
    //   this.remove();
    //   ModalDialog.prototype.close.apply(this, arguments);
    // },
    onNameChange: function (e) {
      this.model.set('fio', $(e.currentTarget).val());
      console.log(this.model)
    },
    onMailOrPhoneChange: function (e) {
      // if (this.isValidEmail($(e.currentTarget).val())) {
      //   this.model.set('email', $(e.currentTarget).val());
      // } else {
      this.model.set('phone', $(e.currentTarget).val());
      // }
      console.log(this.model)
    },
    onTextChange: function (e) {
      this.model.set('text', $(e.currentTarget).val());
      console.log(this.model)
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
          self.$el.parents().find('.question-layer-wrap').hide();
          self.$el.parents().find('.question-layer-success').show();
        },
        error: function () {
          self.$el.parents().find('.question-layer-wrap').hide();
          self.$el.parents().find('.question-layer-error').show();
        }
      })
    }
  });
});