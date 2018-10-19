define(function(require){
  var ListItemView = require('view/base/list-item-view');

  var template = _.template('<div class="notice-list__row">\n' +
    '                    <span class="notice-item-author">От: <%= author %></span>\n' +
    '                    <span class="notice-item-icon <%= icon %>"></span>\n' +
    '                    <span class="notice-item-order"><%= orderNumber %></span>\n' +
    '                    <span class="notice-item-title"><%= title %></span>\n' +
    '                    <span class="notice-item-date"><%= createAt %></span>\n' +
    '                  </div>\n' +
    '                 <div class="notice-deployed-wrap">' +
    '                  <div class="notice-deployed">\n' +
    '                    <div class="incoming-message">\n' +
    '                     <%= message %> \n' +
    '                    </div>\n' +
    '                  </div>\n' +
    '               </div>');

  return ListItemView.extend({
    events: {
      'click .notice-list__row': 'onToggle'
    },
    tagName: 'div',
    className: 'notice-list__item',
    initialize: function(){
      ListItemView.prototype.initialize.apply(this, arguments);
      this.template = template;
    },
    render: function(){
      var noticeType = this.model.get('type');
      var icon = 'notice-item-icon_admin';

      if (noticeType === 'auction') {
        icon = 'notice-item-icon_auction';
      } else if(noticeType === 'info') {
        icon = 'notice-item-icon_document';
      } else if(noticeType === 'tech') {
        icon = 'notice-item-icon_admin';
      } else if(noticeType === 'order') {
        icon = 'notice-item-icon_order';
      }

      this.$el.html(this.template({
        author: this.model.get('author'),
        title: this.model.get('title'),
        createAt: this.model.get('create_at'),
        message: this.model.get('message'),
        id: this.model.get('id'),
        isRead: this.model.get('read'),
        icon: icon,
        orderNumber: this.model.get('orderNumber')
      }));

      this.__toggleRead();

      return this;
    },
    onToggle: function (e) {
      e.preventDefault();
      this.$el.toggleClass('notice-list__item_deployed');
      if (!this.model.get('read')) {
        this.model.set('read', true);
        this.model.save();
        this.__toggleRead();
      }
    },
    __toggleRead: function () {
      if (!this.model.get('read')){
        this.$el.addClass('notice-list__item_new-message');
      } else {
        this.$el.removeClass('notice-list__item_new-message');
      }
    }
  })
});