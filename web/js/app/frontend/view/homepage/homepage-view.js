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
      this.lastVisitedProducts = new ProductCollection(ObjectCache.ViewedProducts || []);

      this.bestOffersView = new BestOffersView({
        collection: this.bestOffers,
        cart: this.cart,
      });
      this.lastVisitedView = new LastVisitedPanelView({
        collection: this.lastVisitedProducts,
        cart: this.cart,
      });
    },
    render: function (){
      var self = this;

      CommonView.prototype.render.apply(this, arguments);

      this.bestOffersView.setElement(this.$('.best-offers'));
      this.bestOffersView.render();

      this.lastVisitedView.setElement(this.$('.last-viewed-panel'));
      this.lastVisitedView.render();

      $(function () {
        self.$('#sliderMain').slick({
          infinite: true,
          dots: true,
          arrows: true,
          autoplay: true,
          autoplayTime: 2000,
          slidesToShow: 2,
          slidesToScroll: 2
        });
      });

      return this;
    },
  })
});