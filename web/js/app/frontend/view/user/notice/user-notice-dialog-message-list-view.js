define(function(require){
  var ListView = require('view/base/list-view'),
    UserNoticeDialogMessageListItemView = require('view/user/notice/user-notice-dialog-message-list-item-view');

  var template = _.template('');

  return ListView.extend({
    template: template,
    itemView: UserNoticeDialogMessageListItemView,
    container: ''
  });
});