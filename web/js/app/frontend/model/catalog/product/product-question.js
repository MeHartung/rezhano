/**
 * Created by Денис on 28.08.2017.
 */
define(function(require){
  var Backbone = require('backbone');

  return Backbone.Model.extend({
    schema: {
      'name': {
        type: 'Text',
        validators: [
          { type: 'required', message: 'Введите Ваше имя' }
        ],
        title: "ФИО:"
      },
      'email': {
        type: 'Text',
        validators: [
          { type: 'required', message: 'Введите E-mail, на который можно отправить ответ' },
          { type: 'email', message: 'Вы ввели неверный E-mail' }
        ],
        title: 'E-mail'
      },
      'text': {
        type: 'TextArea',
        validators: [
          { type: 'required', message: 'Введите текст вопроса'}
        ],
        title: "Ваш вопрос (не более 2000 знаков):",
        editorAttrs: {
          cols: 40,
          maxlength: 2000
        }
      }
    },
    url: function(){
      return urlPrefix + '/api/products/' + this.slug + '/question'
    },
    initialize: function(attrs, options){
      this.slug = options.slug
    }
  });
});