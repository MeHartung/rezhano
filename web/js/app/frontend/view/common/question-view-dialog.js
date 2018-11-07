define(function(require){
  var ModalDialog = require('view/dialog/base/modal-dialog-view');

  var template = _.template('\
    <h2>Какой у вас вопрос?</h2>\n\
    <div class="layer__close"></div>\n\
    <div class="question-wrapper">\
      <input class="input-text" type="text" placeholder="ФИО">\
      <input class="input-text" type="text" placeholder="Телефон или эл.почта">\
      <input class="input-text" type="text" placeholder="Расскажите, что вас интересует">\
    </div>\n\
  ');

  return ModalDialog.extend({
    tagName: 'div',
    className: 'layer layer-map',
    template: template,
    events: {
      'click .layer__close': 'close'
    },
    initialize: function(options){
      ModalDialog.prototype.initialize.apply(this, arguments);
      this.address = options.address;
    },
    render: function(){
      // ModalDialog.prototype.render.apply(this, arguments);

      this.$el.html(template());

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