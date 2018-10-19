define(function(require){
  var ListView = require('view/base/list-view'),
    UserNoticeListItemView = require('view/user/user-notice-list-item'),
    UserNoticeListDialogItemView = require('view/user/user-notice-list-dialog-item');

  var template = _.template('<div class="notice-list"></div>');

  return ListView.extend({
    template: template,
    itemView: UserNoticeListItemView,
    container: '.notice-list',
    _createItemView: function(item, index){
      if (item.get('type') === 'dialog') {
        return new UserNoticeListDialogItemView({ model: item, index: index });
      }

      return new UserNoticeListItemView({ model: item, index: index });
    }
  });
});