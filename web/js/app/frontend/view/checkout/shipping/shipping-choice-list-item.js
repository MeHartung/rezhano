/* 
 *  @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
define(function(require){
  var Backbone = require('backbone');
  
  var template = _.template(require('templates/checkout/shipping/courier-shipping-choice-list-item'))
  
  return Backbone.View.extend({
    tagName: 'li',
    className: 'shipping-method-choice',
    initialize: function(){
      this.listenTo(this, 'attach', this._onAttached, this);
      this.listenTo(this.model, 'change', this.render, this);
    },
    render: function(){
      this.$el.html(template({
        uid: this.model.get('id'),
        name: this.model.get('name'),
        duration: this.model.get('duration'),        
        durationString: this.model.get('durationString'),
        cost: this.model.get('costString'),
        selected: this.model.get('selected'),
        help: this.model.get('help')
      }));
      
      return this;
    },
    dispose: function(){
      this.stopListening();
      this.$el.remove();
    },
    _onAttached: function(){
      this.render();
    }
  })
})

