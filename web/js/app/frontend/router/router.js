/*
 * Copyright (c) 2017. Denis N. Ragozin <dragozin@accurateweb.ru>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

define(function(require){
  var Backbone = require('backbone'),
      TaxonPageView = require('view/catalog/taxon/taxon-page-view'),
      ProductPageView = require('view/catalog/product/product-page-view'),
      CartPageView = require('view/cart/cart-view'),
      CheckoutPageView = require('view/checkout/checkout-page-view'),
      RegisterView = require('view/user/registration/registration-page-view'),
      LoginView = require('view/user/login/login-page-view'),
      ProfileView = require('view/user/user-profile-view'),
      ProfileEditView = require('view/user/user-profile-edit-view'),
      ProfileHistoryView = require('view/user/user-profile-history-view'),
      HomepageView = require('view/homepage/homepage-view'),
      CommonView = require('view/common/common-view'),
      ContactPageView = require('view/contact/contact-page-view'),
      PasswordResetView = require('view/user/password-reset/password-reset-view'),
      CheckoutDeliveryStepView = require('view/checkout/delivery/checkout-delivery-step-page-view'),
      Cart = require('model/cart/cart'),
      User = require('model/user/user'),
      jQuery = require('jquery');


  return Backbone.Router.extend({
    defaultPageAction: function(View){
      var cart = new Cart(ObjectCache.Cart || {}),
          user = new User(ObjectCache.User || {});

      var view = new View({
        cart: cart,
        user: user,
        el: jQuery('body'),
      });

      jQuery(function() {
        view.render();
      });
    },
    productListAction: function(){
      this.defaultPageAction(TaxonPageView);
    },
    productPageAction: function(){
      this.defaultPageAction(ProductPageView);
    },
    cartAction: function(){
      this.defaultPageAction(CartPageView);
    },
    checkoutAction: function(){
      this.defaultPageAction(CheckoutPageView);
    },
    contactPageAction: function(){
      this.defaultPageAction(ContactPageView);
    },
    commonAction: function(){
      this.defaultPageAction(CommonView)
    },
    registerAction:function () {
      this.defaultPageAction(RegisterView);
    },
    loginAction:function () {
      this.defaultPageAction(LoginView);
    },
    profileAction:function () {
      this.defaultPageAction(ProfileView);
    },
    profileEditAction:function () {
      this.defaultPageAction(ProfileEditView);
    },
    profileHistoryAction: function () {
      this.defaultPageAction(ProfileHistoryView);
    },
    homepageAction: function() {
      this.defaultPageAction(HomepageView);
    },
    passwordResetAction: function(){
      this.defaultPageAction(PasswordResetView)
    },
    checkoutDeliveryStepAction: function () {
      this.defaultPageAction(CheckoutDeliveryStepView)
    },
    initialize: function(){
      var routePrefix = urlPrefix || '';
      if (routePrefix.length && routePrefix[0] == '/'){
        routePrefix = routePrefix.substr(1);
      }
      
      if (routePrefix.length > 0 && routePrefix.slice(-1) != '/'){
        routePrefix += '/';
      }


      this.route(routePrefix + '*path', this.commonAction);

      this.route(routePrefix, this.homepageAction);
      if (routePrefix.length > 1){
        this.route(routePrefix.slice(0, -1), this.homepageAction);
      }

      this.route(routePrefix + 'catalog/:slug', this.productListAction);
      this.route(routePrefix + 'search', this.productListAction);
      this.route(routePrefix + 'products/:slug', this.productPageAction);
      this.route(routePrefix + 'cart', this.cartAction);
      this.route(routePrefix + 'checkout', this.checkoutAction);
      this.route(routePrefix + 'registration', this.registerAction);
      this.route(routePrefix + 'login', this.loginAction);
      this.route(routePrefix + 'cabinet/profile', this.profileAction);
      this.route(routePrefix + 'cabinet/profile/edit', this.profileEditAction);
      this.route(routePrefix + 'cabinet/orders', this.profileHistoryAction);
      this.route(routePrefix + 'passwordreset/reset/:token', this.passwordResetAction);
      this.route(routePrefix + 'checkout/delivery', this.checkoutDeliveryStepAction);
      this.route(routePrefix + 'contacts', this.contactPageAction);
    }
  });
});