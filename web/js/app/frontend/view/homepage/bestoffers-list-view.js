define(function(require){
  var Backbone = require('backbone'),
      ListView = require('view/base/list-view'),
      BestOffersListItemView = require('view/homepage/bestoffers-list-item-view')
  ;

  return ListView.extend({
    itemView: BestOffersListItemView,
    initialize: function (options) {
      this._options = _.extend({
        useNarrowCards: false
      }, options);

      this.cart = options.cart;
      ListView.prototype.initialize.apply(this, arguments);
    },
    _createItemView: function(item, index){
      return new this.itemView({ model: item, index: index, cart: this.cart, isNarrow: this._options.useNarrowCards });
    },
    render: function () {
      ListView.prototype.render.apply(this, arguments);
      return this;
    },
  })
})