(function( factory ) {
  if ( typeof define === "function" && define.amd ) {

    // AMD. Register as an anonymous module.
    define([ "jquery", "jquery-ui"  ], factory );
  } else {

    // Browser globals
    factory( jQuery );
  }
}(function($, ui, undefined){
  $.widget("ui.checkbox", {
    options:
    {
      divClass : "checkbox"
    },
    _create: function()
    {
      var self = this;
      var div = $("<div class=\""+self.options.divClass+"\">&nbsp;</div>");
      
      if( self.element.is(':checked') )
        div.addClass( "checked" )
      
      if( self.element.attr("disabled") )
        div.addClass("disabled")
        

      // Пре стилизация
      div.css({"width":self.element.outerWidth()+"px", "height":self.element.outerHeight()+"px" });
      
      

      // Добавление
      div.insertAfter( self.element );

      div.bind("click.checkbox",function(e){
        if( $(this).hasClass("disabled") )
          return;
        self.element.trigger("click");
        e.preventDefault()
      })
      
      self.element.on("change.checkbox", function(e){
        if( self.element.is(':checked') )
          div.addClass( "checked" )
        else
          div.removeClass( "checked" )
        if( self.element.attr("disabled") )
          div.addClass("disabled")
        else
          div.removeClass("disabled")
        e.preventDefault();
      })

      self.div = div;
      self.element.hide();
    },
    destroy: function()
    {
      var self = this;
      
      self.element.show();
      self.element.off(".checkbox");      
      self.div.remove();
      
      $.Widget.prototype.destroy.call( this );
    },
    
    disable: function()
    {
      var self = this;
      self.div.addClass("disabled");
      self.element.attr("disabled", "disabled");
    },
    
    enable: function()
    {
      var self = this;
      self.div.removeClass("disabled");
      self.element.removeAttr("disabled");
      
    },
            
    removeChecked: function()
    {
      var self = this;
      self.div.removeClass("checked");
      self.element.removeAttr("checked");
    }        
    
    
    
  });
}));