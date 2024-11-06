define(function(require){
  var ListItem = require('view/base/list-item-view');

  var template = _.template('<a class="city-select-link" href="#"><%= name %></a>')

  return ListItem.extend({
    events: {
      'click a': 'onClick'
    },
    tagName: 'li',
    initialize: function(options){
      this.location = options.location;

      ListItem.prototype.initialize.apply(this, arguments);
    },
    render: function(){
      this.$el.html(template({
        name: this.model.get('name')
      }));

      return this;
    },
    onClick: function(e){
      e.preventDefault();

      this.location.setCity(this.model);
    }
  });
});