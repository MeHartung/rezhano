define(function(require){
  var ListItemView = require('view/base/list-item-view');

  var template = _.template('\
    <div class="previous-question__title">\
    <span class="previous-question__name">От: <%= author %></span>\
  <span class="previous-question__date"><%= date %></span>\
  </div>\
  <div class="previous-question__text">\
    <%= message %>\
</div>\
');

  return ListItemView.extend({
    tagName: 'div',
    className: 'previous-question',
    render: function(){
      this.$el.html(template({
        author: this.model.get('userName'),
        date: this.model.get('date'),
        message: this.model.get('message')
      }));

      return this;
    },
  })
});