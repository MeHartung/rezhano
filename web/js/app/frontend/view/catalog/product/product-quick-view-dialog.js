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
          this.prevUrl =  window.location.pathname;
      },
      render: function(){
        var self = this;
          ModalDialog.prototype.render.apply(this, arguments);

          this.contentView.setElement(this.$('.layer__container')).render();

        $(window).on('popstate', function (event) {
            self.close()
        });

          return this;
      },
      show: function(){
        this.$overlay.stop().fadeIn();
        this.$el.stop().fadeIn();
        $('body').css({
          overflow: 'hidden'
        });

        var productUrl = this.model.get('url');

        setTimeout(function () {
          Backbone.history.navigate(productUrl, {trigger:true});

        }, 100);
      },
      close: function () {
         ModalDialog.prototype.close.apply(this, arguments);
        $('body').css({
          overflow: 'auto'
        });
        Backbone.history.navigate(this.prevUrl, {trigger:true});
      }
   });
});