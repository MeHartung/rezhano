/**
 * Created by Денис on 27.06.2017.
 */
define(function(require){
  var Product = require('model/catalog/product/product'),
      CommonView = require('view/common/common-view'),
      Buy1ClickButton = require('view/checkout/1click/1click-order-button'),
      PreorderButton = require('view/checkout/preorder/preorder-button'),
      QuantityWidget = require('view/cart/widget/quantity-widget'),
      AskQuestionLinkView = require('view/catalog/product/ask-question-link-view'),
      ImageGalleryView = require('view/catalog/product/product-image-gallery-view');

  require('slick');
  require('jquery-ui/widgets/tabs');

  return CommonView.extend({
    events: {
      'click .nn_tabs_tab': 'onTabHeaderClick',
      'click .button-buy_in-product': 'onAddToCartButtonClick'
    },
    initialize: function(options) {
      CommonView.prototype.initialize.apply(this, arguments);

      this.model = new Product(ObjectCache.Product || {});

      this.cartItem = this.cart.createItem({
        product_id: this.model.get('id')
      });

      this.quantityWidget = new QuantityWidget({
        model: this.cartItem,
        max: this.model.get('available_stock')
      });

      this.imageGalleryView = new ImageGalleryView();
    },
    render: function(){
      CommonView.prototype.render.apply(this, arguments);

      this.$('#product-description-tabs').tabs();

      // if (this.$('.buy-one-product').length){
      //   new Buy1ClickButton({
      //     el: this.$('.buy-one-product')
      //   });
      // }
      //
      // if (this.$('.preorder-product').length){
      //   new PreorderButton({
      //     el: this.$('.preorder-product')
      //   });
      // }

      if (this.$('.quantity-widget').length){
        this.quantityWidget.setElement($('.quantity-widget'));
        this.quantityWidget.render();
      }


      if (this.$('.product-image__gallery').length){
        this.imageGalleryView.setElement(this.$('#product-image-gallery'));
        this.imageGalleryView.render();
      }

      var $askQuestionLinkView = this.$('a.ask-a-question');
      if ($askQuestionLinkView.length){
        this.askQuestionLinkView = new AskQuestionLinkView({
          product_id: $askQuestionLinkView.data('product'),
          el: $askQuestionLinkView
        });
        this.askQuestionLinkView.render();
      }

      return this;
    },
    onTabHeaderClick: function(e){
      e.preventDefault();

      this.openTab($(e.currentTarget));
    },
    openTab: function($el){
      var rel = $el.data('tab-id'),
        element = $el,
        container = $el.parents('.nn_tabs_container');

      element.siblings().removeClass('active');
      element.addClass('active');

      container.find('div.nn_tabs_item').removeClass('nn_tabs_item_active').addClass('nn_tabs_item_inactive');

      container.find('div.nn_tabs_item#tab-content-'+rel).removeClass('nn_tabs_item_inactive').addClass('nn_tabs_item_active');
    },
    onAddToCartButtonClick: function(e){
      e.preventDefault();

      var self = this,
          cart = this.cart,
          cartItem = this.cart.createItem(_.pick(this.cartItem.attributes, ['product_id', 'quantity']));

      cartItem
        .save()
        .done(function(){
          cart.items.set(cartItem, {
            remove: false
          });

          self.onCartItemAdded(cartItem, self.cartItem.get('quantity'));

          self.cartItem.set({ quantity: 1 });
        });
    }
  })
})