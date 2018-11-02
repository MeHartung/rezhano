/* 
 *  @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
define(function(require){
  var Backbone = require('backbone'),
      ListItemView = require('view/base/list-item-view');
  
  var template = _.template(require('templates/checkout/shipping/shipping-choice-list-item'));
  
  return ListItemView.extend({
    className: 'custom-radio',
    events: {
      'change input.radio' : 'onShippingMethodChange'
    },
    initialize: function(){
      this.listenTo(this, 'attach', this._onAttached, this);
      this.listenTo(this.model, 'change', this.render, this);
    },
    render: function(){
      this.$el.html(template({
        id: this.model.get('id'),
        name: this.model.get('name'),
        checked: this.model.get('is_active'),
        cost: this.model.get('cost'),
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
    },
    onShippingMethodChange: function (e) {

      if ($(e.currentTarget).prop('checked')) {
        this.model.set({'is_active': true});
      }
    }
  })
});

