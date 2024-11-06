/* 
 * @author Denis N. Ragozin <ragozin at artsofte.ru>
 * @version SVN: $Id$
 * @revision SVN: $Revision$
 */
 define(function(require){
    var Backbone = require('backbone'), 
        Product = require('model/catalog/product/product');
        
    /**
     * Конструктор коллекции ProductCollection
     * 
     * @constructor
     */
    var ProductCollection = Backbone.Collection.extend({
      model: Product    
    });

    /**
     * Создает экземпляр коллекции товаров из кеша
     * 
     * @param {ProductCache} cache Product cache
     */
    ProductCollection.fromCache = function(cache){
      var items = new Array();
      
      _.each(cache, function(atts){
        items.push(_.extend({}, atts, { id: atts.id}));
      });
      
      return new ProductCollection(items);
    };
     
    return ProductCollection;
  });