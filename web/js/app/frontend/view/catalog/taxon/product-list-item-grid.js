define(function(require){
  var ListItemView = require('view/base/list-item-view'),
      //Buy1ClickButton = require('view/checkout/1click/1click-order-button'),
      User = require('model/user/user'),
      QuantityWidget = require('view/cart/widget/quantity-widget'),
      ProductQuickViewDialog = require('view/catalog/product/product-quick-view-dialog')
      ;//,
      //PreorderButton = require('view/checkout/preorder/preorder-button');

  require('lib/string');

  var template = require('templates/catalog/product/product-list-item-grid-tpl');

  return ListItemView.extend({
    className: 'product-item',
    events: {
      'click .button_add-to-cart': 'onAddToCartButtonClick',
      'click .product-page-link': 'onProductPageLinkClick'
    },
    initialize: function(options){
      ListItemView.prototype.initialize.apply(this, arguments);

      this.options = _.extend({
      }, options);

      // this.buy1clickButton = new Buy1ClickButton();
      // this.preorderButton = new PreorderButton();
      this.cart = options.cart;
      this.quantityWidget = new QuantityWidget({
        model: this.cart.createItem({
          product: this.model.attributes,
          productId: this.model.get('id'),
          quantity: this.model.get('min_count')
        }),
        min: this.model.get('min_count'),
        step: this.model.get('count_step'),
        units: this.model.get('units')
      });
      this.productQuickViewDialog = null;

      this.listenTo(this.cart, 'item:add', this.onCartItemAdd)
    },
    render: function(){
      var isUserAuth = User.getCurrentUser().isNew() == false;
      this.$el.html(template({
        id: this.model.get('id'),
        slug: this.model.get('slug'),
        name: this.model.get('name'),
        url: this.model.get('url'),
        image: this.model.get('image'),
        price: Number(this.model.get('price')).toCurrencyString('₽', 0),
        discountValue: this.model.get('isSale') ? Math.round((1 - this.model.get('price') / this.model.get('oldPrice')) * 100) : null,
        mountBg: this.model.get('background'),
        isSale: this.model.get('isSale'),
        isNovice: this.model.get('isNovice'),
        isHit: this.model.get('isHit'),
        isPurchasable: this.model.get('isPurchasable'),
        isPreorder: this.model.get('isPreorder'),
        isUserAuth: isUserAuth,
        package: this.model.get('package'),
        units: this.model.get('units'),
        type: this.model.get('type') ? this.model.get('type').name : '',
        short_description: this.model.get('short_description')
      }));
      if (this.model.get('isPurchasable')){
        this.$el.removeClass('product-unavailable');
        // if (this.model.get('isPreorder')) {
        //   this.preorderButton.setElement(this.$('.preorder')).render();
        // } else {
        //   this.buy1clickButton.setElement(this.$('.buy-one')).render();
        // }
          this.quantityWidget.setElement(this.$('.product-item__controls')).render();
      } else {
        this.$el.addClass('product-unavailable');
      }

      return this;
    },
    onAddToCartButtonClick: function(e){
      e.preventDefault();

      var self = this,
        productId = $(e.currentTarget).data('product-id'),
        cart = this.cart,
        quantity = +this.quantityWidget.model.get('quantity'),
        step = +this.model.get('count_step'),
        minCount = +this.model.get('min_count');

      // Если количество кратно шагу и больше минимального, можно добавлять в корзину
      var _q = (quantity - minCount).toFixed(3);
      var isValid = _q >= 0 && (_q % step) < 0.0001;
      //var isValid = Math.ceil(_q / step) - _q / step === 0 && quantity >= minCount;

      var cartItem = this.cart.createItem({
        product: this.model.attributes,
        product_id: productId,
        quantity: quantity
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
    onProductPageLinkClick: function(e){
      e.preventDefault();

      if (null === this.productQuickViewDialog){
        this.productQuickViewDialog = new ProductQuickViewDialog({
            model: this.model,
            cart: this.cart
        });
        this.productQuickViewDialog.render().$el.appendTo($('body'));
      }

      this.productQuickViewDialog.open();
    },
    onCartItemAdd: function () {
      this.quantityWidget.model.set({ quantity: this.model.get('min_count') });
    }
  })
});
