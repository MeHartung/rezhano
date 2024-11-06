/**
 * Created by Денис on 11.04.2017.
 */
define(function(require){
  var $ = require('jquery');

  var WindowManager = function(){
    this.windowStack = [];
  }
  WindowManager.prototype.push = function(view){
    this.windowStack.push(view);
  }
  WindowManager.prototype.getTopMostWindow = function(){
    return this.windowStack.length ? this.windowStack[this.windowStack.length - 1] : null;
  }
  WindowManager.prototype.remove = function(view){
    var idx = null;
    for (var i = 0; i < this.windowStack.length; i++){
      if (this.windowStack[i].cid === view.cid){
        idx = i; break;
      }
    }

    if (null !== idx){
      this.windowStack.splice(idx, 1);
    }
  }

  windowManager = new WindowManager();

  $(function(){
    $(document).on('keydown.windowmanager', function(e){
      if (e.which == 27 /* Клавиша ESC */){
        var topMostWindow = windowManager.getTopMostWindow();
        if (topMostWindow){
          topMostWindow.close();
        }
      }
    });
  })

  return windowManager;
})