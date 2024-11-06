define(function(require){
  var ListView = require('view/base/list-view'),
    ListItemView = require('view/user/profile/user-profile-order-statuses-history-item-view');

  return ListView.extend({
    itemView: ListItemView,
    remove: function () {
      _.each(this.items, function(item){
        item.view.remove();
      });
      ListView.prototype.remove.apply(this, arguments);
    }
  });
});