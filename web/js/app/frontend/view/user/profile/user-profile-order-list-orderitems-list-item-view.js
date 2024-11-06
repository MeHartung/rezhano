define(function(require){
  var ListItemView = require('view/base/list-item-view');

  var template = _.template(''+
    '<a href="<%= url %>" class="order-row__name order-row__item"><%= name %></a>\n' +
    '<span class="order-row__count order-row__item"><%= quantity %> шт.</span>\n' +
    '<span class="order-row__value order-row__item"><%= cost %> Р</span>' +
'');

  return ListItemView.extend({
    events: {
    },
    tagName: 'div',
    className: 'popup-layer__order-row',
    initialize: function(){
      ListItemView.prototype.initialize.apply(this, arguments);
    },
    render: function(){
      this.$el.html(template({
        name: this.model.get('name'),
        quantity: this.model.get('quantity'),
        cost: this.model.get('cost'),
        url: this.model.get('product').url
      }));

      return this;
    }
  })
});