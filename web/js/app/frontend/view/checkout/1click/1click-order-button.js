/**
 * Created by Денис on 07.06.2017.
 */
define(function(require){
  var Backbone = require('backbone'),
      Buy1ClickDialogView = require('view/checkout/1click/1click-checkout-dialog'),
      Product = require('model/catalog/product/product');

  var template = _.template('Купить в один клик');

  return Backbone.View.extend({
    className: 'buy-one button',
    events: {
      'click': 'onClick'
    },
    initialize: function(){
      this.oneClickCheckoutDialog = null;
    },
    render: function(){
      this.$el.html(template());

      return this;
    },
    onClick: function(e){
      var self = this;

      if (this.oneClickCheckoutDialog){
        this.oneClickCheckoutDialog.dispose();
      }

      var product = new Product({
        slug: $(e.currentTarget).data('product-slug')
      });

      product.fetch()
        .done(function(){
          self.oneClickCheckoutDialog = new Buy1ClickDialogView({
            model: product
          });

          self.oneClickCheckoutDialog.render();
          $('body').append(self.oneClickCheckoutDialog.$el);
          self.oneClickCheckoutDialog.open();

          window.dataLayer = window.dataLayer || [];
          window.dataLayer.push({
            'event': 'checkout',
            ecommerce: {
              checkout: {
                'actionField': { 'step': 1 },
                products: [{
                  'name': product.get('name'),
                  'id': product.get('product_id'),
                  'price': product.get('price'),
                  // 'brand': product.get('brand'),
                  // 'category': product.get('section'),
                  'quantity': 1
                }]
              }
            }
          });
        })
    }
  });
});