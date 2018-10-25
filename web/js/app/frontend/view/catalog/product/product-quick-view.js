define(function(require){
    var Backbone = require('backbone'),
        QuantityWidget = require('view/cart/widget/quantity-widget')
    ;

    var template = _.template(require('templates/catalog/product/product-quick-view-tpl'));

    return Backbone.View.extend({
      events: {
          'click .button_add-to-cart': 'onAddToCartButtonClick'
      },
      initialize: function(options){
          this.options = _.extend({
          }, options);

          this.cart = options.cart;
          this.quantityWidget = new QuantityWidget({
              model: this.cart.createItem({
                  productId: this.model.get('id')
              })
          });
      },
      render: function(){
          this.$el.html(template({
            isPurchasable: this.model.get('isPurchasable')
          }));

          this.quantityWidget.setElement(this.$('.product-item__controls')).render();

          return this;
      },
        onAddToCartButtonClick: function(e){
            e.preventDefault();

            var self = this,
                cart = this.cart;

            var cartItem = this.cart.createItem({
                product_id: this.model.get('id'),
                quantity: this.quantityWidget.model.get('quantity')
            });

            cartItem
                .save()
                .done(function(){
                    cart.items.set(cartItem, {
                        remove: false
                    });
                    cart.trigger('item:add', cartItem, cartItem.quantity);
                    self.quantityWidget.model.set({ quantity: 1 });
                });
        }
    });
});