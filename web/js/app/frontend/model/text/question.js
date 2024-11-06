define(function(require){
  var Backbone = require('backbone');

  return Backbone.Model.extend({
    urlRoot: urlPrefix + '/question',
    defaults: {
      source: 'question',
      phone: null,
      email: null,
      text: null
    },
    // validate: function(attrs, options) {
    //   if (!attrs.text){
    //     return 'Необходим текст сообщения';
    //   }
    //   if (!attrs.phone || !attrs.email) {
    //     return "Необходимо указать номер телефона или email";
    //   }
    // }
  });
});