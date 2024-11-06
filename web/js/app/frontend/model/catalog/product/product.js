/**
 * Created by Денис on 07.06.2017.
 */
define(function(require){
  var Backbone = require('backbone');

  return Backbone.Model.extend({
    idAttribute: 'slug',
    urlRoot: urlPrefix + '/api/products'
  });
});