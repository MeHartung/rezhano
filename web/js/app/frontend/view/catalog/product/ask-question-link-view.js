/**
 * Created by Денис on 24.08.2017.
 */
define(function(require){
  var Backbone = require('backbone'),
      AskQuestionDialog = require('view/catalog/product/ask-question-dialog-view'),
      Product = require('model/catalog/product/product');

  return Backbone.View.extend({
    events: {
      'click': 'onClick'
    },
    initialize: function(){
      var product = new Product({
        slug: this.$el.data('product')
      });

      product.fetch();

      this.askQuestionDialog = new AskQuestionDialog({
        product: product
      });

      this.askQuestionDialog.$el.appendTo($('body'));
    },
    onClick: function(e){
      e.preventDefault();

      this.askQuestionDialog.render();
      this.askQuestionDialog.open();
    }
  })
})