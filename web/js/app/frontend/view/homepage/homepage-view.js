define(function(require){
  var Backbone = require('backbone'),
      BestOffersView = require('view/homepage/bestoffers-view'),
      LastVisitedPanelView = require('view/homepage/last-viewed-panel'),
      ProductCollection = require('model/catalog/product/product-collection'),
      CommonView = require('view/common/common-view');

  return CommonView.extend({
    initialize: function () {
      CommonView.prototype.initialize.apply(this, arguments);
      this.bestOffers = new ProductCollection(ObjectCache.BestOffers || []);
//      this.lastVisitedProducts = new ProductCollection(ObjectCache.ViewedProducts || []);

      this.bestOffersView = new BestOffersView({
        collection: this.bestOffers,
        cart: this.cart
      });
      // this.lastVisitedView = new LastVisitedPanelView({
      //   collection: this.lastVisitedProducts,
      //   cart: this.cart,
      // });
    },
    render: function (){
      var self = this;

      CommonView.prototype.render.apply(this, arguments);

      this.bestOffersView.setElement(this.$('.popular-products'));
      this.bestOffersView.render();

      // this.lastVisitedView.setElement(this.$('.last-viewed-panel'));
      // this.lastVisitedView.render();
      //
      $(function () {
        self.$('.header-slider__wrap').slick({
          autoplay: true,
          autoplaySpeed: 5000,
          infinite: true,
          fade: true,
          cssEase: 'linear',
          dots: true,
          arrows: false
        });
        //
        self.$('.notes-slider').slick({
          dots: true,
          arrows: true
        });

        // var $headerMenu = $('header');
        // var headerMenuHeight = $('header').height();
        // var headerSectionHeight = $('.header-section').height() - headerMenuHeight;
        //
        // $(window).scroll(function () {
        //   if ( $(this).scrollTop()>headerMenuHeight-40 ) {
        //     $headerMenu.addClass('fixed');
        //
        //   } else if ( $(this).scrollTop()<headerMenuHeight) {
        //     $headerMenu.removeClass('fixed');
        //   }
        // });
      });

      return this;
    }
  })
});