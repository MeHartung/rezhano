/* 
 * @author Max D. Selezenev <selezenev at artsofte.ru>
 * @version SVN: $Id$
 * @revision SVN: $Revision$ 
 */



define(function(require){
  var CitySelectorView = require('view/common/city-selector-view');
  
  var template = _.template('\
<input type="hidden" id="order_recipient_city" name="order[recipient_city]" value="<%= name %>" > \n\
<a class="city-selector-toggle dashed" href="#"><%= name %></a>\n\
');
  
  return CitySelectorView.extend({
    events: 
    {
      'click .city-selector-toggle' : 'onClick'
    },
    render: function(){
      this.$el.html(template({
        name: this.model.get('name') || 'Не выбран'
      }));
      
      return this;
    },            
//    openLayer: function(event){
//      if (this.layer.length)
//      {
//        this.layer.removeClass('popup')
//                .fadeIn(function() {
//          $(this).addClass('popup')
//        })
//                .position({of: window, my: 'center', at: 'center'});
//        
//        this.layer.find('#fias_city').focus();
//        
//
//      }
//      event.preventDefault();
//    }            
     adjustLayerPosition: function(){
//       this.layerView.$el.css('top', 0);
//       this.layerView.$el.css('left', 0);
       this.layerView.$el.position({of: $(window), my: 'center', at: 'center'});
     }
  });
});