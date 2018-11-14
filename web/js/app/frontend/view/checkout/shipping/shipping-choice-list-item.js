/* 
 *  @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
define(function(require){
  var Backbone = require('backbone'),
      ListItemView = require('view/base/list-item-view'),
    MapViewDialog = require('view/common/map-view-dialog');;
  
  var template = _.template(require('templates/checkout/shipping/shipping-choice-list-item'));
  
  return ListItemView.extend({
    className: 'custom-radio',
    events: {
      'change input.radio' : 'onShippingMethodChange',
      'click .delivery-address' : 'onAddressClick'
    },
    initialize: function(){
      this.listenTo(this, 'attach', this._onAttached, this);
      this.listenTo(this.model, 'change', this.render, this);
      this.mapViewDialog = null;
    },
    render: function(){
      this.$el.html(template({
        id: this.model.get('id'),
        name: this.model.get('name'),
        checked: this.model.get('is_active'),
        cost: this.model.get('cost'),
        help: this.model.get('help'),
        show_address: this.model.get('show_address'),
        address: this.model.get('address'),
        recipient_address: this.model.get('options').recipient_address_required
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
    },
    onAddressClick: function (e) {
      e.preventDefault();

      this.mapViewDialog = new MapViewDialog({
        model: new Backbone.Model({
          address: $(e.currentTarget).attr('data-address')
        }),
      });
      this.mapViewDialog.render().$el.appendTo($('body'));

      this.mapViewDialog.open();
    },
  })
});

