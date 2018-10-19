define(function(require){
  var Backbone = require('backbone'),
      BestOffersListView = require('view/homepage/bestoffers-list-view');

  return Backbone.View.extend({
    initialize: function (options) {
      this.cart = options.cart;
      this.listView = new BestOffersListView({
        collection: this.collection,
        cart: this.cart
      });
    },
    render: function () {

      this.listView.setElement(this.$('.section-wrap'));
      this.listView.render();

      return this;
    },
  })
})