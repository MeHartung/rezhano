define(function (require) {
  var UserNoticeListItem = require('view/user/user-notice-list-item'),
    Backbone = require('backbone'),
    DialogMessage = require('model/user/dialog/message/message'),
    UserNoticeDialogMessageListView = require('view/user/notice/user-notice-dialog-message-list-view');

  var template = _.template('<div class="notice-list__row">\n' +
    '                    <span class="notice-item-author">От: <%= author %></span>\n' +
    '                    <span class="notice-item-icon notice-item-icon_admin"></span>\n' +
    '                    <span class="notice-item-order"></span>\n' +
    '                    <span class="notice-item-title"><%= title %></span>\n' +
    '                    <span class="notice-item-date"><%= createAt %></span>\n' +
    '                  </div>\n' +
    '                 <div class="notice-deployed-wrap">' +
    '                  <div class="notice-deployed">\n' +
    '                    <div class="incoming-message">\n' +
    '                      <%= message %> \n' +
    '                    </div>\n' +
    '                  </div>\n' +
    '<% if(showTextarea) { %>' +
    '                    <div class="outgoing-question-form">\n' +
    '                    <span class="user-icon"></span>\n' +
    '                    <div class="custom-textarea">\n' +
    '                      <textarea name="" id="" cols="30" rows="10" placeholder="Введите текст, чтобы ответить"></textarea>\n' +
    '                    </div>\n' +
    '                    <br><button class="button send-form">Отправить</button>' +
    '                    <span class="error-list"></span>' +
    '                  </div>\n' +
    ' <% } %>            ' +
    '                  <div class="last-messages"></div>\n' +
    '               </div>');

  return UserNoticeListItem.extend({
    events: function () {
      return _.extend({}, UserNoticeListItem.prototype.events, {
        'click .send-form': 'onClickSendForm',
        'keydown' : 'onFormInput'
      });
    },
    initialize: function () {
      UserNoticeListItem.prototype.initialize.apply(this, arguments);
      this.messagesCollection = new Backbone.Collection(this.model.get('dialog').messages, {
        model: DialogMessage
      });
      this.messagesView = new UserNoticeDialogMessageListView({
        collection: this.messagesCollection
      });
      this.template = template;
    },
    render: function () {
      var lastMessage = (this.messagesCollection.length > 0)?this.messagesCollection.first():null;
      this.$el.html(this.template({
        author: this.model.get('author'),
        title: this.model.get('title'),
        createAt: this.model.get('create_at'),
        message: this.model.get('message'),
        id: this.model.get('id'),
        isRead: this.model.get('read'),
        showTextarea: this.messagesCollection.length > 1 && !lastMessage.get('isOwner')
      }));

      if (!this.model.get('read')) {
        this.$el.addClass('notice-list__item_new-message');
      }

      this.messagesView
        .setElement(this.$('.last-messages'))
        .render();

      return this;
    },
    onClickSendForm: function () {
      this.__sendMessage(this.$('textarea').val());
    },
    __sendMessage: function (text) {
      this.$('.send-form').attr('disabled', true);
      var message = new DialogMessage({
        dialogId: this.model.get('dialog').id,
        message: text
      });
      var self = this;
      message.on('error', this.onMessageValidateError);
      message.save(message.attributes, {
        success: function (model, response, options) {
          self.messagesCollection.unshift(model);
          self.$('.send-form').removeAttr('disabled');
          self.$('textarea').val('');
          //Ваше сообщение успешно отправлено
          $('.custom-textarea').removeClass('error');
          $('.error-list').text('');
          this.$('.outgoing-question-form').remove();
        },
        error: function (model, xhr, options) {
          self.$('.send-form').removeAttr('disabled');

          $('.custom-textarea').addClass('error');
          $('.error-list').text(xhr);
        }
      });
    },
    onMessageValidateError: function (model, error) {
      // console.log(model);
      // console.log(error, error.responseJSON.errors.message[0]);
      $('.custom-textarea').addClass('error');
      $('.error-list').text(error.responseJSON.errors.message[0]);
    },
    onFormInput: function () {
      $('.custom-textarea').removeClass('error');
      $('.error-list').text('');
    }
  })
});