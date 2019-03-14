define(function(require){
   var ModalDialog = require('view/dialog/base/modal-dialog-view'),
       ProductQuickView = require('view/catalog/product/product-quick-view');

   return ModalDialog.extend({
      className: 'layer quick-view',
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
      },
      show: function(){
        this.$overlay.stop().fadeIn();
        this.$el.stop().fadeIn();
        $('body').css({
          overflow: 'hidden'
        });
        if ( $('html').hasClass('mobile') ||$('html').hasClass('tablet') ) {
          window.location.hash = "modal";
        }
      },
      close: function () {
         ModalDialog.prototype.close.apply(this, arguments);

        $('body').css({
          overflow: 'auto'
        });
      }
   });
});