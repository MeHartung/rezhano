define(function(require){
  var ListItemView = require('view/base/list-item-view'),
      Backbone = require('backbone');
  
  var template = _.template('\
<input type="radio" autocomplete="off" name="checkout[payment_method]" \
  id="checkout_payment_method_<%= id %>" class="<% if(enabled === false) { %> disabled<% } %>" value="<%= id %>" \
    <% if(enabled === false) { %>disabled="disabled"<% } %> <% if (active === true && enabled === true) { %>checked="checked"<% } %> \
      data-required data-describedby="payment-method-errors" data-description="payment-method" />\
<label for="checkout_payment_method_<%= id %>">\
<span class="vmpayment">\
 <span class="vmpayment_name"><%= name %></span>\
 <span class="vmpayment_description vmpayment_description_6"><%= help %></span>\
 <% if (fee) { %>\
 <span class="vmpayment_cost"> (<%= fee.toCurrencyString() %>)</span>\
 <% } %>\
</span>\
</label>\
');

  return ListItemView.extend({
    className: 'radio',
    events: 
    {
      'change input' : 'onChange'
    },
    initialize: function(){
      this.render();
      this.model.on('change', this.render, this);
    },
    render: function(){
      this.$el.html(template(this.model.toJSON()));
    },
    onChange: function(event){
      var el = $(event.currentTarget);
      // Вызываем событие только если выбран данный элемент
      if(el.is(':checked')) {
        this.trigger('item:selected', this, this.model);
      }
    },
    dispose: function(){
      this.undelegateEvents();      
      this.stopListening();
      this.remove();
    }        
  })
})

