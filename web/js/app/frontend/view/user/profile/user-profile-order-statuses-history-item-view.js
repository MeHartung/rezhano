define(function(require){
  var ListItemView = require('view/base/list-item-view');

  var template = _.template('\
  <div class="popup-order-status__row passed-stage">\
    <span class="popup-order-status__date"><%= date %></span>\
    <span class="popup-order-status__info-tick"></span>\
    <span class="popup-order-status__status-info"><%= name %></span>\
  </div>');

  return ListItemView.extend({
    events: {
    },
    tagName: 'div',
    className: 'popup-order-status',
    initialize: function(){
      ListItemView.prototype.initialize.apply(this, arguments);
    },
    render: function(){
      this.$el.html(template({
        date: this.model.get('created_at').match(/\d{2}\.\d{2}/)[0],
        name: this.model.get('status').name
      }));

      return this;
    }
  })
});