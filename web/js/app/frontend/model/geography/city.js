/**
 * Created by Dancy on 15.09.2017.
 */
define(function(require){
  var Backbone = require('backbone');

  return Backbone.Model.extend({
    idAttribute: 'code',
    defaults: {
      name: null,
      code: null
    }
  });
});