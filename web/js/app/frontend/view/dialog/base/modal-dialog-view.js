/* 
 *  @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
define(function(require){
  var DialogView = require('view/dialog/base/dialog-view'),
      windowManager = require('view/dialog/base/window-manager'),
      $ = require('jquery');

  /**
   * Представление модального диалогового окна
   */
  return DialogView.extend({
    initialize: function(options){
      var _opts = $.extend({}, {
        position: 'center_screen'
      }, options);

      DialogView.prototype.initialize.call(this, _opts);

      var self = this;
      
      this.isVisible = this.isOpened = false;
      this.$el.on('hide.modaldialog', function(e){
        if (self.isVisible){
          e.preventDefault();
          e.isModal = true;
        }
      });  
      
      this.$overlay = $('<div class="ui-widget-overlay"></div>').hide();
      this.$overlay.on('click.modaldialog'+this.cid, function(){
        self.close();
      });
      
      $('body').append(this.$overlay);


      $(window).on('scroll.modaldialog'+this.cid, $.proxy(this.onWindowScroll, this));
    },
    show: function(){
      this.$overlay.show();
      this.$el.show();

      this.isVisible = this.isOpened = true;
    },
    open: function()
    {
      windowManager.push(this);

      this._updateZIndex();
      this._restorePosition();

      this.show();
    },            
    close: function()
    {
      windowManager.remove(this);

      this.hide();
    },
    hide: function(){
      this.$el.hide();
      this.$overlay.hide();

      this.isVisible = this.isOpened = false;
    },
    dispose: function(){
      this.$el.off('hide.modaldialog'+this.cid);
      this.$overlay.off('click.modaldialog'+this.cid);

      $(document).off('keydown.modaldialog'+this.cid);

      this.$overlay.remove();

      DialogView.prototype.dispose.apply(this, arguments);
    },
    onCloseButtonClick: function(e){
      e.preventDefault();
      
      this.close();
    },
    onWindowScroll: function(e){
      /* Я собирался не давать проматывать страницу при открытом модальном диалоге, но в итоге решил отказаться от этого,
       * так как диалог может не помещаться на экран устройства, и в этом случае пользователю нужно его проматывать, чтобы
       * увидеть все содержимое диалогового окна
       */
    },
    _updateZIndex: function(){
      DialogView.prototype._updateZIndex.apply(this, arguments);
      this.$overlay.css({ zIndex: 1000 + windowManager.windowStack.length*2 - 1});
    }
  });
  
  
});

