define(function(require){
  var Backbone = require('backbone'),
    Question = require('model/text/question'),
    CommonView = require('view/common/common-view');

  return CommonView.extend({
    initialize: function () {
      CommonView.prototype.initialize.apply(this, arguments);
      // var q = new Question();
      // q.set('text', 'asd');
      // q.save();
    },
    render: function () {
      CommonView.prototype.render.apply(this, arguments);
    }
  })
});