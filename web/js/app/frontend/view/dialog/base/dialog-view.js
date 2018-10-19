define(function(require){
  var Backbone = require('backbone'),
      windowManager = require('view/dialog/base/window-manager'),
      $ = require('jquery');

  var defaultTemplate = _.template('' +
    '    <div class="layer__close"></div>\n' +
    '    <div class="layer__container">\n' +
    '      <div class="layer__title"><%= title %></div>\n' +
    '      <%= content %>\n' +
    '    </div>\n');

  var DialogView = Backbone.View.extend({
    tagName: 'div',
    className: 'layer',
    attributes: {
      'style': 'display:none;'
    },
    events: {
      'click .layer__close': 'onCloseButtonClick'
    },    
    render: function(){
      this.$el.html(this.template({
        title: this.model.get('title'),
        content: this.model.get('content')
      }));
      
      return this;
    },
    initialize: function(options){
      if (options.template){
        this.template = options.template
      } else {
        this.template = defaultTemplate;
      }
      this.position = options.position || null;

      $(window).on('resize.dialog'+this.cid, $.proxy(this.onWindowResize, this));

      this.isOpened = false;
    },
    show: function(){
      this.$el.show();
    },
    open: function(){
      windowManager.push(this);

      this._updateZIndex();
      this._restorePosition();

      this.show();

      this.isOpened = true;
    },
    toggle: function(){
      if (!this.$el.is(":visible"))
        this.open();
      else
        this.close();
    },
    toggle_ext: function(link, align) {
      this.$el.css('top', ($(link).offset().top+$(link).height()+10)+'px');
      
      align = align || 'right';
      
      var left = 0;
      
      if (align == 'right')
        left = $(link).offset().left - (this.$el.width() - $(link).width());
      if (align == 'center')
        left = $(link).offset().left + (- this.$el.width() + $(link).width()) / 2;
      if (align == 'left')
        left = $(link).offset().left - 10; //10 для красоты =)
      
      this.$el.css('left', left +'px');
      
//      if ($.isFunction(hidePopups)){
//        if (this.is_popup)
//          this.$el.removeClass("popup");
//        hidePopups();
//        if (this.is_popup)
//          this.$el.addClass("popup");
//      }
      
      this.$el.toggle();
    },
    _center_coords: function() {
      var top = $(window).scrollTop() + Math.max(($(window).height() - this.$el.height())/2, 15); 
      var left = $(window).width()/2 - this.$el.width()/2;
      
      return [top, left];      
    },
    close: function(){
      windowManager.remove(this);

      this.hide();

      this.isOpened = false
    },
    hide: function(){
      this.$el.hide();
    },
    _restorePosition: function(){
      var coords = null;
      if (_.isArray(this.position)){
        coords = this.position;
      } else if (this.position === 'center_screen'){
        coords = this._center_coords();
      }

      if (null !== coords){
        this.$el.css("top", coords[0]+"px");
        this.$el.css("left", coords[1] + "px");
      }
    },
    _updateZIndex: function(){
      this.$el.css({ zIndex: 1000 + windowManager.windowStack.length*2 });
    },
    onCloseButtonClick: function(e){
      e.preventDefault();
          
      this.close();
    },
    onWindowResize: function(e){
      this._restorePosition();
    },
    dispose: function(){
      $(window).off('resize.dialog'+this.cid);

      this.stopListening();
      this.$el.remove();
    }
  });
  
  return DialogView;
});
