define(function(require){
  var Backbone = require('backbone');

  var template = _.template('\
  <span class="club-price-popup__message">Теперь вы можете покупать товары по клубным ценам, смотреть историю и статус заказов!</span>\
  <div class="club-price-popup__close layer__close"></div>\
  ');

  return Backbone.View.extend({
    tagName: 'div',
    id: 'clubPricePopup',
    className: 'club-price-popup',
    events: {
      'click .club-price-popup__close': 'onClickClose'
    },
    initialize: function(options){

    },
    render: function(){
      this.$el.html(template({

      }));

      return this;
    },
    onClickClose: function(e){
      e.preventDefault();
      $.ajax({
        url: urlPrefix + '/api/cabinet/club-popup',
        method: 'DELETE'
      });
      this.$el.remove();
    }
  });
});