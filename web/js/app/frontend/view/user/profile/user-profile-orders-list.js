define(function(require){
  var ListView = require('view/base/list-view'),
      ListItemView = require('view/user/profile/user-profile-orders-list-item-view');

  var template = _.template(''+
    '<div class="order-list-header">\n' +
    '  <span class="order-list-header__item-title order-list-item__number">Номер заказа</span>\n' +
    '  <span class="order-list-header__item-title order-list-item__status">Статус</span>\n' +
    '  <span class="order-list-header__item-title order-list-item__date">Дата</span>\n' +
    '  <span class="order-list-header__item-title order-list-item__value">Стоимость</span>\n' +
    '</div>');

  return ListView.extend({
    template: template,
    itemView: ListItemView,
    _append: function(container, view, index){
      view.undelegateEvents();
      if (index === 0){
        view.$el.insertAfter(this.$('.order-list-header'));
      } else if (!!index && container.children().length >= index){
        view.$el.insertAfter(container.children(':eq('+(index)+')'));
      } else {
        container.append(view.$el);
      }
      view.delegateEvents();
    },
  });
});