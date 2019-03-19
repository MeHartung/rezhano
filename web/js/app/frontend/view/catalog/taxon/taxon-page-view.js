define(function(require){
  var Backbone = require('backbone'),
      CommonView = require('view/common/common-view'),
      ProductCollection = require('model/catalog/product/product-collection'),
      ProductListView = require('view/catalog/taxon/product-list-view'),
      ProductFilterView = require('view/catalog/filter/catalog-filter-view'),
      ProductPagerView = require('view/catalog/taxon/product-pager-view'),
      ProductPerPageSelectView = require('view/catalog/taxon/product-per-page-counter-view'),
      ProductSortPanelView = require('view/catalog/taxon/product-sort-panel-view'),

      Filter = require('model/catalog/filter/filter');

  return CommonView.extend({
    events: {
      'click .button-question': 'onQuestionClick',
      'click .footer-maps__link' : 'onAddressClick',
      'click .cmn-toggle-switch' : 'onShowMobileMenu',
      'click .cmn-toggle-switch__close' : 'onHideMobileMenu',
    },
    initialize: function(options){
      var self = this;
      CommonView.prototype.initialize.apply(this, arguments);

      this.filter = new Filter(ObjectCache.Filter);
      this.products = ProductCollection.fromCache(ObjectCache.ProductList);
      this.listenTo(this.filter, 'filtered', function(data){
        self.products.set(data.products);
      });
      this.productListView = new ProductListView({
        cart: options.cart,
        collection: this.products
      });

      this.productFilterView = new ProductFilterView({
        model: this.filter,
        collection: this.products
      });

      this.productPagerView = new ProductPagerView({
        model: this.filter.getPager()
      });

      this.productPerPageSelectView = new ProductPerPageSelectView({
        model: this.filter
      });
      this.productSortPanelView = new ProductSortPanelView({
        filter: this.filter
      })
    },
    render: function(){
      CommonView.prototype.render.apply(this, arguments);
      this.productListView.setElement(this.$('#product-listview')).render();
      this.productFilterView.setElement(this.$('.product-filter-group')).render();
      this.productPagerView.setElement(this.$('.pagination')).render();
      this.productPerPageSelectView.setElement(this.$('.display-number')).render();
      this.productSortPanelView.setElement(this.$('.product-list-sort')).render();

      return this;
    }

  });
});