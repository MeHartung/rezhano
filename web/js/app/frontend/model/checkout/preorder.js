define(function(require){
  var Backbone = require('backbone');

  return Backbone.Model.extend({
    urlRoot: urlPrefix + '/checkout/preorder',
    defaults: {
      firstname: null,
      email: null,
      phone: null,
      product_slug: null
    }
  })
})