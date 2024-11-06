define(function(require){
  var Backbone = require('backbone');

  return Backbone.Model.extend({
    urlRoot: function () {
      return urlPrefix + '/api/dialog/message/'+this.get('dialogId');
    },
    defaults: {
      message: '',
      dialogId: null
    },
    validate: function(attrs, options) {
      if (!attrs.dialogId) {
        return "Требуется указать id диалога";
      }
    },
    toJSON: function(options) {
      var data = _.clone(this.attributes);
      delete data.dialogId;
      return data;
    },
  });
});