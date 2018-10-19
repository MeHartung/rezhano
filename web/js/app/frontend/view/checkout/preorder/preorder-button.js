define(function(require){
  var Backbone = require('backbone'),
      PreorderDialogView = require('view/checkout/preorder/preorder-checkout-dialog'),
      Product = require('model/catalog/product/product');

  var template = _.template('Предзаказ');

  return Backbone.View.extend({
    className: 'preorder-product button',
    events: {
      'click': 'onClick'
    },
    initialize: function(){
      this.preorderCheckoutDialog = null;
    },
    render: function(){
      this.$el.html(template());

      return this;
    },
    onClick: function(e){
      var self = this;

      // if (this.preorderCheckoutDialog){
      //   this.preorderCheckoutDialog.dispose();
      // }

      var product = new Product({
        slug: $(e.currentTarget).data('product-slug')
      });

      product.fetch()
        .done(function(){
          self.preorderCheckoutDialog = new PreorderDialogView({
            model: product
          });

          self.preorderCheckoutDialog.render();
          $('body').append(self.preorderCheckoutDialog.$el);
          self.preorderCheckoutDialog.open();
        })
    }
  });
});