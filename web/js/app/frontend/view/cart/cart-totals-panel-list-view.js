define(function(require){
  var ListView = require('view/base/list-view'),
      CartTotalsPanelItemView = require('view/cart/cart-totals-panel-list-item-view'),
      $ = require('jquery');

  return ListView.extend({
    itemView: CartTotalsPanelItemView,
    initialize: function(options){
      this.options = options;

      ListView.prototype.initialize.apply(this, arguments);
    },
    _createItemView: function(item, index){
      return new this.itemView({ model: item, index: index, cart: this.options.cart });
    },
    render: function(){
      ListView.prototype.render.apply(this, arguments);

      return this;
    }
  });
});