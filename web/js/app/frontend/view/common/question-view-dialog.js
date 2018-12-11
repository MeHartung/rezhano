define(function(require){
  var ModalDialog = require('view/dialog/base/modal-dialog-view'),
      Backbone = require('backbone');

  var template = _.template('\
    <h2>Какой у вас вопрос?</h2>\n\
    <div class="layer__close"></div>\n\
    <form action="">\
      <div class="question-wrapper">\
        <div class="question-wrapper__row">\
          <div class="step-item input-text__name">\
            <input class="input-text " type="text" placeholder="ФИО">\n\
              <i class="error-icon">\n\
                 <span class="error-icon__message" id="name-errors"><div>Представьтесь, пожалуйста</div></span>\n\
              </i>\n\
          </div>\
          <div class="step-item input-text__email">\
            <input class="input-text " type="text" placeholder="Телефон или эл.почта">\n\
            <i class="error-icon">\n\
              <span class="error-icon__message" id="name-errors"><div>Представьтесь, пожалуйста</div></span>\n\
            </i>\n\
           </div>\
        </div>\
        <div class="step-item input-text__question">\
            <input class="input-text "  type="text" placeholder="Расскажите, что вас интересует">\n\
            <i class="error-icon">\n\
              <span class="error-icon__message" id="name-errors"><div>Представьтесь, пожалуйста</div></span>\n\
            </i>\n\
          </div>\
      </div>\n\
      <div class="step-item step-item-checkbox">\n\
        <div class="custom-checkbox font-alegreya" name="tos">\n\
          <label>\n\
            <input type="checkbox" id="" name="" required="required" class="checkbox" data-validate="" data-description="" data-describedby="" value="">\n\
            <span class="custom-checkbox__checkbox"></span>\n\
            <span>Я согласен с условиями <a href="<%= tosUrl %>">передачи информации</a></span>\n\
          </label>\n\
        </div>\n\
        <i class="error-icon">\n\
          <span class="error-icon__message" ></span>\n\
        </i>\n\
      </div>\n\
      <div class="step-item" style="display: block">\n\
        <a href="" class="button button_black"><span>отправить</span></a>\n\
      </div>\
    </form>\n\
      ');

  return ModalDialog.extend({
    tagName: 'div',
    className: 'layer layer-questions',
    template: template,
    events: {
      'click .layer__close': 'close'
    },
    initialize: function(options){
      ModalDialog.prototype.initialize.apply(this, arguments);
      this.address = options.address;
      this.tosArticle = new Backbone.Model(ObjectCache.TosArticle || {});
    },
    render: function(){
      // ModalDialog.prototype.render.apply(this, arguments);
      var tosUrl = '#';
      if (this.tosArticle.get('slug')){
        tosUrl = urlPrefix + '/' + this.tosArticle.get('slug');
      }

      this.$el.html(template({
        tosUrl: tosUrl,
      }));

      return this;
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
  });
});