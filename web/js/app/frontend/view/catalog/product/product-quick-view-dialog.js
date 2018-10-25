define(function(require){
   var ModalDialog = require('view/dialog/base/modal-dialog-view'),
       ProductQuickView = require('view/catalog/product/product-quick-view');

   return ModalDialog.extend({
      initialize: function(options){
          ModalDialog.prototype.initialize.apply(this, arguments);

          this.contentView = new ProductQuickView(_.extend({}, options, {
              model: this.model
          }));
      },
      render: function(){
          ModalDialog.prototype.render.apply(this, arguments);

          this.contentView.setElement(this.$('.layer__container')).render();

          return this;
      }
   });
});