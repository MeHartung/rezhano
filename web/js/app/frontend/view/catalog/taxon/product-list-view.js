/**
 * Created by Денис on 23.05.2017.
 */

define(function(require){
  var ListView = require('view/base/list-view'),
      ProductListItemGridView = require('view/catalog/taxon/product-list-item-grid'),
      $ = require('jquery');

  return ListView.extend({
    itemView: ProductListItemGridView,
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