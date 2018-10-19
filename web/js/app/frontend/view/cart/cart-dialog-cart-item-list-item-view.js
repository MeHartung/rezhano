/**
 * Created by Денис on 06.06.2017.
 */
define(function(require){
  var ListItemView = require('view/base/list-item-view');

  var template = _.template('\
  <div class="order-wrap__item">\
    <img class="order-pic" src="/images/cube.jpg" alt="" >\
    <a href="<%= product_url %>" class="order-name"><%= name %></a>\
    <span class="order-quantity"><%= quantity %> шт.</span>\
    <span class="order-price"><%= price %> ₽</span>\
  </div>\
  ');

  return ListItemView.extend({
    tagName: 'div',
    className: 'cart-item',
    render: function(){
      this.$el.html(template({
        name: this.model.get('name'),
        quantity: this.model.get('quantity'),
        price: this.model.get('price'),
        product_url: this.model.get('product').url
      }));

      return this;
    }
  })
})