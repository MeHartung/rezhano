/* 
 * @author Max D. Selezenev <selezenev at artsofte.ru>
 * @version SVN: $Id$
 * @revision SVN: $Revision$ 
 */


define(function(require){
  var Backbone = require('backbone'),
      PickupDetailsView =  require('view/cart/checkout/pickup/pickup-details-panel');
      
      
  var template = _.template('\
      <a href="<%= url %>" class="no-underline">\n\
        <span class="text"><%= title %></span>\n\
        <% if (model == "sku") { %>\n\
          <small class="grey"><% _.each(attributes, function(attribute, i) { %><%= attribute.name %>: <%= attribute.value %><% if (i < attributes.length - 1) { %>, <% } %><% }) %></small>\n\
        <% } %>\n\
      </a>\n\
      <p class="order-item-price"><%= quantity %> <%= units %>. — <b><%= (cost).toCurrencyString() %></b></p>');
  

  return Backbone.View.extend({
    tagName: 'li',
    initialize: function(){
 
   
      this.render();
    },
    render: function(){
      this.$el.html(template($.extend({}, this.model.toJSON(), {
        units: this.model.getProduct().get('units')
      })));
    },

    dispose: function(){
      this.undelegateEvents();      
      this.stopListening();
      this.remove();
    } 
    
  })
  
})