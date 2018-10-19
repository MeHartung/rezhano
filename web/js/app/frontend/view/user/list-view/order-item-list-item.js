define(function(require){
  var ListItemView = require('view/base/list-item-view');

  var template = _.template('\
    <a href="<%= link %>" class="order-item__link" >\n\
      <img class="order-item__image" src="<%= image %>" alt="" >\n\
      <span class="order-item__name"><%= product_name %></span>\n\
    </a>\n\
    <span class="order-quantity"><%= quantity %> шт.</span>\n\
    <span class="order-item__price"><%= cost %></span>\n\
  ');

  return ListItemView.extend({
    className: 'order-item',
    initialize: function(){
      ListItemView.prototype.initialize.apply(this, arguments);
    },
    render: function(){
      this.$el.html(template({
        quantity: this.model.get('quantity'),
        cost: this.model.get('cost').toCurrencyString(),
        product_name: this.model.get('product').name,
        product_sku: this.model.get('product').sku,
        product_url: this.model.get('product').url,
        link: urlPrefix + '/products/' + this.model.get('product').slug,
        image: this.model.get('product')['preview_image'] ? this.model.get('product')['preview_image'] : '/images/no_photo.png'
      }));

      return this;
    }
  })
});