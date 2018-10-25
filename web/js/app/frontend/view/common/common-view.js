/**
 * Created by Денис on 06.06.2017.
 */
define(function(require){
  var Backbone = require('backbone'),
      $ = require('jquery'),
      AddToCartSuccessLayerView = require('view/cart/add-to-cart-success-layer-view'),
      CartWidgetView = require('view/cart/widget'),
      CatalogSearchFormView = require('view/catalog/search/catalog-search-form-view'),
      //CitySelectLinkView = require('view/common/header/city-select-link'),
      UserPanelView = require('view/user/user-panel-view')//,
      //Location = require('model/geography/location')
      ;

  return Backbone.View.extend({
    initialize: function(options){
      this.options = $.extend({
        cartWidget: true,
        search: false
      }, options);

      this.cart = options.cart;
      this.user = options.user;

      this.addToCartSuccessLayer = null;

      if (this.options.cartWidget){
        this.cartWidget = new CartWidgetView({
          model: this.cart,
          el: $('.header-menu__info')
        });
      }

      // this.location = new Location(ObjectCache.Location);

      // this.citySelectLinkView = new CitySelectLinkView({
      //   model: this.location
      // });

      this.userPanelView = new UserPanelView({
        model: this.user
      });

      this.listenTo(this.cart, 'item:add', this.onCartItemAdded);
    },
    onCartItemAdded: function(model, quantity){
      if (this.addToCartSuccessLayer){
        this.addToCartSuccessLayer.dispose();
        this.addToCartSuccessLayer = null;
      }

      this.addToCartSuccessLayer = new AddToCartSuccessLayerView({
        model: model,
        quantity: quantity
      });
      this.addToCartSuccessLayer.$el.appendTo($('body'));
      this.addToCartSuccessLayer.render();
      this.addToCartSuccessLayer.open();

      window.dataLayer = window.dataLayer || [];

      window.dataLayer.push({
        'event': 'addToCart',
        ecommerce: {
          currencyCode: 'RUB',
          add: {
            products: [model.toGaJson({
              quantity: quantity
            })]
          }
        }
      });
    },
    render: function(){
      var self = this;

      if (this.options.cartWidget) {
        this.cartWidget.render();
      }

      if (this.options.search) {
        this.searchFormView = new CatalogSearchFormView({
          el: this.$('.header-menu__input')
        });
      }
      // this.citySelectLinkView.setElement(this.$('#cityselection')).render();

      this.userPanelView.setElement(this.$('.header-panel__sign')).render();

      _.forEach(this.$('.member-club'), function (item) {
        new UserPanelView({
          el: $(item),
          dialog: self.userPanelView
        }).render();
      });

      return this;
    }
  });
});