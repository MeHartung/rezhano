define(function(require){
  var Backbone = require('backbone');

  return Backbone.Model.extend({
    idAttribute: 'slug',
    urlRoot: function() {
      return urlPrefix + '/api/user/upload/'+this.get('id');
    }
  });
});