/**
 * Created by Денис on 23.05.2017.
 */


define(function(require){
  var Backbone = require('backbone');

  return Backbone.Model.extend({
    urlRoot: urlPrefix + '/cart/items',
    defaults: {
      quantity: 1
    },
    initialize: function(){
      this.on('change:quantity', this.updateCost, this);
    },
    updateCost: function(){
      this.set('cost', this.cost());
    },
    cost: function(){
      return this.get('price')*this.get('quantity');
    },
    toJSON: function(){
      var attributeList = ['id', 'quantity'];

      if (this.isNew()){
        attributeList.push('product_id');
      }
      return _.pick(this.attributes, attributeList);
    },
    toGaJson: function(options){
      var product = this.get('product');

      return _.extend({}, {
        'name': this.get('name'),
        'id': this.get('product_id'),
        'price': this.get('price'),
        'brand': product.brand,
        'category': product.taxon,
        'quantity': this.get('quantity')
      }, options)
    }
  });
});