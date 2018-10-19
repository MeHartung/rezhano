define(function(require){
  var Backbone = require('backbone'),
    DocumentItem = require('model/user/document-item');

  var DocumentLoadCollection = Backbone.Collection.extend({
    url: urlPrefix + '/registration',
    model: DocumentItem
  });

  DocumentLoadCollection.fromCache = function(cache){
    var items = new Array();

    _.each(cache, function(atts){
      items.push(_.extend({}, atts, { id: atts.id}));
    });

    return new DocumentLoadCollection(items);
  };

  return DocumentLoadCollection;
});
