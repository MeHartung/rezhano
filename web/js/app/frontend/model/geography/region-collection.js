/**
 * Created by Dancy on 15.09.2017.
 */
define(function(require){
  var Backbone = require('backbone'),
      Region = require('model/geography/region');

  return Backbone.Collection.extend({
    model: Region
  });
});