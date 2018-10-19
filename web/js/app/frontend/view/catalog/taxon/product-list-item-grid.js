define(function(require){
  var ListItemView = require('view/base/list-item-view'),
      //Buy1ClickButton = require('view/checkout/1click/1click-order-button'),
      User = require('model/user/user');//,
      //PreorderButton = require('view/checkout/preorder/preorder-button');

  require('lib/string');

  var template = require('templates/catalog/product/product-list-item-grid-tpl');

  return ListItemView.extend({
    className: 'product-item',
    events: {
      'click .button_add-to-cart': 'onAddToCartButtonClick'
    },
    initialize: function(options){
      ListItemView.prototype.initialize.apply(this, arguments);

      this.options = _.extend({
        isNarrow: false
      }, options);

      // this.buy1clickButton = new Buy1ClickButton();
      // this.preorderButton = new PreorderButton();
      this.cart = options.cart;
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
        isSale: this.model.get('isSale'),
        isNovice: this.model.get('isNovice'),
        isHit: this.model.get('isHit'),
        isPurchasable: this.model.get('isPurchasable'),
        isPreorder: this.model.get('isPreorder'),
        isUserAuth: isUserAuth
      }));

      if (this.model.get('isPurchasable')){
        this.$el.removeClass('product-unavailable');
        // if (this.model.get('isPreorder')) {
        //   this.preorderButton.setElement(this.$('.preorder')).render();
        // } else {
        //   this.buy1clickButton.setElement(this.$('.buy-one')).render();
        // }
      } else {
        this.$el.addClass('product-unavailable');
      }

      if (this.options.isNarrow){
        this.$el.addClass('product-item_narrow');
      } else {
        this.$el.removeClass('product-item_narrow');
      }

      return this;
    },
    onAddToCartButtonClick: function(e){
      e.preventDefault();

      var self = this,
        productId = $(e.currentTarget).data('product-id'),
        cart = this.cart;

      var cartItem = this.cart.createItem({
        product_id: productId
      });

      cartItem
        .save()
        .done(function(){
          cart.items.set(cartItem, {
            remove: false
          });
          cart.trigger('item:add', cartItem, 1);
        });
    }
  })
})
