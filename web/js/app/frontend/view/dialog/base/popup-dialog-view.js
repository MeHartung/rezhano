/**
 * Created by Денис on 11.04.2017.
 */
define(function(require){
  var DialogView = require('view/dialog/base/dialog-view'),
      windowManager = require('view/dialog/base/window-manager');

  var PopupDialogView = DialogView.extend({
    className: 'layer popup',
    open: function(){
      $.each(windowManager.windowStack, function(){
        if (this instanceof PopupDialogView){
          this.close();
        }
      });
      DialogView.prototype.open.apply(this, arguments);
    },
    show: function() {
      this.$el.stop().fadeIn('fast');
    },
    hide: function(){
      this.$el.stop().hide();
    }
  });

  return PopupDialogView;
})