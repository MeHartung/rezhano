define(function (require) {
  var Backbone = require('backbone'),
    QuantityWidget = require('view/cart/widget/quantity-widget')
  ;

  require('jscrollpane');
  require('jquery.mousewheel');

  var template = _.template(require('templates/catalog/product/product-quick-view-tpl'));

  return Backbone.View.extend({
    events: {
      'click .button_add-to-cart': 'onAddToCartButtonClick'
    },
    initialize: function (options) {
      this.options = _.extend({}, options);

      this.cart = options.cart;
      this.quantityWidget = new QuantityWidget({
        model: this.cart.createItem({
          productId: this.model.get('id'),
          quantity: this.model.get('min_count')
        }),
        max: this.model.get('available_stock'),
        min: this.model.get('min_count'),
        step: this.model.get('count_step')
      });
    },
    render: function () {
      var _self = this;

      this.$el.html(template({
        name: this.model.get('name'),
        isPurchasable: this.model.get('isPurchasable'),
        images: this.model.get('images') instanceof Array && this.model.get('images') ? this.model.get('images') : '/images/medium-no_photo.png',
        preview_image: this.model.get('preview_image') ? this.model.get('preview_image') : '/images/medium-no_photo.png',
        product_url: this.model.get('url'),
        package: this.model.get('package'),
        units: this.model.get('units'),
        price: Number(this.model.get('price')).toCurrencyString('₽', 0)
      }));

      $(function () {
        _self.$('.scroll-pane').jScrollPane();
        _self.$('.layer__images').slick({
          dots: false,
          arrows: true,
          infinite: true
        });
      });

      this.quantityWidget.setElement(this.$('.product-item__controls')).render();

      return this;
    },
    onAddToCartButtonClick: function (e) {
      e.preventDefault();

      var self = this,
        cart = this.cart;

      var cartItem = this.cart.createItem({
        product_id: this.model.get('id'),
        quantity: this.quantityWidget.model.get('quantity')
      });

      cartItem
        .save()
        .done(function () {
          cart.items.set(cartItem, {
            remove: false
          });
          cart.trigger('item:add', cartItem, cartItem.quantity);
          self.quantityWidget.model.set({quantity: 1});
        });
    }
  });
});