/**
 * Created by Денис on 23.06.2017.
 */
define(function(require){
  var Backbone = require('backbone');

  var template = _.template('\
<span class="payment-info__title">К оплате:</span>\
<span class="payment-info__value"><%= Number(subtotal).toCurrencyString() %></span>\
')

  return Backbone.View.extend({
    className: 'cards-container__payment-info',
    initialize: function(){
      this.listenTo(this.model, 'change', this.render);
    },
    render: function(){
      this.$el.html(template({
        subtotal: this.model.get('subtotal')
      }));

      return this;
    }
  })
});