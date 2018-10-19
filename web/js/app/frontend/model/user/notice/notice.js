define(function(require){
  var Backbone = require('backbone');

  return Backbone.Model.extend({
    urlRoot: urlPrefix + '/api/notice',
    defaults: {
      read: true,
      message: '',
      author: '',
      create_at: '',
      title: ''
    }
  });
});