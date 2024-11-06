define(function(require){
  var ListItemView = require('view/user/list-view/order-history-list-item');

  var template = _.template('\
<td class="op_whitebox"><a class="dashed js-get-order"><%= document_number %></a></td>\
<td class="op_whitebox"><%= date %></td>\
<td class="op_whitebox"><%= total.toCurrencyString() %></td>\
');

  return ListItemView.extend({
    tagName: 'tr',
    className: 'order-history',
    initialize: function(){
      ListItemView.prototype.initialize.apply(this, arguments);
    },
    render: function(){

      this.$el.html(template({
        document_number: this.model.get('document_number'),
        date: this.model.get('created_at'),
        total: Number(this.model.get('total'))
      }));

      return this;
    }
  })
})