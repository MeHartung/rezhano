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
          product: this.model.attributes,
          productId: this.model.get('id'),
          quantity: this.model.get('min_count')
        }),
        min: this.model.get('min_count'),
        step: this.model.get('count_step')
      });

      this.listenTo(this.cart, 'item:add', this.onCartItemAdd)
    },
    render: function () {
      var _self = this;
      var thumbnails = this.model.get('thumbnails');
      this.$el.html(template({
        name: this.model.get('name'),
        isPurchasable: this.model.get('isPurchasable'),
        // images: this.model.get('images') instanceof Array && this.model.get('images') ? this.model.get('images') : '/images/medium-no_photo.png',
        images: this.model.get('gallery_images') instanceof Array && this.model.get('gallery_images') ? this.model.get('gallery_images') : '/images/medium-no_photo.png',
        preview_image: this.model.get('preview_image') ? this.model.get('preview_image') : '/images/medium-no_photo.png',
        product_url: this.model.get('url'),
        package: this.model.get('package'),
        units: this.model.get('units'),
        price: Number(this.model.get('measuredPartPrice')).toCurrencyString('₽', 0),
        description: this.model.get('description'),
        attributes: this.model.get('attributes'),
        productId: this.model.get('id')
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
        cart = this.cart,
        quantity = +this.quantityWidget.model.get('quantity'),
        step = +this.model.get('count_step'),
        minCount = +this.model.get('min_count');

      // Если количество кратно шагу и больше минимального, можно добавлять в корзину
      var _q = (quantity - minCount).toFixed(3);
      var isValid = _q >= 0 && (_q % step) < 0.0001;

      var cartItem = this.cart.createItem({
        product: this.model.attributes,
        product_id: this.model.get('id'),
        quantity: this.quantityWidget.model.get('quantity')
      });

      if (isValid) {
        cartItem
          .save()
          .done(function(){
            cart.items.set(cartItem, {
              remove: false
            });
            cart.trigger('item:add', cartItem, cartItem.quantity);
          });
      } else {
        // Если количество не валидно, говорим об этом пользователю
        cart.trigger('item:invalid', cartItem, this.model);
      }
    },
    onCartItemAdd: function () {
      this.quantityWidget.model.set({ quantity: this.model.get('min_count') });
    }
  });
});