/**
 * Created by Dancy on 20.09.2017.
 */
define(function(require){
  var Backbone = require('backbone');

  return Backbone.Model.extend({
    urlRoot: urlPrefix + '/api/articles',
    idAttribute: 'slug'
  });
});