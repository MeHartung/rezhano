/* 
 * @author Denis N. Ragozin <ragozin at artsofte.ru>
 * @version SVN: $Id$
 * @revision SVN: $Revision$ 
 */
define(function(require){
  var Backbone = require('backbone');
  
  var template = _.template('\
<span class="grey"><%= address %>, <%= name %></span>\n\
');
  

  return Backbone.View.extend({
    tagName: 'li',
    className: 'radio',
    events: 
    {
      'change input' : 'onChange'
    },
    initialize: function(){
      this.render();
      this.model.on('change', this.render, this);
    },
    render: function(){
      this.$el.html(template(this.model.toJSON()));
    },
    onChange: function(event){
      var el = $(event.currentTarget);
      // Вызываем событие только если выбран данный элемент
      if(el.is(':checked'))  
        this.trigger('item:selected', this, this.model);
        
    },
    dispose: function(){
      this.undelegateEvents();      
      this.stopListening();
      this.remove();
    }        
  })
})

