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
      ImageGalleryView = require('view/catalog/product/product-image-gallery-view'),
      MapViewDialog = require('view/common/map-view-dialog');

  require('slick');
  require('jquery-ui/widgets/tabs');
  require('jscrollpane');
  require('jquery.mousewheel');

  return CommonView.extend({
    events: {
      'click .nn_tabs_tab': 'onTabHeaderClick',
      'click .button-buy_in-product': 'onAddToCartButtonClick',
      // 'click .footer-maps__link': 'onShopClick',
    },
    initialize: function(options) {
      CommonView.prototype.initialize.apply(this, arguments);

      this.model = new Product(ObjectCache.Product || {});

      this.cartItem = this.cart.createItem({
        product_id: this.model.get('id'),
        quantity: this.model.get('min_count')
      });

      this.quantityWidget = new QuantityWidget({
        model: this.cartItem,
        max: this.model.get('available_stock'),
        min: this.model.get('min_count'),
        step: this.model.get('count_step')
      });

      this.mapViewDialog = null;

      this.imageGalleryView = new ImageGalleryView();
    },
    render: function(){
      var _self = this;
      CommonView.prototype.render.apply(this, arguments);

      this.$('#product-description-tabs').tabs();

      this.$('.layer__images .main-image').slick({
        dots: false,
        arrows: true,
        infinite: true
      });

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
        this.quantityWidget.setElement($('.product-item__controls'));
        this.quantityWidget.render();
      }


      if (this.$('.product-image__gallery').length){
        this.imageGalleryView.setElement(this.$('#product-image-gallery'));
        this.imageGalleryView.render();
      }

      _self.$('.scroll-pane').jScrollPane();

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

          self.cartItem.set({ quantity: self.model.get('min_count') });
        });
    },
    onShopClick: function (e) {
      e.preventDefault();

      var points = {
        0: {
          city: 'Екатеринбург',
          address: 'Екатеринбург, ул. Красноармейская, 68 (с 10:00 до 21:00)',
          coordinates: [56.830773, 60.618136]
        },
        1: {
          city: 'Реж',
          address: 'Реж, ул. Олега Кошевого, 16',
          coordinates: [57.345120, 61.344415]
        }
      };

      this.mapViewDialog = new MapViewDialog({
        model: new Backbone.Model(points[e.currentTarget.dataset.point]),
      });
      this.mapViewDialog.render().$el.appendTo($('body'));

      this.mapViewDialog.open();
    },
  })
})